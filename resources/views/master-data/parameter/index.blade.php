@extends('layouts.app')
@section('title','Master Data')
@section('subtitle','Parameter')
@section('content')

<div class="card radius-10 w-100">
    <div class="card-body">
      <div class="d-flex align-items-center justify-content-between">
        <h6 class="mb-0">Parameter</h6>
        <span>
          <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-filter" class="btn btn-sm btn-outline-secondary">
            <ion-icon name="filter-circle-outline"></ion-icon> Filter
          </a>
          <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-add" class="btn btn-sm btn-primary">
             Add Parameter
          </a>
        </span>
      </div>
      <div class="table-responsive mt-2">
        <table class="table align-middle mb-0" id="table-parameters" style="width: 100%">
          <thead class="table-light">
            <tr>
              <th class="text-center">#ID</th>
              <th class="text-center">Stack</th>
              <th class="text-center">Name</th>
              <th class="text-center">SISPEK Code</th>
              <th class="text-center">Status</th>
              <th class="text-center">Type</th>
              <th class="text-center">Unit</th>
              <th class="fs-5 text-center">
               
              </th>
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
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Filter Data</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST" id="form-filter">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
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
              <div class="form-group">
                <label>Status</label>
                <select name="status_id" class="form-select">
                  <option value="">All</option>
                  @foreach ($statuses as $status)
                      <option value="{{ $status->id }}">{{ $status->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Type</label>
                <select name="p_type" class="form-select">
                  <option value="">All Type</option>
                  @foreach ($types as $type)
                      <option value="{{ $type }}">{{ Str::ucfirst($type) }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
          <div class="d-flex justify-content-between my-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Set Filter</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
{{-- Modal Edit --}}
<div class="modal fade" id="modal-edit" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Parameter</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="/" method="POST" id="form-edit-parameter">
          @csrf
          @method("PATCH")
          <div class="row">
            <div class="col-md-4">
              <div class="form-group mb-2">
                <label>Stack</label>
                <select name="stack_id" class="form-select">
                  <option value="">Select Stack</option>
                  @foreach ($stacks as $stack)
                      <option value="{{ $stack->id }}">{{ $stack->code }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group mb-2">
                <label>Parameter Name</label>
                <input type="text" name="name" placeholder="Parameter Name" class="form-control">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group mb-2">
                <label>ID</label>
                <input type="number" name="parameter_id" placeholder="Parameter ID" class="form-control">
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group mb-2">
                <label class="text-danger" title="Please don't edit this field">SISPEK Code</label>
                <input type="text" name="sispek_code" placeholder="SISPEK Code" class="form-control">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group mb-2">
                <label>Unit</label>
                <select name="unit_id" class="form-select">
                  <option value="">Select Unit</option>
                  @foreach ($units as $unit)
                      <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group mb-2">
                <label title="O2 = Oxygen , Main = Corrected, Support = Average Only">Type</label>
                <select name="p_type" class="form-select">
                  <option value="">Select Type</option>
                  @foreach ($types as $type)
                      <option value="{{ $type }}">{{ Str::ucfirst($type) }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group mb-2">
                <label>Status</label>
                <select name="status_id" class="form-select">
                  <option value="">Select Status</option>
                  @foreach ($statuses as $status)
                      <option value="{{ $status->id }}">{{ $status->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group mb-2">
                <label>Rounding</label>
                <input type="number" name="rounding" placeholder="Rounding" class="form-control">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group mb-2">
                <label>Baku Mutu</label>
                <input type="number" name="max_value" placeholder="Baku Mutu" class="form-control">
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group mb-2">
                <label title="Analog Input">AIN</label>
                <input type="number" name="ain" placeholder="AIN" class="form-control">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group mb-2">
                <label>Analyzer IP</label>
                <input type="text" name="ip_analyzer" placeholder="Analyzer IP" class="form-control">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group mb-2">
                <label>Alarm Notification</label>
                <div class="form-check form-switch">
                    <input type="hidden" name="enable_notification" value="0">
                    <input class="form-check-input" name="enable_notification" value="1" type="checkbox" id="enableNotification">
                    <label class="form-check-label" for="enableNotification">Enable</label>
                </div>
              </div>   
            </div>
            <div class="col-md-8">
              <div class="form-group mb-2">
                <label title="Data Aqcuitition Formula">DAS Formula</label>
                <textarea name="formula" rows="3" placeholder="Data Aqcuitition Formula" class="form-control"></textarea>
              </div>   
            </div>
          </div>
          <div class="d-flex justify-content-between my-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
{{-- Modal Add --}}
<div class="modal fade" id="modal-add" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Parameter</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="{{ route("master.parameter.store") }}" method="POST" id="form-add-parameter">
          @csrf
          <div class="row">
            <div class="col-md-4">
              <div class="form-group mb-2">
                <label>Stack</label>
                <select name="stack_id" class="form-select">
                  <option value="">Select Stack</option>
                  @foreach ($stacks as $stack)
                      <option value="{{ $stack->id }}">{{ $stack->code }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group mb-2">
                <label>Parameter Name</label>
                <input type="text" name="name" placeholder="Parameter Name" class="form-control">
              </div>
            </div>
             <div class="col-md-4">
              <div class="form-group mb-2">
                <label>ID</label>
                <input type="number" name="parameter_id" placeholder="Parameter ID" class="form-control">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group mb-2">
                <label class="text-danger">SISPEK Code</label>
                <input type="text" name="sispek_code" placeholder="SISPEK Code" class="form-control">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group mb-2">
                <label>Unit</label>
                <select name="unit_id" class="form-select">
                  <option value="">Select Unit</option>
                  @foreach ($units as $unit)
                      <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                  @endforeach
                  <option value="temp">Temp</option>
                  <option value="pressure">Pressure</option>
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group mb-2">
                <label title="O2 = Oxygen , Main = Corrected, Support = Average Only">Type</label>
                <select name="p_type" class="form-select">
                  <option value="">Select Type</option>
                  @foreach ($types as $type)
                      <option value="{{ $type }}">{{ Str::ucfirst($type) }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group mb-2">
                <label>Status</label>
                <select name="status_id" class="form-select">
                  <option value="">Select Status</option>
                  @foreach ($statuses as $status)
                      <option value="{{ $status->id }}">{{ $status->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group mb-2">
                <label>Rounding</label>
                <input type="number" name="rounding" placeholder="Rounding" class="form-control">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group mb-2">
                <label>Baku Mutu</label>
                <input type="number" name="max_value" placeholder="Baku Mutu" class="form-control">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group mb-2">
                <label title="Analog Input">AIN</label>
                <input type="number" name="ain" placeholder="AIN" class="form-control">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group mb-2">
                <label>Analyzer IP</label>
                <input type="text" name="ip_analyzer" placeholder="Analyzer IP" class="form-control">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group mb-2">
                <label>Alarm Notification</label>
                <div class="form-check form-switch">
                    <input type="hidden" name="enable_notification" value="0">
                    <input class="form-check-input" name="enable_notification" value="1" type="checkbox" id="enableNotification">
                    <label class="form-check-label" for="enableNotification">Enable</label>
                </div>
              </div>   
            </div>
            <div class="col-md-8">
              <div class="form-group mb-2">
                <label title="Data Aqcuitition Formula">DAS Formula</label>
                <textarea name="formula" rows="3" placeholder="Data Aqcuitition Formula" class="form-control"></textarea>
              </div>   
            </div>
            <div class="col-md-6">
              <div class="form-group mb-2">
                <label>PI Tag ID (Get)</label>
                <input type="text" name="web_id" placeholder="PI-XXX" class="form-control">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group mb-2">
                <label>PI Tag ID (POST)</label>
                <input type="text" name="web_id_post" placeholder="PI-XXX" class="form-control">
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group mb-2">
                <label>Is Normalized?</label>
                <select name="is_normalized" class="form-control">
                  <option value="0">Not yet normalized</option>
                  <option value="1">Has been normalized</option>
                </select>
              </div>
            </div>
          </div>
          <div class="d-flex justify-content-between my-2">
            <button type="reset" class="d-none"></button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Create New</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
@section('css')
<link rel="stylesheet" href="{{ asset("assets/plugins/datatable/css/dataTables.bootstrap5.min.css") }}">
@endsection
@section('js')
<script src="{{ asset("assets/plugins/datatable/js/jquery.dataTables.min.js") }}"></script>
<script src="{{ asset("assets/plugins/datatable/js/dataTables.bootstrap5.min.js") }}"></script>
<script>
  $(document).ready(function(){
    const table = $("#table-parameters").DataTable({
      theme : "boostrap5",
      serverSide: true,
      processing: true,
      ajax : {
        url : "{{ route('master.parameter.datatable') }}",
        data : function(req){
          req.stack_id = $("#form-filter select[name='stack_id']").val();
          req.status_id = $("#form-filter select[name='status_id']").val();
          req.p_type = $("#form-filter select[name='p_type']").val();
        }
      },
      columns : [
        {
          className : 'text-center',
          render : function(data,type,row){
            return `#${row.id} <small class="text-secondary fs-7">(${row.parameter_id})</small>`
          },
          data : "id"
        },
        {
          className : 'text-center',
          data : "stack.code"
        },
        {
          className : 'text-center',
          data : "name"
        },
        {
          className : 'text-center',
          data : "sispek_code"
        },
        {
          className : 'text-center',
          render: function(data,type,row){
            return `<span class="badge badge-alert bg-${row.status_id == 1 ? 'success' : row.status_id == 4 ? 'danger' : 'info' }">${row.status.name}</span>`
          },
          data : "status_id"
        },
        {
          className : 'text-center',
          render: function(data,type,row){
            return `<span class="fs-7 badge badge-alert bg-${row.p_type == 'o2' ? 'warning' : row.p_type == 'main' ? 'success' : 'info' }">${row.p_type}</span>`
          },
          data : "p_type"
        },
        {
          className : 'text-center',
          data : "unit.name"
        },
        {
          className : 'text-center',
          orderable : false, 
          render : function(data,type,row){
            return ` <div class="d-flex align-items-center gap-3 fs-6">
                      <!--<a href="{{ route('master.parameter.index') }}/${row.id}" class="text-primary btn-view">
                        <ion-icon name="eye-sharp"></ion-icon>
                      </a> -->
                      <a href="#" data-json='${JSON.stringify(row)}' class="text-warning btn-edit">
                        <ion-icon name="pencil-sharp"></ion-icon>
                      </a>
                      <a href="#" data-id='${row.id}' data-name='${row.name} (${row.stack.code})' class="text-danger btn-delete">
                        <ion-icon name="trash-sharp"></ion-icon>
                      </a>
                    </div>`
          },
        }
        
      ]
    })
    // Filter Action
    $("#form-filter").submit(function(e){
      e.preventDefault()
      table.ajax.reload()
    })
    $(document).delegate('.btn-edit','click',function(){
      const data = $(this).data("json")
      const url = `{{ route("master.parameter.index") }}/${data.id}`
      $("#modal-edit").modal("show")
      $("#form-edit-parameter").attr("action",url)
      $("#form-edit-parameter select[name='stack_id']").val(data.stack_id)
      $("#form-edit-parameter select[name='unit_id']").val(data.unit_id)
      $("#form-edit-parameter select[name='p_type']").val(data.p_type)
      $("#form-edit-parameter select[name='status_id']").val(data.status_id)
      $("#form-edit-parameter input[name='parameter_id']").val(data.parameter_id)
      $("#form-edit-parameter input[name='sispek_code']").val(data.sispek_code)
      $("#form-edit-parameter input[name='name']").val(data.name)
      $("#form-edit-parameter textarea[name='formula']").val(data.formula)
      $("#form-edit-parameter input[name='rounding']").val(data.rounding)
      $("#form-edit-parameter input[name='max_value']").val(data.max_value)
      $("#form-edit-parameter input[name='ip_analyzer']").val(data.ip_analyzer)
      $("#form-edit-parameter input[name='ain']").val(data.ain)
      $("#form-edit-parameter input[name='web_id']").val(data.web_id)
      $("#form-edit-parameter input[name='web_id_post']").val(data.web_id_post)
      $("#form-edit-parameter select[name='is_normalized']").val(data.is_normalized)
    })

    $(document).delegate('.btn-delete','click',function(){
        const name = $(this).data("name")
        const id = $(this).data("id")
        Swal.fire({
          title: `Do you want to delete ${name}?`,
          text : `Parameter ${name} will be deleted and cant revert this action after you click confirm`,
          showDenyButton: true,
          showCancelButton: false,
          confirmButtonText: 'Confirm',
          denyButtonText: `Cancel Delete`,
        }).then((result) => {
         
          if (result.isConfirmed) {
            $.ajax({
              url : `{{ route('master.parameter.index') }}/${id}`,
              method : "DELETE",
              data : {
                _token : `{{ csrf_token() }}`
              },
              dataType : 'json',
              success : function(data){
                toastr.success(data.message)
                table.ajax.reload()
              },
              error : function(data){
                data.responseJSON.errors.map(function(error){
                  toastr.error(error)
                })
              }
            })
          }
        })
        let timer = 10
        let confirmInterval = setInterval(() => {
          if(timer<=0){
            $('.swal2-confirm').prop("disabled",false)
            $('.swal2-confirm').html("Confirm")
            clearInterval(confirmInterval)
          }else{
            $('.swal2-confirm').html(`Confirm (${timer})`)
            $('.swal2-confirm').prop("disabled",true)
          }
          timer--
        }, 1000);
        $('.swal2-deny').click(function(){
          clearInterval(confirmInterval)
        })
    })
    $('#form-add-parameter').submit(function(e){
      e.preventDefault()
      $.ajax({
          url : `{{ route('master.parameter.store') }}`,
          type : 'POST',
          dataType : 'json',
          data : $(this).serialize(),
          success : function(data){
            if(data.success){
              toastr.success(data.message)
              table.ajax.reload()
              $(this).find("button[type='reset']").trigger("click")
              $("#modal-add").modal("hide")
            }
          },
          error : function(data){
            data.responseJSON.map(function(error){
              toastr.error(error)
            })
          }
      })
    })

    // Submit Form Edit
    $("#form-edit-parameter").submit(function(e){
      e.preventDefault()
      $.ajax({
          url : $(this).attr("action"),
          type : "PATCH",
          dataType : 'json',
          data : $(this).serialize(),
          success : function(data){
              if(data.success){
                toastr.success(data.message)
                table.ajax.reload()
              }
          },
          error : function(err){
              err.responseJSON.errors.map(function(error){
                toastr.error(error)
              })
          }
      })
    })
  })
</script>
@endsection