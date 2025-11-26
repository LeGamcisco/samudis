@extends('layouts.app')
@section('title','Settings')
@section('subtitle','Main Configuration')
@section('header-option')
<div class="btn-group">
  <a href="{{ route("settings.sispek.index") }}" class="btn btn-warning text-white">SISPEK Configuration</a>
</div>
@endsection
@section('content')

<form action="{{ route('settings.configuration.update',[$config->id]) }}" method="POST" class="row gap-2">
    @csrf
    @method("PATCH")
    <div class="card radius-10 col">
        <div class="card-body">
            <div class="d-flex align-items-center ">
                <h6 class="mb-0">Company Information</h6>
            </div>
            <div class="my-3 d-flex gap-1 justify-content-between">
                <div class="form-group w-100">
                    <label>Samu DIS Code</label>
                    <input type="text" name="egateway_code" value="{{ old("egateway_code", $config->egateway_code) }}" placeholder="Samu Code" class="form-control">
                </div>
                <div class="form-group w-100">
                    <label>Company Name</label>
                    <input type="text" name="customer_name" value="{{ old("customer_name", $config->customer_name) }}" placeholder="Company Name" class="form-control">
                </div>
            </div>
            <div class="my-3 d-flex gap-1 justify-content-between">
                <div class="form-group w-100">
                    <label>City</label>
                    <input type="text" name="city" value="{{ old("city", $config->city) }}" placeholder="City" class="form-control">
                </div>
                <div class="form-group w-100">
                    <label>Province</label>
                    <input type="text" name="province" value="{{ old("province", $config->province) }}" placeholder="Province" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label>Full Address</label>
                <textarea name="address" placeholder="Address" class="form-control">{{ old("address",$config->address) }}</textarea>
            </div>
            <div class="my-3 d-flex gap-1 justify-content-between">
                <div class="form-group w-100">
                    <label>Latitude</label>
                    <input type="text" name="lat" value="{{ old("lat", $config->lat) }}" placeholder="Latitude" class="form-control">
                </div>
                <div class="form-group w-100">
                    <label>Longitude</label>
                    <input type="text" name="lon" value="{{ old("lon", $config->lon) }}" placeholder="Longitude" class="form-control">
                </div>
            </div>
        </div>
    </div>
    <div class="card radius-10 col-md-6">
        <div class="card-body">
            <div class="d-flex align-items-center ">
                <h6 class="mb-0">Sofware Configuration</h6>
            </div>
            <p class="alert alert-info alert-dismissible fade show" role="alert">
               <span>
                    @php
                        $totalSpace = disk_total_space("/");
                        $freeSpace = disk_free_space("/");
                        $usage = $totalSpace - $freeSpace;
                        $percentage = $usage/$totalSpace *100;
                    @endphp
                    <strong>Disk Space</strong> <br>
                    Total : {{ HumanSize($totalSpace) }} <br>
                    Free Space : {{ HumanSize($freeSpace) }} <br>
                    <span class="d-block p-0 w-100 bg-secondary text-left rounded overflow-hidden" style="height: 24px" title="{{ round($percentage,2)."% : ".HumanSize($usage) }}">
                        <span style="width: {{ $percentage }}%; display: inline-block; overflow: hidden" class="px-3 py-1 text-white bg-{{ $percentage <= 50 ? "success" : ($percentage <= 90 ? "warning" : "danger") }}"><small>{{ HumanSize($usage) }}</small></span>
                    </span>
                    <strong>Database</strong> <br>
                    Database Usage : {{ $dbUsage }} <br>
                    <strong>PHP Information</strong> <br>
                    PHP Version : {{ phpversion() }} <br>
                    Memory Limit : {{ ini_get("memory_limit") }} <br>
                    Max. Execution Time : {{ ini_get("max_execution_time") == 0 ? "Unlimited" : ini_get("max_execution_time") }} <br>
                    Max. File Upload : {{ ini_get("max_file_uploads") }}
               </span>
               <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </p>
            <div class="my-3 d-flex gap-1 justify-content-between">
                <div class="form-group w-100">
                    <label>Interval Raw Data</label>
                    <select name="interval_das_logs" class="form-select">
                        @for ($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}" {{ $i == $config->interval_das_logs ? "selected" : "" }}>{{ $i }} mins</option>
                        @endfor
                    </select>
                </div>
                <div class="form-group w-100">
                    <label>Interval Measurement Averaging</label>
                    <select name="interval_average" class="form-select">
                        @for ($i = 1; $i <= 60; $i++)
                        <option value="{{ $i }}" {{ $i == $config->interval_average ? "selected" : "" }}>{{ $i }} mins</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="my-3 d-flex gap-1 justify-content-between">
                <div class="form-group w-100">
                    <label>Manual Backup</label>
                    <div class="form-check form-switch">
                        <input type="hidden" name="manual_backup" value="0">
                        <input class="form-check-input" name="manual_backup" value="1" type="checkbox" id="enableManualBackup" {{ $config->manual_backup == 1 ? 'checked' : '' }}>
                        <label class="form-check-label" for="enableManualBackup">Enable Manual Backup DB</label>
                    </div>
                </div>
                <div class="form-group w-100">
                    <label>Automatic Backup DB Day</label>
                    <select name="day_backup" class="form-select">
                        <option value="">Select Date</option>
                        @for ($i = 1; $i <= 28; $i++)
                        <option value="{{ $i }}" {{ $i == $config->day_backup ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>DBMS Path</label>
                <textarea name="mysql_path" placeholder="C:/Program Files/PostgreSQL/15/bin" class="form-control">{{ old("mysql_path", $config->mysql_path) }}</textarea>
            </div>
            <div class="form-group">
                <label>Software Path</label>
                <textarea name="main_path" placeholder="C:/eGateway" class="form-control">{{ old("main_path", $config->main_path) }}</textarea>
            </div>
            <div class="d-block d-flex justify-content-end py-3">
                <button type="submit" class="btn btn-info text-white">Save Changes</button>

            </div>
        </div>
    </div>
</form>
@endsection
@section('css')
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

@endsection
