<?php

namespace App\Imports;

use App\Models\Load;
use App\Models\Report;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\RemembersChunkOffset;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;

class ReportImport implements ToCollection, WithBatchInserts, WithChunkReading, WithEvents
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    use RemembersChunkOffset;

    protected int $duplicate;
    protected int $rows;
    protected int $added;
    protected string $status;

    protected array $columns;
    protected array $ruColumns;

    public string $fileName;

    public int $loadId;

    function __construct()
    {
        $this->duplicate = 0;
        $this->rows = 0;
        $this->added = 0;
        $this->status = 'processing';

        $this->columns = [
            'МФЦ, в котором зарегистрировано дело' => 'department',
            'Наименование услуги' => 'service_name',
            'Число услуг в деле' => 'services_count',
            'Дата регистрации' => 'registration_datetime',
            'Дата выдачи дела' => 'issue_datetime',
            'Наименование ОГВ, исполняющего услугу' => 'done_by',
            'Текущий статус услуги' => 'status',
        ];
        $this->ruColumns = [];

        $this->loadId = 0;
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function registerEvents(): array
    {
        return [
            AfterImport::class => function () {
                $load = Load::find($this->loadId);
                $load->rows = $this->rows;
                $load->status = $this->status;
                $load->added = $this->added;
                $load->duplicates = $this->duplicate;
                $load->save();
            }
        ];
    }

    public function collection(Collection $rows)
    {
        try {
            foreach ($rows as $index => $row) {
                if ($index == 0 && $this->getChunkOffset() == 1) {
                    foreach ($row as $title) {
                        if ($title === null) continue;
                        if (!array_key_exists($title, $this->columns)) {
                            if (!Schema::hasColumn('reports', Str::slug($title, '_'))) {
                                Schema::table('reports', function ($table) use ($title) {
                                    $table->string(Str::slug($title, '_'))->nullable();
                                });
                            }
                            array_push($this->ruColumns, $title);
                            $this->columns[$title] = Str::slug($title, '_');
                        } else {
                            array_push($this->ruColumns, $title);
                        }
                    }
                    continue;
                };
                $this->rows++;
                $tmpReport = [];
                foreach ($row as $i => $value) {
                    if ($value === null) continue;
                    $tmpReport[$this->columns[$this->ruColumns[$i]]] = $value;
                }

                $keysAndValues = [];
                foreach ($tmpReport as $key => $value) {
                    $keysAndValues[$key] = $value;
                }

                $report = Report::firstOrCreate($keysAndValues);
                (!$report->wasRecentlyCreated) ? $this->duplicate++ : $this->added++;
            }
            $this->status = "loaded";
        } catch (Exception $e) {
            Log::debug($e);
            $this->status = "crash";
        } finally {
            Storage::disk('public')->delete($this->fileName);
        }
    }
}
