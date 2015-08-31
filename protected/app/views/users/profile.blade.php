@extends('layout')


@section('content')
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="text-center profile-header">
                <div class="profile-pic-container">
                    <img class="profile-pic img-circle" src="{{UserHelpers::getSquareProfilePic($user, 150)}}" alt="{{{ $user->name }}}">
                </div>
                <h1 class="user-name">{{{$user->name}}}</h1>
                <ul class="list-unstyled stats-list list-inline">
                    <li>
                        <b>{{$listsCount}}</b> lists
                    </li>
                    {{--<li>
                        <i class="fa fa-star star-icon"></i> 18190 points
                    </li>--}}
                </ul>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('lists/listsList')
            {{ $lists->links('pagination::simple') }}

            @if(!empty($widgets['belowListsList']))
                <div class="lists-page-foot-widget-section">
                    @foreach($widgets['belowListsList'] as $widget)
                        {{do_shortcode($widget['content'])}}
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@stop