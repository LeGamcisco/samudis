@extends('layouts.app')
@section('title','Raw Data')
@section('subtitle','DIS Logs Analytic')
@section('header-option')
@endsection
@section('content')

<div class="card radius-10 w-100">
    <div class="card-body">
      <div class="d-flex align-items-center justify-content-between">
        <h6 class="mb-0">Data Trend DIS Logs</h6>
        <span class="d-flex align-items-center justiy-content-start gap-1">
          <div class="form-group d-inline">
            <select name="data_source" class="form-select">
              <option value="dis_logs">Select Data Source</option>
              @foreach ($tables as $table)
                  <option value="{{ $table }}">{{ $table }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-filter" class="btn btn-sm btn-outline-info">
              <ion-icon name="filter-circle-outline"></ion-icon> Filter
            </a>
            <a href="#" onclick="return window.history.go(-1)"  class="btn btn-sm btn-outline-secondary">
              <ion-icon name="arrow-back-circle-outline"></ion-icon> Back
            </a>
          </div>
        </span>
      </div>
      <div class="container mx-auto">
        <div id="chart1" class="w-full h-full p-5">

        </div>
      </div>
      
    </div>
</div>
@endsection
@section('modal')
{{-- Modal Filter --}}
<div class="modal fade" id="modal-filter" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Filter Data</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST" id="form-filter">
          <input type="hidden" name="data_source">
          <div class="row ">
            <div class="col-md-6">
              <div class="form-group mb-2">
                <label>Stack</label>
                <select required name="stack_id" class="form-select">
                  <option value="">Select Stack</option>
                  @foreach ($stacks as $stack)
                      <option value="{{ $stack->id }}">{{ $stack->code }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group mb-2">
                <label>Parameter</label>
                <select required name="parameter_id" class="form-select form-select2">
                  <option value="">Select</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group mb-2">
                <label>Datetime Start</label>
                <input type="datetime-local" required name="datetime_start" class="form-control">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group mb-2">
                <label>Datetime End</label>
                <input type="datetime-local" required name="datetime_end" class="form-control">
              </div>
            </div>
          </div>
          <div class="d-flex justify-content-between my-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <div class="gap-2">
              <button type="reset" class="btn btn-warning text-white" data-bs-dismiss="modal">Clear Filter</button>
              <button type="submit" class="btn btn-primary">Set Filter</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
@section('css')
<link rel="stylesheet" href="{{ asset("assets/plugins/select2/css/select2.min.css") }}">
<link rel="stylesheet" href="{{ asset("assets/plugins/select2/css/select2-bootstrap4.css") }}">
<link rel="stylesheet" href="{{ asset("assets/plugins/apexcharts-bundle/css/apexcharts.css") }}">
@endsection
@section('js')
<script src="{{ asset("assets/plugins/apexcharts-bundle/js/apexcharts.min.js") }}"></script>
{{-- <script src="{{ asset("assets/js/index.js") }}"></script> --}}
<script src="{{ asset("assets/plugins/select2/js/select2.min.js") }}"></script>
<script>
  var options = {
    series: [
      {
        name: "Measured",
        data: [] //Data
      },{
        name: "Corrective",
        data: []
      }
    ],
    chart: {
        type: "area",
        width: '100%',
        height: 380,
        toolbar: {
            show: true
        },
        zoom: {
            enabled: true
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
            enabled: false
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
            horizontal: true,
            columnWidth: "35%",
            endingShape: "rounded"
        }
    },
    dataLabels: {
        enabled: false
    },
    stroke: {
        show: true,
        width: 2,
        curve: "smooth"
    },
    xaxis: {
        categories: [] // Date
    },
    fill: {
      type: 'gradient',
      gradient: {
        shade: 'light',
        type: 'vertical',
        shadeIntensity: 0.5,
        gradientToColors: ['#00337C',"#03C988"],
        inverseColors: false,
        opacityFrom: 0.5,
        opacityTo: 0.1,
        //stops: [0, 100]
      }
	  },
    colors: ["#00337C","#03C988"],
    tooltip: {
        theme: "dark",
        fixed: {
            enabled: !1
        },
        x: {
            show: !1
        },
        y: [{
            title: {
                formatter: function(e) {
                    return "Measured"
                }
            },
        },{
            title: {
                formatter: function(e) {
                    return "Corrective"
                }
            }
        }],
        marker: {
            show: !1
        }
    },
    title: {
        text: "Data Trend",
        align: 'left',
        margin: 10,
        offsetX: 0,
        offsetY: 0,
        floating: false,
        style: {
          fontSize:  '14px',
          fontWeight:  'bold',
          fontFamily:  'Poppins',
          color:  '#03C988'
        },
    },
    annotations: {
      yaxis: [
        {
          y: 0,
          borderColor: '#FF2171',
          label: {
            borderColor: '#FF2171',
            style: {
              color: '#fff',
              background: '#FF2171'
            },
            text: 'Baku mutu'
          }
        }
      ]
    }
  };

  var chart = new ApexCharts(document.querySelector("#chart1"), options);
  chart.render();
  $("select[name='data_source']").change(function(){
    const value = $(this).val()
    $('#form-filter input[name="data_source"]').val(value)
  })
  $('#form-filter').submit(function(e){
    e.preventDefault()
    const button = $('#form-filter  button[type="submit"]')
    const parameterId = $('select[name="parameter_id"]').val()
    const parameter = $('select[name="parameter_id"] option:selected').text()
    const timeStart = $('input[name="datetime_start"]').val().split("T").join(" ")
    const timeEnd = $('input[name="datetime_end"]').val().split("T").join(" ")
    button.html(`Loading...`)
    button.prop("disabled",true)
    $.ajax({
      url : `{{route("dis-logs.analytic")}}/data/${parameterId}`,
      dataType : 'json',
      data : $(this).serialize(),
      success : function(data){
          if(data.success){
            button.prop("disabled",false)
            button.html(`Set Filter`)
            if(data.data.length == 0){
              return toastr.error(`${parameter} on ${timeStart} - ${timeEnd} not available`)
            }
            let measured = [],corrective = [], labels = []
            data.data.map(function(logs){
              measured.push(parseFloat(logs.value))
              corrective.push(parseFloat(logs.value_correction))
              labels.push(logs.time_group)
            })
            const series = [{data:measured},{data:corrective}]
            options.xaxis.categories = labels
            options.title.text = `${parameter} on ${timeStart} - ${timeEnd}`
            options.annotations.yaxis[0].label.text = `Baku mutu : ${data.parameter.max_value} ${data.parameter.unit.name}`
            options.annotations.yaxis[0].y = data.parameter.max_value
            chart.updateOptions(options)
            chart.updateSeries(series)
            $("#modal-filter").modal("hide")
          }
      },
      error : function(xhr, status, err){
          
      }
    })
  })
  

</script>
<script>
  $(document).ready(function(){
    $('select[name="data_source"]').select2()
    $('select[name="stack_id"]').change(function(){
      const id = $(this).val()
      $.ajax({
          url : `{{ route("master.stack.index") }}/parameters/${id}`,
          dataType : 'json',
          success : function(data){
              if(data.parameters){
                $('select[name="parameter_id"]').empty()
                let parameters = [{id : '', text:'Select Parameter'}]
                data.parameters.map(function(parameter){
                  parameters.push({id: parameter.parameter_id, text:parameter.name})
                })
                $('select[name="parameter_id"]').select2({
                  dropdownParent : $('#form-filter'),
                  data:parameters
                })

              }
          }
      })
    })
  })
</script>
@endsection