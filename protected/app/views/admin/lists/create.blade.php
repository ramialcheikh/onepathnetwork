@extends('admin/layout')


@section('content')

    <h1 class="page-header">
        @if($creationMode) Create a @elseif($editingMode) Edit @endif
        list</h1>

    @if(!empty($duplicateList))
    <div class="alert alert-success">Duplicating list <b>"{{$duplicateList->topic}}"</b></div>
    @endif

            <!-- Item creation stuff here -->

@stop


@section('foot')
    @parent

    <script src="{{asset('js/admin/admin.js')}}"></script>

@stop


