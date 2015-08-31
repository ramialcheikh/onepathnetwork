@extends('layout')

@section('content')
    <h3>{{__('resetPassword')}}</h3>
    @if(Session::has('status'))
        <div class="alert alert-success">
            {{Session::get('status')}}
        </div>
    @endif
    @if(Session::has('error'))
        <div class="alert alert-danger">
            {{Session::get('error')}}
        </div>
    @endif
    <div class="row">
        <div class="col-md-6 col-xs-12">
            <form action="{{ action('RemindersController@postReset') }}" method="POST">
                <input type="hidden" name="token" value="{{ $token }}">
                <div class="form-group">
                    <input class="form-control" type="email" name="email" placeholder="{{__('email')}}">
                </div>
                <div class="form-group">
                    <input class="form-control" type="password" name="password" placeholder="{{__('password')}}">
                </div>
                <div class="form-group">
                    <input class="form-control" type="password" name="password_confirmation" placeholder="{{__('confirmPassword')}}">
                </div>
                <div class="form-group">
                    <input class="btn btn-primary" type="submit" value="{{__('resetPassword')}}">
                </div>
            </form>
        </div>
    </div>
@stop