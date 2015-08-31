@extends('app.views.layout')

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
            <h3 class="text-center"><strong>{{__('latest@@plural-pascalCase@@')}}</strong></h3>
            @include('@@plural@@/@@plural@@List')
            @if(is_array($@@plural@@) && count($@@plural@@))
            <div class="text-center">
                <br/>
                <a href="{{route('@@plural@@')}}" class="btn btn-primary"><span>{{__('viewMore@@plural-pascalCase@@')}}</span></a>
            </div>
            @endif
        </div>
    </div>
@stop