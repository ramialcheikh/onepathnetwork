<nav id="mainNavbar" class="navbar navbar-default navbar-fixed-top">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed pull-right" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="{{url('/')}}" class="navbar-brand pull-left" style="padding:0px;">
                @if(!empty($config['main']['logo']))
                    <img src="{{asset($config['main']['logo'])}}" alt="" style="height: 100%;vertical-align: middle;margin: 0px 0px;">
                @else
                    <div style="height: 60px; line-height: 60px;padding: 0px 10px;font-weight: bold;font-size: medium;">{{$config['main']['siteName']}}</div>
                @endif
            </a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-main-links navbar-left">
                <li class=""><a href="{{URL::route('lists')}}" style="outline: none;"><span>{{__('latest')}}</span></a></li>
                <li class=""><a href="{{URL::route('popularLists')}}" style="outline: none;"><span>{{__('popular')}}</span></a></li>
                @if($categories->count())
                    @if(@$config['main']['categoryModalEnabled'] && $config['main']['categoryModalEnabled'] != "false")
                        <li class=""><a id="categoriesNavLink" href="javascript;" style="outline: none;"><span>{{__('categories')}}</span></a></li>
                        <script>
                            $('#categoriesNavLink').click(function() {
                                $('#categoryModal').modal('show'); return(false);
                            })
                        </script>
                    @else
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{__('categories')}} <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            @foreach($categories as $category)
                                <li class=""><a href="{{route('category', array('category-slug' => $category->slug))}}" hidefocus="true" style="outline: none;">{{ htmlspecialchars($category->name) }}</a></li>
                            @endforeach
                        </ul>
                    </li>
                    @endif
                @endif
                @if(!empty($widgets['navbarLinks']))
                    @foreach($widgets['navbarLinks'] as $widget)
                        <li class="">{{do_shortcode($widget['content'])}}</li>
                    @endforeach
                @endif
            </ul>
            <ul id="headerUserMenu" class="hide nav navbar-nav navbar-right">
                @if(@$config['main']['enableUserLogin'] && $config['main']['enableUserLogin'] != "false")
                    <li class="before-login-actions">
                        <a id="headerUserLoginLink" href="{{ route('login')}}" style="outline: none;">{{__('loginBtn')}}</a>
                    </li>
                    <li class="dropdown after-login-actions">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><img id="userProfilePicture" alt="user profile picture" class="img-circle" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" width="33" height="33"> <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="{{ route('myProfile')}}"><span><i class="fa fa-user"></i> &nbsp;{{__('myProfile')}}</span></a></li>
                            <li><a href="{{ route('myProfileSettings')}}"><span><i class="fa fa-cog"></i> &nbsp;{{__('settings')}}</span></a></li>
                            <li><a id="headerUserLogoutLink" href="{{ route('logout')}}"><span><i class="fa fa-sign-out"></i> &nbsp;{{__('logoutBtn')}}</span></a></li>
                        </ul>
                    </li>
                @endif
            </ul>

            <ul class="nav navbar-nav navbar-right">
                <li>
                    <a class="btn btn-default navbar-create-btn" href="{{route('createList')}}"><i class="fa fa-plus"></i> {{__('create')}}</a>
                </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="javascript;" onclick="return false;" class="reveal-search-btn"><i class="fa fa-search"></i><i class="fa fa-times"></i></a></li>
            </ul>
            <form action="{{route('search')}}" class="navbar-form navbar-right navbar-search-form" role="search">
                <button type="submit" class="btn btn-default search-btn pull-right"><i class="fa fa-search"></i></button>
                <div class="form-group">
                    <input type="text" name="q" class="form-control" placeholder="{{__('search')}}">
                </div>
            </form>

            <script>
                $(function() {
                   $('.reveal-search-btn').click(function() {
                       $('#mainNavbar').toggleClass('show-search');
                       $('.navbar-search-form .form-control').focus();
                   });
                    $('.navbar-search-form .form-control').blur(function() {
                        if(!$(this).val()) {
                            setTimeout(function() {
                                $('#mainNavbar').toggleClass('show-search', false);
                            }, 100);
                        }
                    });

                    $('.navbar-create-btn').click(function(e) {
                        if(!User.isLoggedIn()) {
                            $('body').trigger('prompt-login', '{{str_replace('\'', '\\\'', __('loginToCreate'))}}');
                            e.preventDefault();
                            return false;
                        }
                    });
                });
            </script>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>

<script>
    (function(){
        var defaultProfilePic = '{{{asset('images/profile-pic.png')}}}';
        function updateUserMenu(){
            $('#headerUserMenu').removeClass('hide')
            if(User.isLoggedIn()){
                $('#userProfilePicture').attr('src', User.data['photo'] || defaultProfilePic);
                $('#headerUserMenu').addClass('logged-in');
            } else {
                $('#headerUserMenu').removeClass('logged-in');
            }
        }
        $('body').on('loggedIn', function(){
            updateUserMenu();
        });
        updateUserMenu();
        $('#headerUserLoginLink').click(function(e){
            $('body').trigger('prompt-login', '{{str_replace('\'', '\\\'', __('generalLoginPromptMessage'))}}');
            e.preventDefault();
        });
    })();
</script>

@if(!empty($widgets['belowNavbar']))
    <div class="row">
        <div class="col-md-12">
            <div class="belowNavbar-widget-section">
                @foreach($widgets['belowNavbar'] as $widget)
                    {{do_shortcode($widget['content'])}}
                @endforeach
            </div>
        </div>
    </div>
@endif