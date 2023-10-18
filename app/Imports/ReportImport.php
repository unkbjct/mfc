<?php

namespace App\Imports;

use App\Models\Load;
use App\Models\Report;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
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

    protected int $loadId;

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
            BeforeImport::class => function (BeforeImport $event) {
                $load = new Load();
                $load->status = $this->status;
                $load->rows = $this->rows;
                $load->added = 0;
                $load->duplicates = 0;
                // $load->user_id = Auth::user()->id;
                $load->save();
                $this->loadId = $load->id;
            },

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
        // Log::debug('readed chunk');
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

                $report = new Report();
                foreach ($tmpReport as $key => $value) {
                    $report->$key = $value;
                }

                $repeat = Report::where('service_name', $tmpReport['service_name']);
                if (array_key_exists('department', $tmpReport)) $repeat = $repeat->where('department', $tmpReport['department']);
                if (array_key_exists('services_count', $tmpReport)) $repeat = $repeat->where('services_count', $tmpReport['services_count']);
                if (array_key_exists('registration_datetime', $tmpReport)) $repeat = $repeat->where('registration_datetime', $tmpReport['registration_datetime']);
                if (array_key_exists('issue_datetime', $tmpReport)) $repeat = $repeat->where('issue_datetime', $tmpReport['issue_datetime']);
                if (array_key_exists('done_by', $tmpReport)) $repeat = $repeat->where('done_by', $tmpReport['done_by']);
                if (array_key_exists('status', $tmpReport)) $repeat = $repeat->where('status', $tmpReport['status']);
                $repeat = $repeat->get();

                if ($repeat->isNotEmpty()) {
                    $this->duplicate++;
                    continue;
                };

                $this->added++;
                $report->save();
            }
            $this->status = "loaded";
        } catch (Exception $e) {
            $this->status = "crash";
        }
    }
}
