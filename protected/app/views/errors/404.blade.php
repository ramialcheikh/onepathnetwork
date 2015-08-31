@extends('layout')

@section('content')
    <div class="row">
        <div class="col-md-6 col-md-offset-3 text-center notFound-title-block well">
            <div style="font-size: 10em;"><strong>404</strong></div>
            <hr/>
            <h1>{{__('pageNotFound')}}</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <h3 class="text-center"><strong>{{__('latestLists')}}</strong></h3>
            @include('lists/listsList')
            @if(is_array($lists) && count($lists))
            <div class="text-center">
                <br/>
                <a href="{{route('lists')}}" class="btn btn-primary"><span>{{__('viewMoreLists')}}</span></a>
            </div>
            @endif
        </div>
    </div>
@stop