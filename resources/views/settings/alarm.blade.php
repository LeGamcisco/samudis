@extends('layouts.app')
@section('title','Settings')
@section('subtitle','Alarm Configuration')
@section('content')

<form action="{{ route('settings.alarm.update',[$alarm->id]) }}" method="POST" class="row gap-2">
    @csrf
    @method("PATCH")
    <div class="card radius-10 col">
        <div class="card-body">
            <div class="d-flex align-items-center ">
                <h6 class="mb-0">Email Configuration</h6>
            </div>
            <div class="my-3 d-flex gap-1 justify-content-between">
                <div class="form-group w-100">
                    <label>Enable Service</label>
                    <div class="form-check form-switch">
                        <input type="hidden" name="enable_email" value="0">
                        <input class="form-check-input" name="enable_email" value="1" type="checkbox" id="enableEmail" {{ $alarm->enable_email == 1 ? 'checked' : '' }}>
                        <label class="form-check-label" for="enableEmail">Enable Email Notification  </label>
                    </div>
                </div>                
            </div>
            <div class="my-3 d-flex gap-1 justify-content-between">
                <div class="form-group w-100">
                    <label>Sent From</label>
                    <input type="text" name="sent_from" value="{{ old("sent_from", $alarm->sent_from) }}" placeholder="Sent From" class="form-control">
                </div>
                <div class="form-group w-100">
                    <label>Protocol <small class="text-secondary">(SMTP only)</small></label>
                    <input type="text" name="protocol" readonly value="{{ old("protocol", $alarm->protocol) }}" placeholder="Protocol" class="form-control">
                </div>
                <div class="form-group w-100">
                    <label>Host</label>
                    <input type="text" name="host" value="{{ old("host", $alarm->host) }}" placeholder="Host" class="form-control">
                </div>
            </div>
            <div class="my-3 d-flex gap-1 justify-content-between">
                <div class="form-group w-100">
                    <label>SMTP User</label>
                    <input type="text" name="smtp_user" value="{{ old("smtp_user", $alarm->smtp_user) }}" placeholder="SMTP User" class="form-control">
                </div>
                <div class="form-group w-100">
                    <label>SMTP Password</label>
                    <input type="password" name="smtp_pass" value="{{ old("smtp_pass", $alarm->smtp_pass) }}" placeholder="SMTP Password" class="form-control">
                </div>
                <div class="form-group w-100">
                    <label>SMTP Port</label>
                    <input type="text" name="smtp_port" value="{{ old("smtp_port", $alarm->smtp_port) }}" placeholder="SMTP Port" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label>Sent To <small class="text-secondary">(add separator comma to add multiple receive)</small></label>
                <textarea name="sent_to" placeholder="Sent To" class="form-control">{{ old("sent_to",$alarm->sent_to) }}</textarea>
            </div>
            
        </div>
    </div>
    <div class="card radius-10 col-md-6">
        <div class="card-body">
            <div class="d-flex align-items-center ">
                <h6 class="mb-0">Telegram Configuration</h6>
            </div>
            <div class="form-group w-100">
                <label>Enable Services</label>
                <div class="form-check form-switch">
                    <input type="hidden" name="enable_telegram" value="0">
                    <input class="form-check-input" name="enable_telegram" value="1" type="checkbox" id="enableTelegram" {{ $alarm->enable_telegram == 1 ? 'checked' : '' }}>
                    <label class="form-check-label" for="enableTelegram">Enable Telegram Notification</label>
                </div>
            </div>
            <div class="my-3 d-flex gap-1 justify-content-between">
                <div class="form-group w-100">
                    <label>Telegram Chat ID</label>
                    <input type="text" name="telegram_chat_id" value="{{ old("telegram_chat_id", $alarm->telegram_chat_id) }}" placeholder="Longitude" class="form-control">
                </div>
                <div class="form-group w-100">
                    <label>Telegram Bot Token</label>
                    <input type="password" name="telegram_bot_token" value="{{ old("telegram_bot_token", $alarm->telegram_bot_token) }}" placeholder="Longitude" class="form-control">
                </div>
            </div>
            <div class="form-group w-100">
                <label>Timeout Execution</label>
                <input type="number" name="timeout" value="{{ old("timeout", $alarm->timeout) }}" placeholder="Longitude" class="form-control">
            </div>
            <div class="d-block d-flex justify-content-end gap-1 py-3">
                <button type="button" onclick="return confirm(`Are you sure?`)" id="btn-test-email" class="btn btn-outline-primary">Test Email</button>
                <button type="button" onclick="return confirm(`Are you sure?`)" id="btn-test-telegram" class="btn btn-outline-primary">Test Telegram</button>
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
<script>
    $(document).ready(function(){
        $("#btn-test-email").click(function(){
            $(this).addClass("disabled");
            $(this).html("Sending...");
            $.ajax({
                url: "{{ route('settings.alarm.test-email') }}",
                type : "POST",
                data : {
                    _token : `{{ csrf_token() }}`
                },
                dataType : "json",
                success : function(data){
                    if(data.success){
                        toastr.success(data.message);
                    }
                    $('#btn-test-email').removeClass("disabled");
                    $('#btn-test-email').html("Test Email");
                },
                error: function(xhr, status, err){
                    toastr.error(xhr?.responseJSON?.message ?? err.toString());
                    $('#btn-test-email').removeClass("disabled");
                    $('#btn-test-email').html("Test Email");
                }
            })
        })
        $("#btn-test-telegram").click(function(){
            $(this).addClass("disabled");
            $(this).html("Sending...");
            $.ajax({
                url: "{{ route('settings.alarm.test-telegram') }}",
                type : "POST",
                data : {
                    _token : `{{ csrf_token() }}`
                },
                dataType : "json",
                success : function(data){
                    if(data.success){
                        toastr.success(data.message);
                        $('#btn-test-telegram').removeClass("disabled");
                        $('#btn-test-telegram').html("Test Telegram");
                    }
                    
                },
                error: function(xhr, status, err){
                    toastr.error(xhr?.responseJSON?.message ?? err.toString());
                    $('#btn-test-telegram').removeClass("disabled");
                    $('#btn-test-telegram').html("Test Telegram");
                }
            })
        })
    })
</script>

@endsection