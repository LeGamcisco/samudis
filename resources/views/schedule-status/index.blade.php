@extends('layouts.app')
@section('title','Settings')
@section('subtitle','Schedule Status')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <label class="form-label fw-bold">Schedule Status</label>
                    <button type="button" data-bs-toggle="modal" data-bs-target="#modal-create" class="btn btn-sm btn-primary">Create New</button>
                </div>
                <hr class="p-0 m-0 mb-3">
                <div class="table-responsive">
                    <table id="table" class="table table-sm align-middle mb-0" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 1rem"></th>
                                <th>Parameter</th>
                                <th>Status</th>
                                <th>Start At</th>
                                <th>End At</th>
                                <th>Reason</th>
                                <th>Created By</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@section('modal')
<div class="modal fade" id="modal-create" tabindex="-1" aria-labelledby="modal-createLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modal-createLabel">Create New Schedule Status Parameter</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form action="{{ route('settings.schedule-status.store') }}" id="form-create" method="post">
                @csrf
                <div class="mb-3 form-group">
                    <label class="form-label fw-bold">Parameter</label>
                    <select name="parameter_id[]" data-placeholder="Choose Parameter" multiple class="form-control" required>
                        <option value=""></option>
                    </select>
                </div>
                <div class="mb-3 form-group">
                    <label class="form-label fw-bold">Status</label>
                    <select name="status_id" class="form-control" required>
                        <option value=""> Choose Status</option>
                        @foreach ($statuses as $status)
                            <option {{ old('status_id') == $status->id ? 'selected' : '' }} value="{{ $status->id }}">{{ $status->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3 form-group d-flex justify-content-between gap-2">
                    <div class="col">
                        <label class="form-label fw-bold">Start At</label>
                        <input type="datetime-local" value="{{ old('start_at') }}" name="start_at" class="form-control">
                    </div>
                    <div class="col">
                        <label class="form-label fw-bold">End At</label>
                        <input type="datetime-local" value="{{ old('end_at') }}" name="end_at" class="form-control">
                    </div>
                </div>
                <div class="mb-3 form-group">
                    <label class="form-label fw-bold">Description <small class="text-muted">(optional)</small></label>
                    <textarea name="description" placeholder="Reason to change status parameter " class="form-control">{{ old('description') }}</textarea>
                </div>
                <div class="mb-3 d-flex justify-content-end">
                    <button type="reset" class="d-none"></button>
                    <button type="submit" class="btn btn-primary">Add New</button>
                </div>
            </form>
        </div>
      </div>
    </div>
</div>
<div class="modal fade" id="modal-edit" tabindex="-1" aria-labelledby="modal-createLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modal-createLabel">Edit Schedule Status Parameter</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form action="" id="form-edit" method="post">
                @csrf
                @method("PATCH")
                <div class="mb-3 form-group">
                    <label class="form-label fw-bold">Parameter</label>
                    <input type="text" name="parameter_id" readonly class="form-control">
                </div>
                <div class="mb-3 form-group">
                    <label class="form-label fw-bold">Status</label>
                    <select name="status_id" class="form-control" required>
                        <option value="">Choose Status</option>
                        @foreach ($statuses as $status)
                            <option {{ old('status_id') == $status->id ? 'selected' : '' }} value="{{ $status->id }}">{{ $status->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3 form-group d-flex justify-content-between gap-2">
                    <div class="col">
                        <label class="form-label fw-bold">Start At</label>
                        <input type="datetime-local" value="{{ old('start_at') }}" name="start_at" class="form-control">
                    </div>
                    <div class="col">
                        <label class="form-label fw-bold">End At</label>
                        <input type="datetime-local" value="{{ old('end_at') }}" name="end_at" class="form-control">
                    </div>
                </div>
                <div class="mb-3 form-group">
                    <label class="form-label fw-bold">Reason <small class="text-muted">*</small></label>
                    <textarea name="description" required placeholder="Reason to change status parameter " class="form-control">{{ old('description') }}</textarea>
                </div>
                <div class="mb-3 d-flex justify-content-end">
                    <button type="reset" class="d-none"></button>
                    <button type="submit" class="btn btn-primary">Add New</button>
                </div>
            </form>
        </div>
      </div>
    </div>
</div>
@endsection
@endsection
@section('css')
<link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap-5/select2-bootstrap-5-theme.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}">
<link rel="stylesheet" href="{{ asset("assets/plugins/datatable/css/dataTables.bootstrap5.min.css") }}">
@endsection
@section('js')
@if (session()->has("success"))
<script>
    toastr.success("{{ session()->get("success") }}");
</script>
@endif
@if ($errors->any())

<script>
    {!! implode('', $errors->all('toastr.error(":message");')) !!}
</script>    
@endif
<script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
<script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset("assets/plugins/datatable/js/jquery.dataTables.min.js") }}"></script>
<script src="{{ asset("assets/plugins/datatable/js/dataTables.bootstrap5.min.js") }}"></script>
<script>
    $(document).ready(function(){
        const select2 = $('#form-create select[name="parameter_id[]"]').select2({
            theme: "bootstrap-5",
            placeholder: "Choose Parameter",
            dropdownParent: $('#form-create'),
            ajax : {
                url: "{{ route('settings.parameter.select2') }}",
                dataType: 'json',
                processResults: function(data){
                    return {
                        results: data.map(item => {
                            return {
                                id: item.parameter_id,
                                text : `[${item?.stack?.code}] ${item.name}`
                            }
                        })
                    }
                }
            }
        })

        const table = $("#table").DataTable({
            theme : "bootstrap5",
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('settings.schedule-status.datatable') }}",
                data: function(req){
                    req.stack_id = $("#form-filter select[name='stack_id']").val();
                    req.parameter_id = $("#form-filter select[name='parameter_id']").val();
                }
            },
            order : [
                [3, 'desc']
            ],
            columns: [
                {
                    render : function(data,type,row){
                        return `
                        <div class="d-flex justify-content-between gap-3 fs-6">
                            <a href="javascript:void(0)" data-id="${row.id}" class="text-info btn-edit">
                                <ion-icon name="pencil-outline"></ion-icon>
                            </a>
                            <a href="javascript:void(0)" data-id="${row.id}" class="text-danger btn-delete">
                                <ion-icon name="trash-outline"></ion-icon>
                            </a>
                        </div>
                        `
                    },
                    data : 'id',
                    orderable : false,
                    width : '1rem',
                },
                {
                    render : function(data,type,row){
                        return `[${row?.parameter?.stack?.code}] ${row?.parameter?.name}`
                    },
                    data : 'parameter_id'
                },
                {
                    render : function(data,type,row){
                        switch (row?.status?.id) {
                            case 1: 
                            default:
                                return `<span class="badge bg-success text-white">${row?.status?.name}</span>`
                            break;
                            case 2: 
                                return `<span class="badge bg-warning text-white">${row?.status?.name}</span>`
                            break;
                            case 3: 
                                return `<span class="badge bg-info text-white">${row?.status?.name}</span>`
                            break;
                            case 4: 
                                return `<span class="badge bg-dark text-white">${row?.status?.name}</span>`
                            break;
                        }
                    },
                    data : 'status_id'
                },
                {
                    data : 'start_at'
                },
                {
                    data : 'end_at'
                },
                {
                    data : 'description'
                },
                {
                    render : function(data,type,row){
                        return row?.user?.name
                    },
                    data : 'user_id'
                },
            ]
        })

        $('#form-create,#form-edit').submit(function(e){
            e.preventDefault()
            $.ajax({
                url : $(this).attr('action'),
                type : $(this).attr('method'),
                dataType : 'json',
                data : $(this).serialize(),
                success : function(data){
                    if(data.success){
                        table.ajax.reload()
                        $('#form-create').find("button[type='reset']").trigger('click')
                        $('#form-edit').find("button[type='reset']").trigger('click')
                        $('#modal-create').modal('hide')
                        $('#modal-edit').modal('hide')
                        return toastr.success(data.message)
                    }
                    return toastr.error(data?.message)
                },
                error : function(xhr, status, err){
                    return toastr.error(xhr?.responseJSON?.message)
                }
            })
        })

        $(document).delegate('.btn-edit','click',function(){
            const id = $(this).data("id")
            $.ajax({
                url : `{{ route('settings.schedule-status.index') }}/${id}`,
                success : function(data){
                    $("#form-edit").attr("action",`{{ route('settings.schedule-status.index') }}/${id}`)
                    $("#form-edit input[name='parameter_id']").val(data.parameter.name)
                    $("#form-edit input[name='start_at']").val(data.start_at)
                    $("#form-edit input[name='end_at']").val(data.end_at)
                    $("#form-edit select[name='status_id']").val(data.status_id)
                    $("#form-edit textarea[name='description']").val(data.description)
                    $('#modal-edit').modal('show')
                }
            })
        })

        $(document).delegate('.btn-delete','click', function(){
            const id = $(this).data("id")
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if(result.isConfirmed){
                    $.ajax({
                        url : `{{ route('settings.schedule-status.index') }}/${id}`,
                        type : "DELETE",
                        data : {
                            _token : `{{ csrf_token() }}`
                        },
                        success : function(data){
                            table.ajax.reload()
                            return toastr.success(data.message)
                        },
                        error : function(xhr, status, err){
                            return toastr.error(xhr?.responseJSON?.message)
                        }
                    })
                }
            })
        })
    })
</script>

@endsection