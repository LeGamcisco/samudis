<?php

namespace App\Exports;

use App\Models\Configuration;
use App\Models\Measurement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MeasurementLogGovernmentExport implements FromQuery, WithMapping, WithHeadings
{
    use Exportable;
    protected $query;
    protected $tableName;
    public function __construct($query, $tableName){
        $this->query = $query;
        $this->tableName = $tableName;
    }

    public function map($logs):array{
        $time_group = Carbon::parse($logs->time_group)->format('d-m-Y');
        $startHour = Carbon::parse($logs->time_group)->subHour(1)->format('H:00');
        $endHour = Carbon::parse($logs->time_group)->format('H:00');
        $flowrate = DB::table($this->tableName)->whereRaw("parameter_id in (select parameter_id from parameters where stack_id = '$logs->stack_id' and sispek_code = 'laju_alir') and time_group = '$logs->time_group'")->avg("value_correction");
        $o2 = DB::table($this->tableName)->whereRaw("parameter_id in (select parameter_id from parameters where stack_id = '$logs->stack_id' and sispek_code = 'oksigen') and time_group = '$logs->time_group'")->avg("value_correction");
        return [
            "'$time_group",
            ("$startHour-$endHour"),
            $logs->value_correction,
            $flowrate,
            $o2,
        ];
    }

    public function headings():array{
        return [
            'TANGGAL',
            'JAM',
            'KONSENTRASI (MG/NM3)',
            'LAJU ALIR (M3/DETIK)',
            'OKSIGEN (%)',
        ];
    }

    /**
    * @return \Illuminate\Support\Query
    */
    public function query(){
        return $this->query->orderBy("time_group", "desc")->orderBy("parameter_id");
    }
}
