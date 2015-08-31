@extends('admin/layout')


@section('content')

    <h1 class="page-header">
        @if($creationMode) Create a @elseif($editingMode) Edit @endif
        @@singular@@</h1>

    @if(!empty($duplicate@@singular-pascalCase@@))
    <div class="alert alert-success">Duplicating @@singular@@ <b>"{{$duplicate@@singular-pascalCase@@->topic}}"</b></div>
    @endif

            <!-- Item creation stuff here -->

@stop


@section('foot')
    @parent

    <script src="{{asset('js/admin/admin.js')}}"></script>

@stop


