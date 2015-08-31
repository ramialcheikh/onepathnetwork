@extends('layout')
@section('content')
    <div class="row">
        <div class="col-md-6 col-md-offset-3 text-center">
            <br/>
            <div class="white-tile login-page-form-box">
                <h2>{{__('loginBtn')}}</h2>
            </div>
        </div>
    </div>
@stop

@section('foot')
    @parent
    <script>
        $(function() {
            var redirectUrl = '{{$redirectUrl}}';
            $('body').trigger('prompt-login', '{{str_replace('\'', '\\\'', __('generalLoginPromptMessage'))}}');
            $('body').on('loggedIn', function(){
                window.location.href = redirectUrl;
            });
        });

    </script>
@stop