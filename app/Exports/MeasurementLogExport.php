<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MeasurementLogExport implements FromQuery, WithMapping, WithHeadings
{
    use Exportable;
    protected $query;
    public function __construct($query){
        $this->query = $query;
    }

    public function map($logs):array{
        return [
            $logs->id,
            $logs->time_group,
            $logs->stack_name,
            $logs->parameter_name,
            $logs->value,
            $logs->value_correction,
            round($logs->records_total / 12  * 100, 2),
            $logs->unit_name,
            $logs->status_name,
        ];
    }

    public function headings():array{
        return [
            'ID',
            'Datetime',
            'Stack',
            'Parameter',
            'Measured',
            'Corrective',
            'Percentage',
            'Unit',
            'Data Status',
        ];
    }

    /**
    * @return \Illuminate\Support\Query
    */
    public function query(){
        return $this->query->orderBy("time_group", "desc")->orderBy("parameter_id");
    }
}
