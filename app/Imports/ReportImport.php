<?php

namespace App\Imports;

use App\Models\Import;
use App\Models\Report;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;

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
            $report->save();
        }
    }
}
