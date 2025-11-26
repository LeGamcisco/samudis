@extends('layouts.app')
@section('title','DIS Logs')
@section('subtitle','Data')
@section('header-option')
@endsection
@section('content')

<div class="card radius-10 w-100">
    <div class="card-body">
      <div class="d-flex align-items-center justify-content-between">
        <h6 class="mb-0">DIS Logs</h6>
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
            <a href="javascript:void(0)" id="btn-export" class="btn btn-sm btn-primary">
              <ion-icon name="cloud-download"></ion-icon> Export
            </a>
            {{-- <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-unsent" class="btn btn-sm btn-outline-warning">
              <ion-icon name="arrow-undo-outline"></ion-icon> Unsent
            </a> --}}
            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-filter" class="btn btn-sm btn-outline-info">
              <ion-icon name="filter-circle-outline"></ion-icon> Filter
            </a>
            <a href="#" onclick="return window.history.go(-1)"  class="btn btn-sm btn-outline-secondary">
              <ion-icon name="arrow-back-circle-outline"></ion-icon> Back
            </a>
          </div>
        </span>
      </div>
      <div class="table-responsive mt-2">
        <table class="table align-middle mb-0" style="width: 100%" id="table-data">
          <thead class="table-light">
            <tr>
              <th class="text-center">#ID</th>
              <th class="text-center">Datetime</th>
              <th class="text-center">Stack</th>
              <th class="text-center">Parameter</th>
              <th class="text-center">Measured</th>
              <th class="text-center">Corrective</th>
              <th class="text-center">Unit</th>
              <th class="text-center">Data Status</th>
              <th class="text-center">SISPEK Status</th>
              {{-- <th class="fs-5 text-center"></th> --}}
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
</div>
@endsection
@section('modal')
{{-- Modal Filter --}}
<div class="modal fade" id="modal-filter" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Filter Data</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST" id="form-filter">
          <input type="hidden" name="data_source">
          <div class="row ">
            <div class="col-md-4">
              <div class="form-group mb-2">
                <label>Stack</label>
                <select name="stack_id" class="form-select">
                  <option value="">All Stack</option>
                  @foreach ($stacks as $stack)
                      <option value="{{ $stack->id }}">{{ $stack->code }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group mb-2">
                <label>Status</label>
                <select name="data_status_id" class="form-select">
                  <option value="">All</option>
                  @foreach ($statuses as $status)
                      <option value="{{ $status->id }}">{{ $status->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group mb-2">
                <label>Parameter</label>
                <select multiple name="parameter_id[]" data-parent="#modal-filter" class="form-select form-select2">
                  <option value="">All Parameter</option>
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group mb-2">
                <label>Datetime Start</label>
                <input type="datetime-local" name="datetime_start" class="form-control">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group mb-2">
                <label>Datetime End</label>
                <input type="datetime-local" value="{{  now()->format('Y-m-d\TH:i') }}" name="datetime_end" class="form-control">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group mb-2">
                <label>Status SISPEK</label>
                <select name="is_sent_sispek" class="form-select">
                  <option value="">All</option>
                  <option value="0">Not Sent</option>
                  <option value="1">Sent</option>
                </select>
              </div>
            </div>
          </div>
          <div class="d-flex justify-content-between my-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <div class="gap-2">
              <button type="reset" class="btn btn-warning text-white" data-bs-dismiss="modal">Clear Filter</button>
              <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Set Filter</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="modal-unsent" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-warning text-white">
        <h5 class="modal-title fs-6 m-0 p-0">Set Unsent Data</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST" id="form-unsent">
          @csrf
          <div class="row ">
            <div class="col-md-4">
              <div class="form-group mb-2">
                <label>Stack</label>
                <select name="stack_id" class="form-select">
                  <option value="">All Stack</option>
                  @foreach ($stacks as $stack)
                      <option value="{{ $stack->id }}">{{ $stack->code }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group mb-2">
                <label>Status</label>
                <select name="status_id" class="form-select">
                  @foreach ($statuses as $status)
                      <option value="{{ $status->id }}">{{ $status->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group mb-2">
                <label>Parameter</label>
                <select multiple name="parameter_id[]" data-parent="#modal-unsent" class="form-select form-select2">
                  <option value="">All Parameter</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group mb-2">
                <label>Datetime Start</label>
                <input type="datetime-local" name="datetime_start" class="form-control">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group mb-2">
                <label>Datetime End</label>
                <input type="datetime-local" name="datetime_end" class="form-control">
              </div>
            </div>
          </div>
          <div class="d-flex justify-content-between my-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <div class="gap-2">
              <button type="submit" class="btn btn-warning text-white" data-bs-dismiss="modal">Set Unsent</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
@section('css')
<link rel="stylesheet" href="{{ asset("assets/plugins/datatable/css/dataTables.bootstrap5.min.css") }}">
<link rel="stylesheet" href="{{ asset("assets/plugins/select2/css/select2.min.css") }}">
<link rel="stylesheet" href="{{ asset("assets/plugins/select2/css/select2-bootstrap4.css") }}">
@endsection
@section('js')
<script src="{{ asset("assets/plugins/datatable/js/jquery.dataTables.min.js") }}"></script>
<script src="{{ asset("assets/plugins/datatable/js/dataTables.bootstrap5.min.js") }}"></script>
<script src="{{ asset("assets/plugins/select2/js/select2.min.js") }}"></script>
<script>
  $(document).ready(function(){
    const table = $("#table-data").DataTable({
      theme : "boostrap5",
      serverSide: true,
      processing: true,
      order : [1, 'desc'],
      ajax : {
        url : "{{ route('dis-logs.datatable') }}",
        data : function(req){
          req.data_source = $("#form-filter input[name='data_source']").val();
          req.data_status_id = $("#form-filter select[name='data_status_id']").val();
          req.stack_id = $("#form-filter select[name='stack_id']").val();
          req.parameter_id = $("#form-filter select[name='parameter_id[]']").val();
          req.datetime_start = $("#form-filter input[name='datetime_start']").val();
          req.datetime_end = $("#form-filter input[name='datetime_end']").val();
          req.is_sent_sispek = $("#form-filter select[name='is_sent_sispek']").val();
        }
      },
      columns : [
        {
          className : 'text-center',
          render : function(data,type,row){
            return `#${row.id}`
          },
          data : "id"
        },
        {
          className : 'text-center',
          data : "time_group",
        },
        {
          className : 'text-center',
          render: function(data,type,row){
              return row.stack_name
          },
          data : "stacks.id"
        },
        {
          className : 'text-center',
          render: function(data,type,row){
              return row.parameter_name
          },
          data : "parameters.name"
        },
        {
          className : 'text-center',
          data : "value"
        },
        {
          className : 'text-center',
          data : "value_correction"
        },
        {
          className : 'text-center',
          render: function(data,type,row){
              return row.unit_name
          },
          data : "units.name"
        },
        {
          className : 'text-center',
          render: function(data,type,row){
            return `<span class="badge badge-alert bg-${row.data_status_id == 1 ? 'success' : row.data_status_id == 4 ? 'danger' : 'info' }">${row.status_name}</span>`
          },
          data : "data_status_id"
        },
        {
          className : 'text-center',
          render: function(data,type,row){
            return `<span class="badge badge-alert bg-${row.is_sent_sispek == 1 ? 'success' :  'danger' }">${row.is_sent_sispek ? `Sent` : 'Not Sent'}</span>`
          },
          data : "is_sent_sispek"
        },
        
        // {
        //   className : 'text-center',
        //   orderable : false, 
        //   render : function(data,type,row){
        //     return ` <div class="d-flex align-items-center gap-3 fs-6">
        //               <a href="#" class="text-primary btn-view">
        //                 <ion-icon name="eye-sharp"></ion-icon>
        //               </a>
        //             </div>`
        //   }
        // }
        
      ]
    })
    // Filter Action
    $("#form-filter").submit(function(e){
      e.preventDefault()
      table.ajax.reload()
    })

    $('#form-unsent').submit(function(e){
      e.preventDefault()
      $.ajax({
          url : `{{ route("dis-logs.unsent") }}`,
          type : 'post',
          dataType : 'json',
          data : $(this).serialize(),
          success : function(data){
              if(data.success){
                $('#modal-unsent').modal('hide')
                table.ajax.reload()
                return toastr.success(`Data was updated to unsent`)
              }
              return toastr.error(data?.message)
            },
            error : function(xhr, status, err){
            return toastr.error(xhr?.responseJSON?.message)
              
          }
      })
    })

    $('#form-filter button[type="reset"]').click(function(){
      $('#form-filter').trigger("reset")
      table.ajax.reload()
    })
    $('.form-select2').each(function(){
      $(this).select2({
        dropdownParent : $(this).parents('.modal'),
      })
    })

    $('select[name="data_source"]').change(function(){
      const value = $(this).val()
      $('input[name="data_source"]').val(value)
      table.ajax.reload()
    })
    $('select[name="data_source"]').select2()

    $('select[name="stack_id"]').change(function(){
      const id = $(this).val()
      $.ajax({
          url : `{{ route("master.stack.index") }}/parameters/${id}`,
          dataType : 'json',
          success : function(data){
              if(data.parameters){
                $('select[name="parameter_id[]"]').empty()
                let parameters = []
                data.parameters.map(function(parameter){
                  parameters.push({id: parameter.parameter_id, text:parameter.name})
                  $('select[name="parameter_id[]"]').append(`<option value="${parameter.parameter_id}">${parameter.name}</option>`)
                })
                $('.form-select2').each(function(){
                  $(this).select2({
                    dropdownParent : $(this).parents($(this).data('parent')),
                    data:parameters
                  })
                })

              }
          }
      })
    })
    $('#btn-export').click(function(){
      const params = $('#form-filter').serialize()
      window.location.href = `{{ route("dis-logs.export") }}?${params}`
    })

  })
</script>
@endsection