@extends('layouts.app')
@section('title','Settings')
@section('subtitle','Parameter Status')
@section('header-option')
<div class="btn-group">
  <button type="button" class="btn btn-outline-primary">{{ $stack->code }}</button>
  <button type="button"
    class="btn btn-outline-primary split-bg-primary dropdown-toggle dropdown-toggle-split"
    data-bs-toggle="dropdown"> <span class="visually-hidden">Toggle Dropdown</span>
  </button>
  <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end">
    @foreach ($stacks as $item)
    <a class="dropdown-item" href="{{ url("settings/parameter/{$item->id}") }}">{{ $item->code }}</a>
    @endforeach
  </div>
</div>
@endsection
@section('content')

<div class="card radius-10 w-100">
    <div class="card-body">
      <div class="d-flex align-items-center justify-content-between">
        <h6 class="mb-0">Parameter Status</h6>
      </div>
      <div class="table-responsive mt-2">
        <table class="table table-sm align-middle mb-0" id="table-parameter-status" style="width: 100%">
          <thead class="table-light">
            <tr>
              <th class="text-center">#ID</th>
              <th class="text-center">Stack</th>
              <th class="text-center">Parameter</th>
              <th class="text-center">Last Sent</th>
              <th class="text-center">Status</th>
              <th class="fs-5 text-center">
                <a href="javascript::void()" id="btn-select-all" class="text-warning"  data-checked="false">
                    <ion-icon name="checkbox-outline"></ion-icon>
                    {{-- <ion-icon name="checkbox"></ion-icon> --}}
                  </a>
              </th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
      <form action="{{ route('settings.parameter.update') }}" id="form-update-status" method="PATCH" class="my-3">
        @method('PATCH')
        @csrf
        <div class="row align-items-end justify-content-between">
          <div class="form-group">
            <input type="hidden" name="parameter_ids" class="form-control">
            <label class="fw-bold">Mass Update to</label>
            <select name="status_id" class="form-control form-control-sm form-select-sm form-select">
              <option value="">Select Status</option>
              <option value="1">Normal</option>
              <option value="2">Abnormal</option>
              <option value="3">Cal. Test</option>
              <option value="4">Broken</option>
            </select>
          </div>
          <div class="mb-3 form-group">
              <label class="form-label fw-bold">Reason <small class="text-muted">*</small></label>
              <textarea name="description" required placeholder="Reason to change status parameter " class="form-control">{{ old('description') }}</textarea>
          </div>
          <div class="form-group offset-md-10 col-md-2">
            <button class="btn btn-primary btn-sm text-white w-100">Save Changes</button>
          </div>
        </div>
      </form>
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
    const table = $("#table-parameter-status").DataTable({
      dom : `<"d-flex flex-md-row flex-column justify-content-between align-items-center"lip>t<"bottom"><"clear">`,
      serverSide: true,
      processing: true,
      ajax : {
        url : "{{ route('settings.parameter.datatable',[$stackId]) }}",
      },
      columns : [
        {
          className : 'text-center',
          data : "id"},
        {
          className : 'text-center',
          data : "stack.code"},
        {
          className : 'text-center',
          data : "name"},
        {
          className : 'text-center',
          render : function(data,type,row){
            return `-`
          }
        },
        {
          className : 'text-center',
          render: function(data,type,row){
            return `<span class="badge badge-alert bg-${row.status_id == 1 ? 'success' : row.status_id == 4 ? 'danger' : 'info' }">${row.status.name}</span>`
          },
          data : "status.id"
        },
        {
          className : 'text-center',
          width : "10%",
          orderable: false,
          render : function(data,type,row){
            return `<div class="d-flex align-items-center gap-3 fs-5">
                      <span class="checkbox-custom text-warning" data-checked="false" data-id="${row.id}">
                        <ion-icon name="checkbox-outline"></ion-icon>
                      </span>
                    </div>`
          },
          data : "id"},
      ]
    })
    function selectParameter(){
      let parameterIdStr = ``
      $('.checkbox-custom').each(function(index, el){
        let id = $(el).data('id')
        let checked = $(el).data('checked')
        if(checked){
          parameterIdStr += `${id},`
        }
      })
      parameterIdStr = parameterIdStr.slice(0, -1)
      $(`input[name='parameter_ids']`).val(parameterIdStr)
    }
    $('#btn-select-all').click(function(e){
      e.preventDefault()
      let checked = $(this).data('checked')
      let checkedIcon = `<ion-icon name="checkbox"></ion-icon>`
      let uncheckIcon = `<ion-icon name="checkbox-outline"></ion-icon>`
      $(this).data('checked',!checked)
      if(checked == false){
        $(this).html(checkedIcon)
        $('.checkbox-custom').html(checkedIcon)
        $('.checkbox-custom').data('checked',!checked)
      }else{
        $(this).html(uncheckIcon)
        $('.checkbox-custom').html(uncheckIcon)
        $('.checkbox-custom').data('checked',!checked)
      }
      selectParameter()      
    })
    $(document).delegate('.checkbox-custom','click',function(){
      let checked = $(this).data('checked')
      let checkedIcon = `<ion-icon name="checkbox"></ion-icon>`
      let uncheckIcon = `<ion-icon name="checkbox-outline"></ion-icon>`
      $(this).data('checked',!checked)
      if(checked == false){
        $(this).html(checkedIcon)
      }else{
        $(this).html(uncheckIcon)
      }
      selectParameter()
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
              setTimeout(() => {
                $('#btn-select-all').trigger("click")
                selectParameter()
              }, 800);
            }
          },
          error : function(data){
            data.responseJSON.errors.map(function(error){
              toastr.error(error)
            })
          }
      })
    })
  })
</script>
@endsection