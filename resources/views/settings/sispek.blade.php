@extends('layouts.app')
@section('title','Settings')
@section('subtitle','SISPEK Configuration')
@section('header-option')
<div class="btn-group">
  <a href="{{ route("settings.configuration.index") }}" class="btn btn-primary text-white"><ion-icon name="arrow-back-circle-outline"></ion-icon> Go Back</a>
</div>
@endsection
@section('content')
<div class="row gap-2 alert alert-danger align-items-center">
    <span class="col-1 text-center">
        <ion-icon name="warning-outline" class="fs-1"></ion-icon>
    </span>
    <span class="col">
        <h6 class="mb-0">Danger Zone</h6>
        <p>Careful, these actions are not reversible!</p>
    </span>
</div>
<form action="{{ route('settings.sispek.update',[1]) }}" method="POST" class="row gap-2">
    @csrf
    @method("PATCH")
    <div class="card radius-10 col">
        <div class="card-body">
            <div class="d-flex align-items-center ">
                <h6 class="mb-0">SISPEK Configuration</h6>
            </div>
            <div class="my-2 form-group w-100">
                <label>Base URL</label>
                <input type="text" name="server" value="{{ old("server", $sispek->server) }}" placeholder="Base URL" class="form-control">
            </div>
            <div class="my-3 d-flex gap-1 justify-content-between">
                <div class="form-group w-100">
                    <label>APP ID</label>
                    <input type="text" name="app_id" value="{{ old("app_id", $sispek->app_id) }}" placeholder="APP ID" class="form-control">
                </div>
                <div class="form-group w-100">
                    <label>APP Secret</label>
                    <input type="password" name="app_secret" value="{{ old("app_secret", $sispek->app_secret) }}" placeholder="APP Secret" class="form-control">
                </div>
            </div>
            <div class="my-3 d-flex gap-1 justify-content-between">
                <div class="form-group w-100">
                    <label>URL Get Token</label>
                    <input type="text" name="api_get_token" value="{{ old("api_get_token", $sispek->api_get_token) }}" placeholder="URL Get Token" class="form-control">
                </div>
                <div class="form-group w-100">
                    <label>URL Get Stack</label>
                    <input type="text" name="api_get_kode_cerobong" value="{{ old("api_get_kode_cerobong", $sispek->api_get_kode_cerobong) }}" placeholder="URL Get Stack" class="form-control">
                </div>
            </div>
            <div class="my-3 d-flex gap-1 justify-content-between">
                <div class="form-group w-100">
                    <label>URL Get Parameter</label>
                    <input type="text" name="api_get_parameter" value="{{ old("api_get_parameter", $sispek->api_get_parameter) }}" placeholder="URL Get Parameter" class="form-control">
                </div>
                <div class="form-group w-100">
                    <label>URL Submit Data</label>
                    <input type="text" name="api_post_data" value="{{ old("api_post_data", $sispek->api_post_data) }}" placeholder="URL Submit Data" class="form-control">
                </div>
            </div>
            <div class="my-3 d-flex gap-1 justify-content-between">
                <div class="form-group w-100">
                    <label>Current Token</label>
                    <input type="text" disabled name="token" value="{{ old("token", $sispek->token) }}" placeholder="Token" class="form-control">
                </div>
                <div class="form-group w-100">
                    <label>Token Expired</label>
                    <input type="date" disabled name="token_expired" value="{{ old("token_expired", date("Y-m-d", strtotime($sispek->token_expired))) }}" class="form-control">
                </div>
            </div>
            <div class="form-group d-flex justify-content-end">
                <button type="submit" class="btn btn-info text-white">Save Changes</button>

            </div>
        </div>
    </div>
    <div class="card radius-10 col-md-3">
        <div class="card-body">
            <div class="d-flex align-items-center ">
                <h6 class="mb-0">SISPEK Tools</h6>
            </div>
            <div class="d-block d-flex flex-column gap-2 justify-content-end py-3">
                <button type="button" id="test-ping" class="btn btn-outline-primary">TEST PING SISPEK</button>
                <button type="button" id="get-stack-code" class="btn btn-outline-primary">Get Stack Code</button>
                <div class="row gap-0">
                    <div class="col">
                        <input type="text" id="code_cerobong" name="code_cerobong" placeholder="Stack Code" class="form-control form-control-sm">
                    </div>
                    <div class="col-6">
                        <button type="button" id="get-parameter" class="w-100 btn btn-sm btn-outline-primary">Get Params.</button>
                    </div>
                </div>
                <textarea name="response" id="response" disabled placeholder="Response" rows="7" class="mt-3 form-control"></textarea>
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
<script>
    $('#test-ping').click(function(){
        $.ajax({
            url: "{{ route('settings.sispek.test-ping') }}",
            dataType : "json",
            success : function(data){
                if(data.success){
                    if(data.data.status == 1){
                        toastr.success(`Samu DIS can reach SISPEK server!`);
                    }else{
                        toastr.error(`Samu DIS cant reach SISPEK server! Please check your network firewall!`);
                    }
                    $("#response").val(`Samu DIS can reach SISPEK server!`);
                }
            },
            error : function(xhr, status, err){
                toastr.error(err);
            }
        })
    })
    $("#get-stack-code").click(function(){
        $.ajax({
            url : `{{ route('settings.sispek.stack-code') }}`,
            type : 'POST',
            dataType : 'json',
            data : {
                _token : "{{ csrf_token() }}",
            },
            success : function(data){
                $("#response").html(``)
                if(data.success){
                    if(data?.data?.cerobong?.length > 0){
                        let stacks = ``
                        data.data.cerobong.map(function(stack){
                            stacks += `${stack.kode_cerobong}\n`
                        })
                        $("#response").val(stacks)
                    }else{
                        $("#response").val(data?.data?.message)
                    }
                }
            },
            error : function(xhr, status, err){
                toastr.error(xhr?.responseJSON?.message ?? err.toString());
            }
        })
    })
    $("#get-parameter").click(function(){
        const stackCode = $("#code_cerobong").val()
        if(stackCode.length == 0){
            return toastr.error("Stack Code is required!");
        }
        $.ajax({
            url : `{{ route('settings.sispek.parameter-code') }}`,
            type : 'post',
            dataType : 'json',
            data : {
                _token : "{{ csrf_token() }}",
                code_cerobong : stackCode
            },
            success : function(data){
                $("#response").val(``)
                if(data.success){
                    let parameters = ``
                    data.data.parameter.map(function(parameter){
                        parameters += `${parameter.nama}\n`
                    })
                    $("#response").val(parameters)
                }
            },
            error : function(xhr, status, err){
                toastr.error(xhr?.responseJSON?.message ?? err.toString());
            }
        })
    })
</script>
@endsection
