<?php

namespace App\Imports;

use App\Models\Import;
use App\Models\Report;
use Dotenv\Parser\Value;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Str;

class ReportImport implements ToCollection
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    public function collection(Collection $rows)
    {
        $columns = [
            'МФЦ, в котором зарегистрировано дело' => 'department',
            'Наименование услуги' => 'service_name',
            'Число услуг в деле' => 'services_count',
            'Дата регистрации' => 'registration_datetime',
            'Дата выдачи дела' => 'issue_datetime',
            'Наименование ОГВ, исполняющего услугу' => 'done_by',
            'Текущий статус услуги' => 'status',
        ];

        $tmpArray = [];
        foreach ($rows as $index => $row) {
            if ($index == 0) {
                foreach ($row as $title) {
                    if ($title === null) continue;
                    if (!array_key_exists($title, $columns) && !Schema::hasColumn('reports', Str::slug($title, '_'))) {
                        Schema::table('reports', function ($table) use ($title) {
                            $table->string(Str::slug($title, '_'))->nullable();
                        });
                    }
                    array_push($tmpArray, $title);
                }
                continue;
            };
            $tmpReport = [];
            foreach ($row as $i => $value) {
                if ($value === null) continue;
                $tmpReport[$columns[$tmpArray[$i]]] = $value;
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

            if ($repeat->isNotEmpty()) continue;

            $report->save();
        }
    }
}
