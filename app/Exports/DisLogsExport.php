<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DisLogsExport implements FromQuery, WithMapping, WithHeadings
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
            $logs->unit_name,
            $logs->status_name,
            $logs->is_sent_sispek == 0 ? 'Not Sent' : 'Sent',
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
            'Unit',
            'Data Status',
            'SISPEK Status',
        ];
    }

    /**
    * @return \Illuminate\Support\Query
    */
    public function query(){
        return $this->query->orderBy("time_group", "desc");
    }
}
