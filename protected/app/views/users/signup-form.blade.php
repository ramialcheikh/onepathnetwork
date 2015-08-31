<!-- TAB NAVIGATION -->
<ul class="nav nav-tabs hide" role="tablist">
    <li class="active"><a href="#loginTab" role="tab" data-toggle="tab">{{__('loginBtn')}}</a></li>
    <li><a href="#signupTab" role="tab" data-toggle="tab">{{__('signUpBtn')}}</a></li>
</ul>
<!-- TAB CONTENT -->
<div class="tab-content login-signup-tabs">
    <div class="tab-pane fade in active" id="loginTab">
        <p>{{__('orLoginWithYourEmail')}}</p>

        <div class="loginsignup-error-message alert alert-danger text-left hide" style="margin-top: 20px;">
        </div>

        {{ Form::open(array('url'=>'users/login', 'class'=>'form-signup', 'id'  => 'loginForm')) }}
        <div class="login-form text-left">
            <div class="form-group">
                {{ Form::text('email', Input::old('email'), array('class'=>'form-control login-field', 'placeholder'=> __('email'))) }}
                <label class="login-field-icon fui-mail" for="login-pass"></label>
            </div>

            <div class="form-group">
                {{ Form::password('password', array('class'=>'form-control login-field', 'placeholder'=> __('password'))) }}
                <label class="login-field-icon fui-lock" for="login-pass"></label>
            </div>
            <input class="btn btn-primary btn-lg btn-block" type="submit" value="{{__('loginBtn')}}"/>
            <a class="login-link lost-password" href="{{action('RemindersController@postRemind')}}">{{{__('lostYourPassword')}}}</a>
            <a id="dontHaveAccountSignupHereBtn" class="login-link" href="#">{{__('dontHaveAccountSignupHere')}}</a>
        </div>
        {{ Form::close() }}
    </div>

    <div class="tab-pane fade" id="signupTab">
        <p>{{__('orSignUpWithYourEmail')}}</p>

        <div class="loginsignup-error-message alert alert-danger text-left hide" style="margin-top: 20px;">
        </div>

        {{ Form::open(array('url'=>'users/signup', 'class'=>'form-signup', 'id' => 'signupForm'), UserController::getValidateRules()) }}
        <div class="login-form text-left">
            <div class="form-group">
                {{ Form::text('name', null, array('class'=>'form-control login-field', 'placeholder'=> __('name'))) }}
                <label class="login-field-icon fui-user" for="login-name"></label>
            </div>

            <div class="form-group">
                {{ Form::text('email', Input::old('email'), array('class'=>'form-control login-field', 'placeholder'=> __('email'))) }}
                <label class="login-field-icon fui-mail" for="login-pass"></label>
            </div>

            <div class="form-group">
                {{ Form::password('password', array('class'=>'form-control login-field', 'placeholder'=> __('password'))) }}
                <label class="login-field-icon fui-lock" for="login-pass"></label>
            </div>
            <input type="submit" class="btn btn-primary btn-lg btn-block" value="{{{__('signUpBtn')}}}">
            <a id="alreadyRegisteredLoginHereBtn" class="login-link" href="#" onclick="$('#loginTab').tab('show'); return false;">{{__('alreadyRegisteredLoginHere')}}</a>
        </div>
        {{ Form::close() }}
    </div>
</div>

<script>
    $(function() {
        function showFormError(error) {
            if(typeof error == 'object')
                error = error.join('<br>');
            $('.loginsignup-error-message').html(error).removeClass('hide');
        }

        function hideErrors() {
            $('.loginsignup-error-message').addClass('hide');
        }

        @if(Session::has('login:errors'))
        showFormError({{json_encode(Session::get('login:errors'))}});
        @endif

        $('#loginForm').submit(function(e) {
                    e.preventDefault();
                    hideErrors();
                    var form = $(this);
                    $.post( form.attr('action'), form.serialize()).done(function(response) {
                        User.setData(response.user);
                    }).fail(function( jqXHR, textStatus) {
                        if(jqXHR.responseJSON) {
                            var response = jqXHR.responseJSON;
                            var error = response.error;
                            if(response.emailNotConfirmed) {
                                error += '<br><a href="{{{action('UserController@getResendConfirmationEmail')}}}">'+ __('resendConfirmationEmail') +'</a>';
                            }
                            showFormError(error);
                        } else{
                            showFormError(jqXHR.responseText);
                        }
                    });
                });
        function showLoginForm() {
            hideErrors();
            $('a[href="#loginTab"]').tab('show');
        }
        function showSignupForm() {
            hideErrors();
            $('a[href="#signupTab"]').tab('show');
        }
        $('#alreadyRegisteredLoginHereBtn').click(function(e) {
            showLoginForm();
            e.preventDefault();
        });
        $('#dontHaveAccountSignupHereBtn').click(function(e) {
            showSignupForm();
            e.preventDefault();
        });
    });
</script>