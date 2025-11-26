@extends('layouts.app')
@section('title','Master Data')
@section('subtitle','Reference Formula')
@section('content')

<div class="card radius-10 w-100">
    <div class="card-body">
      <div class="d-flex align-items-center justify-content-between">
        <h6 class="mb-0">Reference Formula</h6>
        <span>
          <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-add" class="btn btn-sm btn-primary">
             Add Reference
          </a>
        </span>
      </div>
      <div class="table-responsive mt-2">
        <table class="table align-middle mb-0" id="table-parameters" style="width: 100%">
          <thead class="table-light">
            <tr>
              <th class="text-center">#ID</th>
              <th class="text-center">Parameter</th>
              <th class="text-center">Range Start</th>
              <th class="text-center">Range End</th>
              <th class="text-center">Formula</th>
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
{{-- Modal Edit --}}
<div class="modal fade" id="modal-edit" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Parameter</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="{{ route("master.reference.store") }}" method="POST" id="form-edit-reference">
          @csrf
          @method("PATCH")
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label>Parameter *</label>
                <select name="parameter_id" class="form-control">
                  <option value="">Select Parameter</option>
                  @foreach ($parameters as $parameter)
                      <option value="{{ $parameter->id }}">{{ $parameter->name }} ({{ $parameter->Stack->code ?? "-" }})</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Range Start *</label>
                <input type="numeric" inputmode="numeric" name="range_start" placeholder="Range start" class="form-control" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Range End *</label>
                <input type="numeric" inputmode="numeric" name="range_end" placeholder="Range end" class="form-control" required>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label>Formula *</label>
                <textarea name="formula" class="form-control" placeholder="Formula"></textarea>
              </div>
            </div>
          </div>
          <div class="d-flex justify-content-between my-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="reset" class="d-none">Reset</button>
            <button type="submit" class="btn btn-primary">Save changes</button>
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
        <form action="{{ route("master.reference.store") }}" method="POST" id="form-add-reference">
          @csrf
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label>Parameter *</label>
                <select name="parameter_id" class="form-control">
                  <option value="">Select Parameter</option>
                  @foreach ($parameters as $parameter)
                      <option value="{{ $parameter->id }}">{{ $parameter->name }} ({{ $parameter->Stack->code ?? "-" }})</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Range Start *</label>
                <input type="numeric" inputmode="numeric" name="range_start" placeholder="Range start" class="form-control" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Range End *</label>
                <input type="numeric" inputmode="numeric" name="range_end" placeholder="Range end" class="form-control" required>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label>Formula *</label>
                <textarea name="formula" class="form-control" placeholder="Formula"></textarea>
              </div>
            </div>
          </div>
          <div class="d-flex justify-content-between my-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="reset" class="d-none">Reset</button>
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
        url : "{{ route('master.reference.datatable') }}"
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
          data : "parameter.name",
          render : function(data,type,row){
            return `${row.parameter.name} <small class="text-secondary fs-7">(${row.parameter.stack?.code ?? "-"})</small>`
          }
        },
        {
          className : 'text-center',
          data : "range_start"
        },
        {
          className : 'text-center',
          data : "range_end"
        },
        {
          className : 'text-center',
          data : "formula",
          render : function(data,type,row){
            return `<textarea class="form-control" readonly style="resize:none">${row.formula}</textarea>`
          }
        },
        {
          className : 'text-center',
          orderable : false, 
          render : function(data,type,row){
            return ` <div class="d-flex align-items-center gap-3 fs-6">
                      <!--<a href="{{ route('master.reference.index') }}/${row.id}" class="text-primary btn-view">
                        <ion-icon name="eye-sharp"></ion-icon>
                      </a> -->
                      <a href="#" data-json='${JSON.stringify(row)}' class="text-warning btn-edit">
                        <ion-icon name="pencil-sharp"></ion-icon>
                      </a>
                      <a href="#" data-id='${row.id}' class="text-danger btn-delete">
                        <ion-icon name="trash-sharp"></ion-icon>
                      </a>
                    </div>`
          }
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
      const url = `{{ route("master.reference.index") }}/${data.id}`
      $("#modal-edit").modal("show")
      $("#form-edit-reference").attr("action",url)
      $("#form-edit-reference select[name='parameter_id']").val(data.parameter_id)
      $("#form-edit-reference input[name='range_start']").val(data.range_start)
      $("#form-edit-reference input[name='range_end']").val(data.range_end)
      $("#form-edit-reference textarea[name='formula']").val(data.formula)
    })

    $(document).delegate('.btn-delete','click',function(){
        const id = $(this).data("id")
        Swal.fire({
          title: `Do you want to delete?`,
          text : `Reference will be deleted and cant revert this action after you click confirm`,
          showDenyButton: true,
          showCancelButton: false,
          confirmButtonText: 'Confirm',
          denyButtonText: `Cancel Delete`,
        }).then((result) => {
         
          if (result.isConfirmed) {
            $.ajax({
              url : `{{ route('master.reference.index') }}/${id}`,
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
    $('#form-add-reference').submit(function(e){
      e.preventDefault()
      $.ajax({
          url : `{{ route('master.reference.store') }}`,
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
    $("#form-edit-reference").submit(function(e){
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
                $(this).find("button[type='reset']").trigger("click")
                $("#modal-edit").modal("hide")
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