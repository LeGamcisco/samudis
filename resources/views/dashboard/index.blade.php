@extends('layouts.app')
@section('title','Dashboard')
@section('subtitle','Monitoring')

@section('header-option')
<div class="control-panel d-flex align-items-center gap-3">
  <div>
    <div class="form-check form-switch m-0">
      <input class="form-check-input" type="checkbox" id="showLineChart" checked="false" style="cursor: pointer;">
      <label class="form-check-label ms-2" for="showLineChart">LINE CHART</label>
    </div>
  </div>
  <div class="vr bg-secondary opacity-50"></div>
  <div class="btn-group">
    <button type="button" class="btn btn-outline-primary btn-sm fw-bold">{{ $stackActive->code }}</button>
    <button type="button"
      class="btn btn-outline-primary btn-sm dropdown-toggle dropdown-toggle-split"
      data-bs-toggle="dropdown"> <span class="visually-hidden">Toggle Dropdown</span>
    </button>
    <div class="dropdown-menu dropdown-menu-end shadow-lg border-0">
      @foreach ($stacks as $stack)
        <a class="dropdown-item fw-bold" href="{{ route("dashboard.realtime",[$stack->id]) }}" data-id="{{ $stack->id }}">{{ $stack->code }}</a>
      @endforeach
    </div>
  </div>
</div>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset("assets/plugins/datatable/css/dataTables.bootstrap5.min.css") }}">
<link rel="stylesheet" href="{{ asset("assets/plugins/select2/css/select2.min.css") }}">
<link rel="stylesheet" href="{{ asset("assets/plugins/select2/css/select2-bootstrap4.css") }}">
<link rel="stylesheet" href="{{ asset("assets/plugins/apexcharts-bundle/css/apexcharts.css") }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700;800&family=Oswald:wght@400;600&display=swap" rel="stylesheet">

<style>
    /* --- GLOBAL STYLES --- */
    .industrial-dashboard { font-family: 'Oswald', sans-serif; }

    /* --- CARD DESIGN UTAMA --- */
    .industrial-card {
        background: var(--bs-card-bg, #fff) !important; /* Paksa tetap warna tema */
        border: 1px solid var(--bs-border-color, #e5e7eb);
        border-radius: 12px;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        border-left: 5px solid var(--bs-border-color, #e5e7eb);
        color: var(--bs-body-color) !important; /* Reset text color */
    }
    
    .industrial-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }

    /* --- TRIK CSS UNTUK PILL STATUS (Tanpa Ubah JS) --- */
    /* Kita gunakan ::after pada card untuk membuat Badge Pill secara otomatis 
       berdasarkan class yang ditempel oleh JS (bg-dark/danger/success) */

    .industrial-card::after {
        content: ''; /* Default kosong */
        position: absolute;
        top: 15px;
        right: 15px;
        font-family: 'Oswald', sans-serif;
        font-size: 0.75rem;
        font-weight: bold;
        padding: 4px 12px;
        border-radius: 50px;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        z-index: 10;
        display: none; /* Default hidden */
    }

    /* 1. KONDISI OFFLINE (Saat JS tambah class .bg-dark) */
    .industrial-card.bg-dark {
        border-left-color: #374151 !important;
    }
    .industrial-card.bg-dark::after {
        display: block;
        content: 'OFFLINE';
        background-color: #374151; /* Abu Gelap */
        color: #fff;
    }
    /* Reset text color didalam card */
    .industrial-card.bg-dark h5, .industrial-card.bg-dark span, .industrial-card.bg-dark small {
        color: var(--bs-body-color) !important;
    }


    /* 2. KONDISI HIGH ALERT (Saat JS tambah class .bg-danger) */
    .industrial-card.bg-danger {
        border-left-color: #dc2626 !important;
    }
    .industrial-card.bg-danger::after {
        display: block;
        content: 'HIGH ALERT';
        background-color: #dc2626; /* Merah */
        color: #fff;
        animation: pulse-red 2s infinite;
    }
    .industrial-card.bg-danger h5, .industrial-card.bg-danger span, .industrial-card.bg-danger small {
        color: var(--bs-body-color) !important;
    }


    /* 3. KONDISI NORMAL (Saat JS tambah class .bg-success) */
    .industrial-card.bg-success {
        border-left-color: #059669 !important;
    }
    .industrial-card.bg-success::after {
        display: block;
        content: 'NORMAL';
        background-color: #059669; /* Hijau */
        color: #fff;
    }
    .industrial-card.bg-success h5, .industrial-card.bg-success span, .industrial-card.bg-success small {
        color: var(--bs-body-color) !important;
    }

    /* SEMBUNYIKAN ELEMENT BADGE BAWAAN JS */
    /* Karena kita sudah buat badge baru pakai CSS di atas, yang lama di-hide saja biar gak dobel */
    [id^="badge"] { display: none !important; }


    /* --- UNIT BADGE --- */
    .unit-badge {
        font-size: 0.75rem;
        padding: 2px 8px;
        border-radius: 4px;
        /* Force White Background Black Text */
        background-color: #ffffff !important;
        color: #000000 !important;
        font-family: sans-serif;
        margin-left: 8px;
        font-weight: 800;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        z-index: 5;
    }

    /* --- TYPOGRAPHY --- */
    .param-header {
        border-bottom: 1px solid rgba(0,0,0,0.1);
        padding-bottom: 10px;
        margin-bottom: 15px;
    }
    .param-name {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 1rem;
    }
    .value-display {
        font-family: 'JetBrains Mono', monospace;
        font-size: 2.8rem; 
        font-weight: 800; 
        line-height: 1;
        letter-spacing: -1px;
    }
    .sub-value-display {
        font-family: 'JetBrains Mono', monospace;
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--bs-secondary-color);
    }

    @keyframes pulse-red {
        0% { box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(220, 38, 38, 0); }
        100% { box-shadow: 0 0 0 0 rgba(220, 38, 38, 0); }
    }
</style>
@endsection

@section('content')
<div class="industrial-dashboard">
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 row-cols-xxl-4 g-4 mb-4" id="panel-value-logs">
        @foreach ($parameters as $param)
        <div class="col col-parameter" id="param{{ $param->parameter_id }}">
            {{-- Class card diganti industrial-card --}}
            <div class="card industrial-card h-100">
                
                {{-- Container badge asli (Di-hide oleh CSS, digantikan oleh ::after) --}}
                <div id="badge{{ $param->parameter_id }}"></div>
                
                <div class="card-body d-flex flex-column justify-content-between p-4">
                    
                    {{-- HEADER --}}
                    <div class="d-flex align-items-center param-header">
                        <ion-icon name="pulse" class="text-primary fs-5 me-2"></ion-icon>
                        <span class="param-name">{{ $param->name }}</span>
                        <span class="unit-badge">{{ $param->unit->name }}</span>
                    </div>

                    {{-- VALUE DISPLAY --}}
                    <div class="row align-items-end mb-3">
                        <div class="col-7">
                            <small class="d-block mb-1 fw-bold opacity-75" style="font-size: 0.7rem;">MEASURED</small>
                            <h5 class="value-display mb-0" id="param-measured-{{ $param->parameter_id }}">
                                --.--
                            </h5>
                        </div>
                        <div class="col-5 text-end">
                            <small class="d-block mb-1 fw-bold opacity-75" style="font-size: 0.7rem;">CORRECTIVE</small>
                            <h5 class="sub-value-display mb-0" id="param-corrective-{{ $param->parameter_id }}">
                                --
                            </h5>
                        </div>
                    </div>

                    {{-- FOOTER --}}
                    <div class="mt-auto pt-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                @if(!in_array($param->max_value,[null,0,999]))
                                    <div style="font-size: 0.75rem;" class="text-muted">
                                        MAX: <span class="fw-bold">{{ $param->max_value }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="line-chart ms-auto" id="chart{{ $param->parameter_id }}" style="min-height: 40px;"></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- TABLE LOGS --}}
    <div class="card w-100 shadow-sm">
        <div class="card-header bg-transparent py-3">
            <div class="d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold">
                    <ion-icon name="server-outline" class="me-2"></ion-icon> 
                    SISPEK Integration | <span class="text-primary">{{ $stackActive->code }}</span>
                </h6>
                <button id="btn-refresh-table" class="btn btn-sm btn-outline-secondary">
                    <ion-icon name="sync-outline"></ion-icon> REFRESH
                </button>
            </div>
        </div>
        
        <div class="card-body">
            <div class="mb-3">
                <select name="parameter_id" class="form-select" id="filter-parameter-id" multiple>
                    <option value="1">All Parameters</option>
                    @foreach ($parameters as $parameter)
                        @if (!empty($parameter->sispek_code))
                        <option value="{{ $parameter->id }}">{{ $parameter->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div class="table-responsive">
                <table id="table-logs" class="table table-hover align-middle mb-0 table-sm" style="width: 100%">
                    <thead>
                        <tr>
                            <th>#ID</th>
                            <th>STACK</th>
                            <th>PARAMETER</th>
                            <th>VALUE</th>
                            <th>TIMESTAMP</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset("assets/plugins/datatable/js/jquery.dataTables.min.js") }}"></script>
<script src="{{ asset("assets/plugins/datatable/js/dataTables.bootstrap5.min.js") }}"></script>
<script src="{{ asset("assets/plugins/apexcharts-bundle/js/apexcharts.min.js") }}"></script>
<script src="{{ asset("assets/plugins/select2/js/select2.min.js") }}"></script>

@if (session()->has("error"))
<script>
  toastr.error(`{{ session("error") }}`)
</script>
@endif

{{-- JAVASCRIPT ORIGINAL (TIDAK DISENTUH) --}}
<script>
  $(document).ready(function(){
    $('#showLineChart').trigger('click')
    // Global Variable
    var lineChartActive = []
    var lineChart = []
    // 
    const table = $("#table-logs").DataTable({
      lengthChange : false,
      searching : false,
      paging : false,
      ordering : false,
      serverSide : true,
      processing : true,
      ajax : {
        url : "{{ route('datatable.value-sent',[$stackActive->id]) }}",
        data : function(req){
          req.parameter_id = $("#filter-parameter-id").val();
        }
      },
      columns : [
        {data:'id'},
        {
          render : function(data,type,row){
            return row.parameter?.stack ? row.parameter?.stack?.code : '-'
          },
          data:'parameter.stack.code'
        },
        {
          render : function(data,type,row){
            return row.parameter? row.parameter.name : '-'
          },
          data:'parameter.name'
        },
        {
          render:function(data,type,row){
            return `<strong class="fs-6">${row.value_correction}</strong> <small>${row.parameter ? row.parameter.unit.name : '-'}</small>`
          },
          data:'value_correction'
        },
        {data:'time_group'},
        {
          render : function(data,type,row){
            return row.is_sent_sispek == 1 ? `<span class="badge bg-success alert-success">Sent</span>`
            : `<span class="badge bg-danger alert-danger">Not Yet</span>`
          },
          data:'is_sent_sispek'
        },
      ]
    })
    function reloadTable(){
      table.ajax.reload()
      setTimeout(reloadTable, 500000); //Refresh per 5 detik
    }
    function getValue(){
     const id = {{ $stackActive->id }}
     try{
      $.getJSON(`{{ url('value-logs') }}/${id}`,function(data){
        const isLineChartShow = $('#showLineChart').is(':checked')
        if(data.length > 0){
          let o2Value = 0
          data.map(function(logs){
            if(logs?.parameter?.p_type == 'o2'){
              o2Value = logs.measured
            }
            if(!lineChartActive.includes(logs.parameter_id)){
              initLineChart(logs.parameter_id)
              lineChartActive.push(logs.parameter_id)
            }
            $(`#param-measured-${logs.parameter_id}`).html(parseFloat(logs.measured).toFixed(logs.parameter.rounding))
            $(`#param-measured-${logs.parameter_id}`).prop('title',logs.xtimestamp)
            $(`#badge${logs.parameter_id}`).html(``)
            if(logs.parameter.p_type == 'main'){
              const o2StackReference = logs.parameter.stack.oxygen_reference ?? 0
              const corrective = (logs.measured * (21 - o2StackReference) / (21 - o2Value)).toFixed(logs.parameter.rounding)
              $(`#param-corrective-${logs.parameter_id}`).html(corrective)
              const allClass = 'bg-white bg-danger bg-success text-light'
              const badge = `<span class="position-absolute top-0 start-100 translate-middle badge border border-light rounded-circle p-2"><span class="visually-hidden">unread messages</span></span>`
              const percentage = parseFloat(corrective/logs.parameter.max_value)
              if(corrective <= 0){
                $(`#param${logs.parameter_id} > .card`).removeClass(allClass).addClass('bg-dark text-white')
                $(`#badge${logs.parameter_id}`).html($(badge).addClass("bg-dark"))
              }else if(percentage >= 0.9){
                $(`#badge${logs.parameter_id}`).html($(badge).addClass("bg-danger text-white"))
                $(`#param${logs.parameter_id} > .card`).removeClass(allClass).addClass('bg-danger text-white')
              }else if(percentage > 0 && percentage < 0.9){
                $(`#param${logs.parameter_id} > .card`).removeClass(allClass).addClass('bg-success text-white')
              }else{
                $(`#param${logs.parameter_id} > .card`).removeClass(allClass).addClass('bg-success text-white')
              }
            }else{
              const corrective = parseFloat(logs.corrective).toFixed(logs.parameter.rounding)
              $(`#param-corrective-${logs.parameter_id}`).html(corrective)
            }
            if(isLineChartShow){
              getLineChart(logs.parameter_id)
            }
          })
        }
      }).fail(function(){
        console.log('Cant get JSON')
      })
     }catch(e){
       console.log(e)
     }
     setTimeout(getValue, 30000);
    }
    function initLineChart(parameterId){
        let options = {
        series: [{
            name: "Measured",
            data: []
        }],
        chart: {
            type: "area",
            width: 150,
            height: 40,
            toolbar: {
                show: !1
            },
            zoom: {
                enabled: !1
            },
            dropShadow: {
                enabled: 0,
                top: 3,
                left: 14,
                blur: 4,
                opacity: .12,
                color: "#923eb9"
            },
            sparkline: {
                enabled: !0
            }
        },
        markers: {
            size: 0,
            colors: ["#923eb9"],
            strokeColors: "#fff",
            strokeWidth: 2,
            hover: {
                size: 7
            }
        },
        plotOptions: {
            bar: {
                horizontal: !1,
                columnWidth: "35%",
                endingShape: "rounded"
            }
        },
        dataLabels: {
            enabled: !1
        },
        stroke: {
            show: !0,
            width: 2,
            curve: "smooth"
        },
        xaxis: {
            categories: []
        },
        fill: {
        type: 'gradient',
        gradient: {
          shade: 'light',
          type: 'vertical',
          shadeIntensity: 0.5,
          gradientToColors: ['#ff0080'],
          inverseColors: false,
          opacityFrom: 0.5,
          opacityTo: 0.1,
        }
        },
        colors: ["#6528F7"],
        tooltip: {
            theme: "dark",
            fixed: {
                enabled: !1
            },
            x: {
                show: !1
            },
            y: {
                title: {
                    formatter: function(e) {
                        return ""
                    }
                }
            },
            marker: {
                show: !1
            }
        }
      };
      lineChart[parameterId] = new ApexCharts(document.querySelector(`#chart${parameterId}`), options)
      lineChart[parameterId].render()
    }
    function getLineChart(parameterId){
      $.getJSON(`{{ url("realtime-line-chart") }}/${parameterId}`, function(data){
        let series = [{
          name : "Measured",
          data : data.reverse()
        }]
        lineChart[parameterId].updateSeries(series)
      })
    }

    $("#showLineChart").change(function(e){
      const isLineChartShow = $('#showLineChart').is(':checked')
      if(!isLineChartShow){
        $(".line-chart").addClass("d-none")
      }else{
        $(".line-chart").removeClass("d-none")
      }
    })

    $('select[name="parameter_id"]').select2({
      placeholder : "Filter Parameter",
      className : "form-select",
    })
    $('select[name="parameter_id"]').on('change',function(){
      table.ajax.reload()
    })
    $("#btn-refresh-table").click(function(){
      table.ajax.reload()
    })
    getValue()
  })
</script>
@endsection