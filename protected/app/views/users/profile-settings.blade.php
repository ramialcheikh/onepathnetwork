@extends('layout')

@section('head')
    @parent
    <link rel="stylesheet" href="{{ asset('/bower_components/dropzone/dist/min/dropzone.min.css') }}"/>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8 col-xs-12 edit-profile">
            {{Form::model($user, ['route' => 'myProfileSettings'])}}
                @if(!empty($errors) && $errors->all('count'))
                    <div class="alert alert-danger">
                        {{implode('<br>', $errors->all())}}
                    </div>
                @endif
                @if(Session::has('successMessage'))
                    <div class="alert alert-success">
                        {{Session::pull('successMessage')}}
                    </div>
                @endif
                <div class="row">
                    <div class="col-md-4 col-xs-6">
                        <p>
                            <img class="img img-thumbnail profile-pic" src="{{UserHelpers::getSquareProfilePic($user, 150)}}" style="width: 100%;" alt="{{{ $user->name }}}">
                        </p>
                        <div class="btn btn-default btn-xs btn-block change-photo-btn">{{__('changePhotoBtn')}}</div>
                    </div>
                    <div class="col-md-8 col-xs-12">
                        <div class="form-group">
                            <label for="name">{{__('name')}}</label>
                            {{Form::text('name', null, ['class'   =>  'form-control'])}}
                        </div>
                        <div class="form-group">
                            {{ Form::submit( __('updateProfile'), ['class' => 'btn btn-success btn-sm']) }}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {{Form::hidden('photo', null, ['id' =>  'photoField', 'class'   =>  'form-control'])}}
                </div>
            {{Form::close()}}

            <div class="panel panel-default">
            	  <div class="panel-heading">
            			<h3 class="panel-title">{{__('changePassword')}}</h3>
            	  </div>
            	  <div class="panel-body">
                      {{Form::open(['action' => 'UserController@postChangePassword'])}}
                      <div class="form-group">
                          <label for="name">{{__('currentPassword')}}</label>
                          {{Form::password('old_password', ['class'   =>  'form-control'])}}
                      </div>
                      <div class="form-group">
                          <label for="name">{{__('newPassword')}}</label>
                          {{Form::password('password', ['class'   =>  'form-control'])}}
                      </div>
                      <div class="form-group">
                          <label for="name">{{__('confirmNewPassword')}}</label>
                          {{Form::password('password_confirmation', ['class'   =>  'form-control'])}}
                      </div>

                      {{Form::submit(__('changePassword'), ['class' =>  'btn btn-success btn-sm'])}}

                      {{Form::close()}}
            	  </div>
            </div>
        </div>
    </div>
@stop

@section('foot')
    @parent

    @include('partials.media-manager')

    <script src="{{ asset('/bower_components/underscore/underscore-min.js') }}"></script>
    <script src="{{ asset('/bower_components/backbone/backbone-min.js') }}"></script>
    <script src="{{ asset('/bower_components/marionette/lib/backbone.marionette.min.js') }}"></script>
    <script src="{{ asset('/bower_components/dropzone/dist/min/dropzone.min.js') }}"></script>
    <script src="{{ asset('/js/EmbedPreview.js') }}"></script>
    <script src="{{ asset('/js/main.js') }}"></script>

    <script>
        //$(function() {
            function setProfilePic(photo) {
                $('.profile-pic').attr('src', photo);
                $('#photoField').val(photo);
            }

            $('.change-photo-btn').click(function() {
                var self = $(this);
                MediaManager.open(function(media) {
                    setProfilePic(asset(media.url));
                }, {
                    mode: 'photo'
                })
            })

            MediaManager.initialize($('#mediaManagerModal'), {
                templates: {
                    'modalContent' : $('#mediaManagerModalTemplate').html(),
                    'quote' : $('#quoteTemplate').html()
                },
                imageUploadOptions: {
                    maxFileSize: parseFloat(SiteListConfig.fileUploadMaxSize),
                    url: '{{action('UserController@postUploadProfilePic')}}'
                }
            });
        //})
    </script>
@stop
