@extends('layouts.app')
@section('title','Database')
@section('subtitle','Backup Database')
@section('header-option')
@endsection
@section('content')
<div class="card radius-10 w-100">
    <div class="card-body">
      <div class="d-flex align-items-center justify-content-between">
        <h6 class="mb-0">Backup Database</h6>
        <span>
          <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-restore" class="btn btn-sm btn-outline-primary">
             Restore
          </a>
          <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-backup" class="btn btn-sm btn-primary">
             Create New Backup
          </a>
        </span>
      </div>
      <div class="table-responsive mt-2">
        <table class="table align-middle mb-0" id="table-databases" style="width: 100%">
          <thead class="table-light">
            <tr>
              <th class="text-center">File</th>
              <th class="text-center">Size <small class="text-secondary">(Mb)</small></th>
              <th class="text-center">Last Update</th>
              <th class="fs-5 text-center" width="10%">

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
{{-- Modal Backup --}}
<div class="modal fade" id="modal-backup" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Create New Backup</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="form-backup" action="{{ route("database.backup.backup") }}" enctype="multipart/form-data" method="POST" id="form-add-stack">
          @csrf
          <p class="alert alert-info d-flex align-items-center gap-3">
            <span class="fs-1">
              <ion-icon name="alert-circle-outline"></ion-icon>
            </span>
            <span>
              The database backup process may take some time. <strong>Please wait until the backup process is complete</strong>.
            </span>
          </p>
          <div class="d-flex justify-content-between my-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="reset" class="d-none">Reset</button>
            <button type="submit" class="btn btn-primary">Execute Backup</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
{{-- Modal New --}}
<div class="modal fade" id="modal-restore" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Restore</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="{{ route("database.backup.restore") }}" enctype="multipart/form-data" method="POST" id="form-restore">
          @csrf
          <p class="alert alert-warning d-flex align-items-center gap-3">
            <span class="fs-1">
              <ion-icon name="alert-circle-outline"></ion-icon>
            </span>
            <span>
              Doing a data restore can delete the existing data and restore the data to be restored. <strong>Make sure the SQL format comes from Samu DIS </strong>
            </span>
          </p>
          <div class="form-group mb-3">
            <label>Choose SQL File</label>
            <input type="file" name="file" class="form-control">
          </div>
          <div class="d-flex justify-content-between my-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="reset" class="d-none">Reset</button>
            <button type="submit" class="btn btn-primary">Restore</button>
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
@if ($errors->any())
<script>
    @foreach ($errors->all() as $error)
      toastr.error(`{{ $error }}`)
    @endforeach
</script>
@endif
<script>
  $(document).ready(function(){
    const table = $("#table-databases").DataTable({
      theme : "boostrap5",
      serverSide: true,
      processing: true,
      ajax: {
        url : `{{ route("database.backup.datatable") }}`
      },
      columns : [
        {data:'file'},
        {data:'size'},
        {data:'updated_at'},
        {
          render:function(data,type,row){
            return `<div class="d-flex align-items-center gap-3 fs-6">
              <a href="${row.download}" class="text-primary btn-view">
                <ion-icon name="cloud-download-outline"></ion-icon> Download File
              </a>
              <a href="#" data-name="${row.file}" data-id="${row.hash}" class="text-danger btn-delete">
                <ion-icon name="trash-sharp"></ion-icon>
              </a>
            </div>`
          }
        }
      ]
    })
    // Filter Action

    $(document).delegate('.btn-delete','click',function(){
        const name = $(this).data("name")
        const id = $(this).data("id")
        Swal.fire({
          title: `Do you want to delete ${name}?`,
          text : `Data will be deleted permanent and you won't be able to revert this action after you click confirm`,
          showDenyButton: true,
          showCancelButton: false,
          confirmButtonText: 'Confirm',
          denyButtonText: `Cancel Delete`,
        }).then((result) => {

          if (result.isConfirmed) {
            $.ajax({
              url : `{{ route('database.backup.index') }}/${id}`,
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
    $('#form-backup').submit(function(e){
      function enableButton(){
        $("#form-backup button[type='submit']").prop("disabled",false)
        $("#form-backup button[type='submit']").html("Execute Backup")
      }
      $("#form-backup button[type='submit']").prop("disabled",true)
      $("#form-backup button[type='submit']").html("Please wait...")
      e.preventDefault()
      $.ajax({
          url : `{{ route('database.backup.backup') }}`,
          type : 'POST',
          dataType : 'json',
          data : $(this).serialize(),
          success : function(data){
            if(data.success){
              toastr.success(data.message)
              table.ajax.reload()
              enableButton()
              $("#modal-backup").modal("hide")

            }
          },
          error : function(data){
            data.responseJSON.errors.map(function(error){
              toastr.error(error)
            })
            enableButton()
          }
      })
    })
    $("#form-restore button[type='submit']").click(function(e){
      e.preventDefault()
      Swal.fire({
          title: `Are you sure you want to restore??`,
          text : ` Recent data may be lost and replaced with data to be restored. Make sure you import the SQL format downloaded from Samu DIS`,
          showDenyButton: true,
          showCancelButton: false,
          confirmButtonText: 'Confirm',
          denyButtonText: `Cancel Restore`,
        }).then((result) => {
          $('#form-restore').submit()
          $("#form-restore button[type='submit']").prop("disabled",true)
          $("#form-restore button[type='submit']").html("Uploading file...")
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

  })
</script>
@endsection
