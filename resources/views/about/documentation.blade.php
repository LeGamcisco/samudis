@extends('layouts.app')
@section('title','About Samu DIS')
@section('subtitle','Documentation')
@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title">Documentation Software Samu DIS</h5>
    </div>
    <div class="card-body">
       <embed class="w-100" style="min-height: 29.9rem" type="application/pdf" src="{{ asset("assets/docs/eGateway v.3 Documentation_INA.pdf") }}" frameborder="0"></embed>
    </div>
</div>
@endsection
