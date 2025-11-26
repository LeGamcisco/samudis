@extends('layouts.app')
@section('title','Master Data')
@section('subtitle','Stack')
@section('content')

<div class="card radius-10 w-100">
    <div class="card-body">
      <div class="d-flex align-items-center justify-content-between">
        <h6 class="mb-0">Stack</h6>
        <span>
          <a href="#" data-bs-toggle="modal" data-bs-target="#modal-add-new" class="btn btn-sm btn-outline-primary">
             Add Stack
          </a>
        </span>
      </div>
      <div class="table-responsive mt-2">
        <table class="table align-middle mb-0" id="table-users" style="width: 100%">
          <thead class="table-light">
            <tr>
              <th class="">Action</th>
              <th class="text-center">#ID</th>
              <th class="text-center">Stack</th>
              <th class="text-center">SISPEK Code</th>
              <th class="text-center">O2 Reference</th>
              <th class="text-center">Height</th>
              <th class="text-center">Diameter</th>
              <th class="text-center">Flow</th>
              <th class="text-center">Latitude</th>
              <th class="text-center">Longitude</th>
              <th class="text-center">Last Updated</th>
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
{{-- Modal New --}}
<div class="modal fade" id="modal-add-new" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Stack</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="{{ route("master.stack.store") }}" method="POST" id="form-add-stack">
          @csrf
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Stack Name</label>
                <input type="text" name="code" placeholder="Stack Name" class="form-control">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>SISPEK Code</label>
                <input type="text" name="sispek_code" placeholder="Chimney Code" class="form-control">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>O2 Reference</label>
                <input type="number" name="oxygen_reference" placeholder="Chimney Code" class="form-control">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Height <small class="text-secondary">(m)</small></label>
                <input type="number" name="height" placeholder="Height" class="form-control">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Diameter <small class="text-secondary">(m)</small></label>
                <input type="number" name="diameter" placeholder="Height" class="form-control">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Flow</label>
                <input type="number" name="flow" placeholder="Height" class="form-control">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Latitude</label>
                <input type="text" name="lat" placeholder="Chimney Latitude" class="form-control">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Longitude</label>
                <input type="text" name="lon" placeholder="Chimney Longitude" class="form-control">
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
{{-- Modal Edit --}}
<div class="modal fade" id="modal-edit" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Stack</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="/" method="POST" id="form-edit-stack">
          @csrf
          @method("PATCH")
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Stack Name</label>
                <input type="text" name="code" placeholder="Stack Name" class="form-control">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>SISPEK Code</label>
                <input type="text" name="sispek_code" placeholder="Chimney Code" class="form-control">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>O2 Reference</label>
                <input type="number" name="oxygen_reference" placeholder="Chimney Code" class="form-control">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Height <small class="text-secondary">(m)</small></label>
                <input type="number" name="height" placeholder="Height" class="form-control">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Diameter <small class="text-secondary">(m)</small></label>
                <input type="number" name="diameter" placeholder="Height" class="form-control">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Flow</label>
                <input type="number" name="flow" placeholder="Height" class="form-control">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Latitude</label>
                <input type="text" name="lat" placeholder="Chimney Latitude" class="form-control">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Longitude</label>
                <input type="text" name="lon" placeholder="Chimney Longitude" class="form-control">
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
@endsection
@section('css')
<link rel="stylesheet" href="{{ asset("assets/plugins/datatable/css/dataTables.bootstrap5.min.css") }}">
@endsection
@section('js')
@if (session()->has("error"))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: '{{ session("error") }}'
        })
    </script>
    
@endif
<script src="{{ asset("assets/plugins/datatable/js/jquery.dataTables.min.js") }}"></script>
<script src="{{ asset("assets/plugins/datatable/js/dataTables.bootstrap5.min.js") }}"></script>
<script>
  $(document).ready(function(){
    const table = $("#table-users").DataTable({
      theme : "boostrap5",
      serverSide: true,
      processing: true,
      ordering:false,
      ajax : {
        url : "{{ route('master.stack.datatable') }}",
      },
      columnDefs : [
        {
          target : [0,1],
          orderable : false
        }
      ],
      columns : [
        {
          className : 'text-center',
          render : function(data,type,row){
            return ` <div class="d-flex align-items-center gap-3 fs-6">
                      <a href="{{ route('master.stack.index') }}/${row.id}" class="text-primary btn-view">
                        <ion-icon name="eye-sharp"></ion-icon>
                      </a>
                      <a href="#" data-json='${JSON.stringify(row)}' class="text-warning btn-edit">
                        <ion-icon name="pencil-sharp"></ion-icon>
                      </a>
                      <a href="#" data-id='${row.id}' data-name='${row.code}' class="text-danger btn-delete">
                        <ion-icon name="trash-sharp"></ion-icon>
                      </a>
                    </div>`
          },
          data : 'id'
        },
        {
          className : 'text-center',
          data : "id"
        },
        {
          className : 'text-center',
          data : "code"
        },
        {
          className : 'text-center',
          data : "sispek_code"
        },
        {
          className : 'text-center',
          render : function(data,type,row){
            return `<span class="badge bg-success">${row.oxygen_reference} %</span>`
          },
          data : "oxygen_reference"
        },
        {
          className : 'text-center',
          data : "height"
        },
        {
          className : 'text-center',
          data : "diameter"
        },
        {
          className : 'text-center',
          data : "flow"
        },
        {
          className : 'text-center',
          data : "lat"
        },
        {
          className : 'text-center',
          data : "lon"
        },
        {
          className : 'text-center',
          render: function(data,type,row){
            return moment(row.updated_at).locale("id").fromNow()
          },
          data : "updated_at"
        },
        
      ]
    })
    
    $(document).delegate('.btn-edit','click',function(){
      const data = $(this).data("json")
      const url = `{{ route("master.stack.index") }}/${data.id}`
      $("#modal-edit").modal("show")
      $("#form-edit-stack").attr("action",url)
      $("#form-edit-stack input[name='code']").val(data.code)
      $("#form-edit-stack input[name='sispek_code']").val(data.sispek_code)
      $("#form-edit-stack input[name='height']").val(data.height)
      $("#form-edit-stack input[name='diameter']").val(data.diameter)
      $("#form-edit-stack input[name='flow']").val(data.flow)
      $("#form-edit-stack input[name='lon']").val(data.lon)
      $("#form-edit-stack input[name='lat']").val(data.lat)
      $("#form-edit-stack input[name='oxygen_reference']").val(data.oxygen_reference)
    })

    $(document).delegate('.btn-delete','click',function(){
        const name = $(this).data("name")
        const id = $(this).data("id")
        Swal.fire({
          title: `Do you want to delete ${name}?`,
          text : `All parameter in ${name} will be deleted and cant revert this action after you click confirm`,
          showDenyButton: true,
          showCancelButton: false,
          confirmButtonText: 'Confirm',
          denyButtonText: `Cancel Delete`,
        }).then((result) => {
         
          if (result.isConfirmed) {
            $.ajax({
              url : `{{ route('master.stack.index') }}/${id}`,
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
    $('#form-add-stack').submit(function(e){
      e.preventDefault()
      $.ajax({
          url : `{{ route('master.stack.store') }}`,
          type : 'POST',
          dataType : 'json',
          data : $(this).serialize(),
          success : function(data){
            if(data.success){
              toastr.success(data.message)
              table.ajax.reload()
              $(this).find("button[type='reset']").trigger("click")
              $("#modal-add-new").modal("hide")
            }
          },
          error : function(data){
            data.responseJSON.errors.map(function(error){
              toastr.error(error)
            })
          }
      })
    })

    // Submit Form Edit
    $("#form-edit-stack").submit(function(e){
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