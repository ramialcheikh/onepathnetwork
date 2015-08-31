<!DOCTYPE html>
<html lang="{{App::getLocale()}}">

<head>

   @section('head')
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
		@if(!empty($config['main']['favicon']))
			<link rel="shortcut icon" href="{{ htmlspecialchars(asset($config['main']['favicon'])) }}">
		@endif

    @if(!empty($title))
        <title>{{ htmlspecialchars($title) }}</title>
    @endif

    @if(!empty($ogType))
        <meta property="og:type" content="{{$ogType}}">
    @else
        <meta property="og:type" content="website">
    @endif

    @if(!empty($metaAuthorName))
        <meta name="author" content="{{ htmlspecialchars($metaAuthorName) }}" />
    @endif

    @if(!empty($ogTitle))
        <meta property="og:title" content="{{ htmlspecialchars($ogTitle) }}" />
        <meta name="twitter:title" content="{{ htmlspecialchars($ogTitle) }}" />
    @endif

    @if(!@empty($config['main']['social']['facebook']['appId']))
            <meta property="fb:app_id" content="{{$config['main']['social']['facebook']['appId']}}" />
    @endif


    @if(!empty($ogImage))
        <meta property="og:image" content="{{ htmlspecialchars($ogImage) }}" />
        <meta name="twitter:image" content="{{ htmlspecialchars($ogImage) }}" />
        <meta name="twitter:card" content="photo" />
    @endif


    @if(!empty($metaArticlePublishedTime))
        <meta property="article:published_time" content="{{$metaArticlePublishedTime}}" />
    @endif

    @if(!empty($ogImageWidth) && !empty($ogImageHeight))
        <meta property="og:image:width" content="{{ $ogImageWidth }}" />
        <meta property="og:image:height" content="{{ $ogImageHeight }}" />
    @endif

    @if(!empty($ogUrl))
        <meta property="og:url" content="{{ htmlspecialchars($ogUrl) }}" />
        <meta name="twitter:url" content="{{ htmlspecialchars($ogUrl) }}" />
    @endif

    @if(!empty($description))
        <meta name="description" content="{{ htmlspecialchars($description) }}" />
    @endif
    @if(!empty($ogDescription))
        <meta property="og:description" content="{{ htmlspecialchars($ogDescription) }}" />
    @endif

    @if(!empty($config['main']['siteName']))
        <meta property="og:site_name" content="{{ htmlspecialchars($config['main']['siteName']) }}" />
    @endif

    @if(!empty($canonicalUrl))
        <link rel="canonical" href="{{htmlspecialchars($canonicalUrl)}}" />
    @endif
    <!-- Custom CSS -->
	@if(App::isLocal())
		<link href="{{asset('/css/main.css')}}" rel="stylesheet">
	@else
		<link href="{{asset('/css/main.min.css')}}" rel="stylesheet">
	@endif

    @if(App::isLocal())
        <link href="{{asset('/css/bootstrap.css')}}" rel="stylesheet">
        <link href="{{asset('/themes/flat/css/style.css')}}" rel="stylesheet">
    @else
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
        <link href="{{asset('/themes/flat/css/style.min.css')}}" rel="stylesheet">
    @endif

	@if($languageDirection == 'rtl')
                <link href="{{asset('/css/bootstrap-rtl.min.css')}}" rel="stylesheet">
		@include('partials.rtlCss')
	@endif

	@if(!empty($navbarColor))
		<style>
			@include('partials.themeCss')
		</style>
	@endif

    <!-- Custom Fonts -->
    <link href="{{asset('/font-awesome-4.1.0/css/font-awesome.min.css')}}" rel="stylesheet" type="text/css">

   	<!-- jQuery Version 1.11.0 -->
   	@if(App::isLocal())
    <script src="{{ asset('bower_components/jquery/dist/jquery.min.js')}}"></script>
    @else
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    @endif

    <script>
        var BASE_PATH = '{{ url('') }}';
        var ASSET_BASE_PATH = '{{ asset('') }}';
        window.asset = function(path) {
            path = path || '';
            return path.match(/^http[s]?:\/\/.*$/) ? path : ASSET_BASE_PATH + path;
        }
		var SiteMainConfig = {{@$mainConfigJSON}};
		var SiteListConfig = {{@$listConfigJSON}};

		var User = {
			isLoggedIn: function(){
				return (!$.isEmptyObject(this.data));
			},
			setData: function(data){
				this.data = data;
				if(this.isLoggedIn()){
					$('body').trigger('loggedIn');
				}
			}
		};
		User.data = {{$userData or '{}'}};
        @if(!empty($categories))
            window.Categories = {{json_encode($categories)}};
        @endif
	</script>

	<script>
		var languageStrings = {{json_encode($languageStrings)}};
		var defaultLanguageStrings = {{json_encode($defaultLanguageStrings)}};

        (function() {
            function toCapitalizedWords(name) {
                var words = name.match(/[A-Za-z][a-z]*/g);

                return words.map(capitalize).join(" ");
            }

            function capitalize(word) {
                return word.charAt(0).toUpperCase() + word.substring(1);
            }

            //Translation
            window.__ = function(key){
                if(languageStrings.hasOwnProperty(key)){
                    return languageStrings[key];
                } else if (defaultLanguageStrings.hasOwnProperty(key)){
                    return defaultLanguageStrings[key];
                } else {
                    return toCapitalizedWords(key);
                }
            }
        })();
	</script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
	<script>

	  window.fbAsyncInit = function() {
		FB.init({
		  appId      : '<?php echo @$config['main']['social']['facebook']['appId'];?>',
		  xfbml      : true,
		  version    : 'v2.4',
			cookie : true
		});

		$('body').trigger('fb-api-loaded');
		FB.Event.subscribe('auth.statusChange', function(response) {
		  // do something with response
			/*if(response.status === "connected") {
				$('body').trigger('loggedIn:fb');
			}*/
		});
	  };

	  (function(d, s, id){
			 var js, fjs = d.getElementsByTagName(s)[0];
			 if (d.getElementById(id)) {return;}
			 js = d.createElement(s); js.id = id;
			 js.src = "//connect.facebook.net/{{$languageFbCode}}/sdk.js";
			 fjs.parentNode.insertBefore(js, fjs);
		})(document, 'script', 'facebook-jssdk');
	</script>

	@if(!empty($config['main']['customCode']['head']))
		{{$config['main']['customCode']['head']}}
	@endif

	@show

</head>

<body class="@if($languageDirection == 'rtl') rtl-language @endif">
	<div id="fb-root"></div>

        <div class="body_wrap @if(!empty($currentPage))page-{{$currentPage}}@endif">
    	<div class="body-container container-fluid">

            @include('header')
            <div class="row">
            	<div class="col-md-8 col-sm-7 col-xs-12 main-content-col pull-left">
				@yield('content')
				</div>

				<div class="col-md-4 col-sm-5 col-xs-12 sidebar-col pull-right">
					@include('sidebar')
			   </div>
            </div>
            @include('partials.widgets', array('section' => 'commonFooterSection'))

            <!-- /.container-fluid -->

        </div>
            <div class="container-fluid">
                <div class="row footer-row">
                    <div class="col-md-12">
                        @include('footer')
                    </div>
                </div>
            </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    <div id="loginDialog" class="dialog">
        <div class="dialog__overlay"></div>
        <div class="dialog__canvas">
            <div class="dialog__content">
                <div class="heading">{{__('loginBtn')}} / {{__('signUpBtn')}}</div>
                <div class="login-prompt-message"></div>
                <div class="login-panel">
                    @if(Session::has('registration_success_message'))
                        <div class="alert alert-success">
                            {{ Session::get('registration_success_message') }}
                        </div>
                    @endif

                    @if(Session::has('email_confirmation_message'))
                        <div class="alert alert-success">
                            {{ Session::get('email_confirmation_message') }}
                        </div>
                    @endif

                    <div class="btn btn-social-facebook login-with-fb-btn" data-action="loginWithFB"><i class="fa fa-facebook"></i> {{__('loginWithFB')}}</div>
                        <div class="sk-rotating-plane loading-anim loading-primary"></div>
                        <div id="socialLoginError" class="hide"></div>
                    <div class="logging-in-msg">
                        <b class="text-center">{{__('loggingYouIn')}}</b>
                    </div>
                    @include('users.signup-form')
                </div>
                <div class="dialog-footer">
                    <div class="btn btn-link action" data-dialog-close=""><i class="fa fa-times"> {{__('closeBtn')}}</i></div>
                </div>
            </div>
        </div>
    </div>

    @if($categories->count())
        <div id="categoryModal" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">{{__('categories')}}</h4>
                    </div>
                    <div class="modal-body text-center">
                        <ul class="list-group">
                            @foreach($categories as $category)
                                <li class="list-group-item"><a href="{{route('category', array('category-slug' => $category->slug))}}" hidefocus="true" style="outline: none;">{{ htmlspecialchars($category->name) }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">{{__('closeBtn')}}</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

    @endif

    <script src="{{ asset('js/Modernizr.min.js') }}"></script>
    <script src="{{ asset('js/dialogFx.js') }}"></script>
    <script>
        (function() {

            var dlgtrigger = document.querySelector( '[data-dialog]' );
            if(dlgtrigger) {
                vasomedialog = document.getElementById( dlgtrigger.getAttribute( 'data-dialog' ) );
                var dlg = new DialogFx( somedialog );
                dlgtrigger.addEventListener( 'click', dlg.toggle.bind(dlg) );
            }
        })();
    </script>

   @section('foot')
   <!-- Logging in -->
	<script>
		(function(){
            var vent = $('body');
            var body = $('body');

            var socialLoginErrorElm = $('#socialLoginError');

            var loginDialogElm = $('#loginDialog');
            var loginDialog= new DialogFx( loginDialogElm[0] , {
                onOpenDialog: function() {
                    socialLoginErrorElm.addClass('hide')
                }
            });


            vent.on('social-login:error', function(e, error) {
                socialLoginErrorElm.removeClass('hide').html('<div class="alert alert-danger">' + error + '</div>');
                loginDialogElm.removeClass("logging-in");
            });

			window.loginWithFb = function(){
                FB.login(function(response) {
                    loginDialogElm.addClass("logging-in");
                    if (response.authResponse) {
                        if(response.authResponse.grantedScopes.indexOf('email') < 0) {
                            //If email permission not granted
                            vent.trigger('social-login:error', (__('fbNoEmailError')));
                            return;
                        }
                        FB.api('/me', {fields: 'id,name,email'}, function(response) {
                            console.log('Logged in as ' + response.name + '.');
                            //Dual check email - needed to if check if the user has a verified email ID
                            if(!response.email) {
                                vent.trigger('social-login:error', (__('fbNoEmailError')));
                                return;
                            }
                            body.trigger('loggedIn:fb');
                        });
                    } else {
                        vent.trigger('social-login:error', (__('fbPermissionError')));
                    }
                }, {
                    scope: 'email',
                    auth_type: 'rerequest',
                    'return_scopes': true
                });
                socialLoginErrorElm.addClass('hide');
			}

			body.on('click', '[data-action="loginWithFB"]', function(e){
				loginWithFb();
				e.preventDefault();
			});
			body.on('loggedIn', function(){
                loginDialog.close();
			});
			body.on('loggedIn:fb', function(){
				if(!User.isLoggedIn()) {
					$.get(BASE_PATH + '/login/fb').success(function(response){
						User.setData(response.user);
					}).fail(function(jqXHR, message){
						vent.trigger('social-login:error', jqXHR.responseText);
					}).always(function(){
                        loginDialogElm.removeClass("logging-in");
					});
				}
			});
			body.on('prompt-login', function(e, message){
                var promptMessage = loginDialogElm.find('.login-prompt-message');
                if(message) {
                    promptMessage.html(message).show();
                } else {
                    promptMessage.hide();
                }

                loginDialog.open(loginDialog);

			});
		})();

	</script>
    <!-- Bootstrap Core JavaScript -->
    {{--<script src="{{ asset('/themes/modern/js/libs/modernizr.min.js') }}"></script>--}}

   <script src="{{ asset('/themes/flat/js/flat-ui.min.js') }}"></script>

   <script src="{{asset('bower_components/jquery.validate/dist/jquery.validate.min.js')}}"></script>
   <script src="{{asset('packages/bllim/laravalid/jquery.validate.laravalid.js')}}"></script>

   <script type="text/javascript">
       $('#signupForm').validate({onkeyup: false}); //while using remote validation, remember to set onkeyup false
   </script>

	   @if(!empty($config['main']['customCode']['foot']))
		   {{$config['main']['customCode']['foot']}}
	   @endif

	@show
</body>

</html>
