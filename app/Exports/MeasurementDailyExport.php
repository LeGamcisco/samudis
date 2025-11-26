<?php

namespace App\Exports;

use App\Models\Configuration;
use App\Models\Stack;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class MeasurementDailyExport implements FromView
{
    public $data; 
    public $stack; 
    public $monthYear; 
    public function __construct($data, $monthYear,$stackId){
        $this->data = $data;
        $this->monthYear = Carbon::parse($monthYear)->format("F Y");
        $this->stack = Stack::find($stackId);
    }
    /**
    * @return \Illuminate\Support\View
    */
    public function view(): View
    {
        $config = Configuration::find(1);
        return view('exports.daily-report',[
            'logs' => $this->data,
            'stack' => $this->stack,
            'monthYear' => $this->monthYear,
            'config' => $config,
        ]);
    }
}
