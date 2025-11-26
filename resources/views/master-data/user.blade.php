@extends('layouts.app')
@section('title','Master Data')
@section('subtitle','Users Management')
@section('header-option')
<div class="btn-group">
  <button type="button" class="btn btn-outline-primary">{{ @$group->name ? $group->name : 'Select Group' }}</button>
  <button type="button"
    class="btn btn-outline-primary split-bg-primary dropdown-toggle dropdown-toggle-split"
    data-bs-toggle="dropdown"> <span class="visually-hidden">Toggle Dropdown</span>
  </button>
  <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end">
    <a class="dropdown-item" href="{{ route("master.user.index") }}/0">Superuser</a>
    @foreach ($groups as $item)
    <a class="dropdown-item" href="{{ route("master.user.index") }}/{{ $item->id }}">{{ $item->name }}</a>
    @endforeach
  </div>
</div>
@endsection
@section('content')

<div class="card radius-10 w-100">
    <div class="card-body">
      <div class="d-flex align-items-center justify-content-between">
        <h6 class="mb-0">User Management</h6>
        <span>
          <a href="#" data-bs-toggle="modal" data-bs-target="#modal-add-user" class="btn btn-sm btn-outline-primary">
            <ion-icon name="person-add-outline"></ion-icon> Add User
          </a>
        </span>
      </div>
      <div class="table-responsive mt-2">
        <table class="table align-middle mb-0" id="table-users" style="width:100%">
          <thead class="table-light">
            <tr>
              <th class="text-center">#ID</th>
              <th class="text-center">Fullname</th>
              <th class="text-center">Email</th>
              <th class="text-center">Phone</th>
              <th class="text-center">Role</th>
              <th class="text-center">Created at</th>
              <th class="text-center">Updated at</th>
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
{{-- Modal View --}}
<div class="modal fade" id="modal-detail" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table class="table" id="table-user-detail">
          <tbody>
            <tr>
              <td>Fullname</td>
              <td class="fw-bold fullname">-</td>
            </tr>
            <tr>
              <td>Email</td>
              <td class="fw-bold email">-</td>
            </tr>
            <tr>
              <td>Phone</td>
              <td class="fw-bold phone">-</td>
            </tr>
            <tr>
              <td>Role</td>
              <td class="fw-bold role">-</td>
            </tr>
            <tr>
              <td>Created by</td>
              <td class="fw-bold created_by">-</td>
            </tr>
            <tr>
              <td>Created at</td>
              <td class="fw-bold created_at"></td>
            </tr>
            <tr>
              <td>Updated at</td>
              <td class="fw-bold updated_at"></td>
            </tr>
            <tr>
              <td>Updated by</td>
              <td class="fw-bold updated_by"></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
{{-- Modal Edit --}}
<div class="modal fade" id="modal-edit" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><ion-icon name="person-outline"></ion-icon> Edit User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="/" method="POST" id="form-edit-user">
          @csrf
          @method("PATCH")
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Fullname</label>
                <input type="text" name="name" placeholder="Fullname" class="form-control">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>e-Mail</label>
                <input type="email" name="email" placeholder="Email" class="form-control">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" placeholder="Phone Number" class="form-control">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Group</label>
                <select name="group_id" class="form-select" required>
                  <option value="">Select Role</option>
                  @if(Auth::user()->group_id == 0)
                  <option value="0">Superuser</option>
                  @endif
                  <option value="1">Administrator</option>
                  <option value="2">Operator</option>
                </select>
              </div>
            </div>
            <div class="col-md-12 py-2">
              <p class="alert alert-dismissible fade show alert-info"><ion-icon name="alert-circle-outline"></ion-icon> Input new password if you want to change <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></p>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>New Password <small class="text-secondary">(optional)</small></label>
                <input type="password" name="password" placeholder="New Password" class="form-control">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="password_confirmation" placeholder="Password Confirmation" class="form-control">
              </div>
            </div>
          </div>
          <div class="d-flex justify-content-between my-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="reset" class="btn-reset d-none">Reset</button>
            <button type="submit" class="btn btn-primary">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
{{-- Modal Add --}}
<div class="modal fade" id="modal-add-user" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><ion-icon name="person-outline"></ion-icon> Add User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="{{ route("master.user.store") }}" method="POST" id="form-add-user">
          @csrf
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Fullname</label>
                <input type="text" name="name" placeholder="ex: John Doe" class="form-control">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>e-Mail</label>
                <input type="email" name="email" placeholder="ex: user@domain.com" class="form-control">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" placeholder="ex: 628123456789" class="form-control">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Group</label>
                <select name="group_id" class="form-select" required>
                  <option value="">Select Role</option>
                  @if(Auth::user()->group_id == 0)
                  <option value="0">Superuser</option>
                  @endif
                  <option value="1">Administrator</option>
                  <option value="2">Operator</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Password</label>
                <input type="password" required name="password" placeholder="New Password" class="form-control">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" required name="password_confirmation" placeholder="Password Confirmation" class="form-control">
              </div>
            </div>
          </div>
          <div class="d-flex justify-content-between my-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="reset" class="btn-reset d-none">Reset</button>
            <button type="submit" class="btn btn-primary">Save</button>
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
    // $("#modal-edit").modal("show")
    const table = $("#table-users").DataTable({
      theme : "boostrap5",
      serverSide: true,
      processing: true,
      ajax : {
        url : "{{ route('master.user.datatable') }}",
        data : function(req){
          req.group_id = `{{ $groupId }}`
        }
      },
      columns : [
        {
          className : 'text-center',
          data : "id"},
        {
          className : 'text-center',
          data : "name"
        },
        {
          className : 'text-center',
          data : "email"
        },
        {
          className : 'text-center',
          data : "phone"
        },
        {
          className : 'text-center',
          render : function(data,type,row){
            return `<span class="badge badge-alert bg-${row.group_id == 0 ? 'success' : row.group_id == 1 ? 'info' : 'primary' }">
                    ${row.group ? row.group.name : `Superuser`}
                </span>`
          },
          data : "group_id"
        },
        {
          className : 'text-center',
          render: function(data,type,row){
            return moment(row.created_at).locale("id").format("dddd, D MMMM YYYY h:mm:ss a")
          },
          data : "created_at"
        },
        {
          className : 'text-center',
          render: function(data,type,row){
            return moment(row.updated_at).locale("id").fromNow()
          },
          data : "updated_at"
        },
        {
          className : 'text-center',
          orderable : false, 
          render : function(data,type,row){
            return ` <div class="d-flex align-items-center gap-3 fs-6">
                      <a href="#" data-json='${JSON.stringify(row)}' class="text-primary btn-view">
                        <ion-icon name="eye-sharp"></ion-icon>
                      </a>
                      <a href="#" data-json='${JSON.stringify(row)}' class="text-warning btn-edit">
                        <ion-icon name="pencil-sharp"></ion-icon>
                      </a>
                      <a href="#" data-id="${row.id}" data-name="${row.email}" class="text-danger btn-delete">
                        <ion-icon name="trash-sharp"></ion-icon>
                      </a>
                    </div>`
          }
        }
        
      ]
    })
    
    $(document).delegate('.btn-view','click',function(){
      const data = $(this).data("json")
      $("#modal-detail").modal("show")
      $("#table-user-detail .fullname").html(data.fullname)
      $("#table-user-detail .email").html(data.email)
      $("#table-user-detail .phone").html(data.phone)
      $("#table-user-detail .role").html(`
          <span class="badge badge-alert bg-${data.group_id == 0 ? 'success' : data.group_id == 1 ? 'info' : 'primary' }">
                ${data.group? data.group.name : `Superuser`}
          </span>
      `)
      $("#table-user-detail .created_by").html(data.created_by)
      $("#table-user-detail .created_at").html(moment(data.created_at).format("dddd, D MMMM YYYY h:mm:ss a"))
      $("#table-user-detail .updated_at").html(moment(data.updated_at).fromNow())
      $("#table-user-detail .updated_by").html(data.updated_by)
    })
    // Event on Edit Button
    $(document).delegate('.btn-edit','click',function(){
      const data = $(this).data("json")
      const url = `{{ route("master.user.index") }}/${data.id}`
      $("#modal-edit").modal("show")
      $("#form-edit-user").attr("action",url)
      $("#form-edit-user input[name='name']").val(`${data.name}`)
      $("#form-edit-user input[name='email']").val(data.email)
      $("#form-edit-user input[name='phone']").val(data.phone)
      $("#form-edit-user select[name='group_id']").val(data.group_id)
    })
    // Event on Delete Button
    $(document).delegate('.btn-delete','click',function(){
        const name = $(this).data("name")
        const id = $(this).data("id")
        Swal.fire({
          title: `Do you want to delete ${name}?`,
          text : `${name} will be deleted and cant revert this action after you click confirm`,
          showDenyButton: true,
          showCancelButton: false,
          confirmButtonText: 'Confirm',
          denyButtonText: `Cancel Delete`,
        }).then((result) => {
         
          if (result.isConfirmed) {
            $.ajax({
              url : `{{ route('master.user.index') }}/${id}`,
              method : "DELETE",
              data : {
                _token : `{{ csrf_token() }}`
              },
              dataType : 'json',
              success : function(data){
                toastr.success(data.message)
                table.ajax.reload()
              },
              error : function(xhr, status, err){
                const msg = xhr?.responseJSON?.message
                toastr.error(msg)
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

    // Submit Form
    $('#form-update-status').submit(function(e){
      e.preventDefault()
      $.ajax({
          url : `{{ route('settings.parameter.update') }}`,
          type : 'PATCH',
          dataType : 'json',
          data : $(this).serialize(),
          success : function(data){
            if(data.success){
              toastr.success(data.message)
              table.ajax.reload()
              selectParameter()
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
    $("#form-edit-user").submit(function(e){
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
                $('#modal-edit').modal('hide')
                $(this).find(".btn-reset").trigger("click")
              }
          },
          error : function(err){
              toastr.error(err.responseJSON.message ?? "Something went wrong")
          }
      })
    })
    // Submit Form Edit
    $("#form-add-user").submit(function(e){
      e.preventDefault()
      $.ajax({
          url : $(this).attr("action"),
          type : "POST",
          dataType : 'json',
          data : $(this).serialize(),
          success : function(data){
              if(data.success){
                toastr.success(data.message)
                table.ajax.reload()
                $('#modal-add-user').modal('hide')
                $(this).find(".btn-reset").trigger("click")
              }
          },
          error : function(err){
              toastr.error(err.responseJSON.message ?? "Something went wrong")
          }
      })
    })
  })
</script>
@endsection