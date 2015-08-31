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
    <br/>
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title">
                        Categories
                    </div>
                </div>
                <div class="panel-body">
                    <div>
                        <a class="btn btn-success" href="{{route('adminCategoriesAddEdit')}}"><i class="fa fa-plus"></i> Create category</a>
                    </div>
                    {{ $grid }}
                </div>
            </div>
        </div>
    </div>
@stop