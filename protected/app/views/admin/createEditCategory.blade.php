@extends('admin/layout')

@section('head')
    @parent
    {{ Rapyd::head() }}
    <style>
        .btn-toolbar h2 {
            margin-top: 0px;
        }
    </style>
@show

@section('content')
    <div class="row">
        <div class="col-md-6">
            <br/>
            {{ $edit->header }}
            <div class="well">

                    {{ $edit->message }}

                    @if(!$edit->message)
                        Name: {{ $edit->field('name') }}
                        <p class="bg-danger">{{ $edit->field('name')->message }}</p>

                        Meta title: {{ $edit->field('meta_title') }}
                        <p class="bg-danger">{{ $edit->field('meta_title')->message }}</p>

                        Meta description: {{ $edit->field('meta_description') }}
                        <p class="bg-danger">{{ $edit->field('meta_description')->message }}</p>

                    @endif
                    {{ $edit->footer }}
            </div>
        </div>
    </div>
@stop