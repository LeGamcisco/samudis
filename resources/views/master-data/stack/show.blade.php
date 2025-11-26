@extends('layouts.app')
@section('title','Master Data')
@section('subtitle','Stack')
@section('header-option')
<span>
    <a href="{{ route("master.stack.index") }}" class="btn btn-outline-secondary">
        <ion-icon name="chevron-back-outline"></ion-icon> Back
    </a>
  </span>
@endsection
@section('content')

<div class="row gap-2">
    <div class="col card radius-10 w-50">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between">
            <h6 class="mb-0">Detail Stack</h6>
          </div>
          <div class="row">
            <div class="col-md-6 d-flex mb-3 flex-column align-items-start">
                <span class="fs-6">Stack Name</span>
                <span class="fs-7 fw-bold">{{ $stack->code }}</span>
            </div>
            <div class="col-md-6 d-flex mb-3 flex-column align-items-start">
                <span class="fs-6">Chimney SISPEK Code</span>
                <span class="fs-7 fw-bold">{{ $stack->sispek_code }}</span>
            </div>
            <div class="col-md-4 d-flex mb-3 flex-column align-items-start">
                <span class="fs-6">Height <small class="text-secondary">(m)</small></span>
                <span class="fs-7 fw-bold">{{ $stack->height }}</span>
            </div>
            <div class="col-md-4 d-flex mb-3 flex-column align-items-start">
                <span class="fs-6">Diameter <small class="text-secondary">(m)</small></span>
                <span class="fs-7 fw-bold">{{ $stack->diameter }}</span>
            </div>
            <div class="col-md-4 d-flex mb-3 flex-column align-items-start">
                <span class="fs-6">Flow</span>
                <span class="fs-7 fw-bold">{{ $stack->flow }}</span>
            </div>
            <div class="col-md-4 d-flex mb-3 flex-column align-items-start">
                <span class="fs-6">O2 Reference</span>
                <span class="fs-7 fw-bold">{{ $stack->oxygen_reference }} %</span>
            </div>
            <div class="col-md-4 d-flex mb-3 flex-column align-items-start">
                <span class="fs-6">Latitude</span>
                <span class="fs-7 fw-bold">{{ $stack->lat }}</span>
            </div>
            <div class="col-md-4 d-flex mb-3 flex-column align-items-start">
                <span class="fs-6">Longitude</span>
                <span class="fs-7 fw-bold">{{ $stack->lon }}</span>
            </div>
          </div>
          
        </div>
    </div>
    <div class="col-md-6 card radius-10 w-50">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between">
            <h6 class="mb-0"><a href="{{ route("settings.parameter.index") }}/{{ $stack->id }}">List Parameter {{ $stack->code }}</a></h6>
          </div>
          <div class="table-responsive">
            <table class="table">
                <thead>
                    <th>Parameter</th>
                    <th>Parameter SISPEK Code</th>
                    <th>Unit</th>
                </thead>
                <tbody>
                    @foreach ($stack->parameters as $parameter)
                        <tr>
                            <td>{{ $parameter->name }}</td>
                            <td>{{ $parameter->sispek_code }}</td>
                            <td>{{ $parameter->unit->name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
          </div>
          
        </div>
    </div>
</div>
@endsection