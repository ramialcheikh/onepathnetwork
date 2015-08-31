@extends('layout')

@section('content')
    <div class="row">
        <div class="col-md-6 col-xs-12 col-md-offset-3">
            <h3 class="text-center">{{__('resendConfirmationEmail')}}</h3>
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
            <div class="well">
                <form action="{{ action('UserController@postResendConfirmationEmail') }}" method="POST">
                    <div class="form-group">
                        <input class="form-control" type="email" name="email" placeholder="{{__('email')}}">
                    </div>
                    <div class="form-group">
                        <input class="btn btn-primary btn-block" type="submit" value="{{__('resendConfirmationEmail')}}">
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop