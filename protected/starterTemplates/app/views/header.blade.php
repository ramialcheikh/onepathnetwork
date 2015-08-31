<div class="row">
	<div class="col-sm-12">
		<!-- Website Menu -->
		<div id="topmenu" class="fixed">
			<ul class="dropdown clearfix boxed boxed-white">
			   <li class="menu-toggle-block clearfix">
					<a href="{{url('/')}}" class="pull-left" style="padding:0px;">
						@if(!empty($config['main']['logo']))
							<img src="{{asset($config['main']['logo'])}}" alt="" style="height: 40px;vertical-align: middle;margin: 10px 20px;">
						@else
							<div style="height: 60px; line-height: 60px;padding: 0px 10px;font-weight: bold;font-size: medium;">{{$config['main']['siteName']}}</div>
						@endif
					</a>
					<a class="btn menu-toggle" onclick="$(this).parents('.dropdown').first().toggleClass('expanded')">
						<i class="menu-icon fa fa-bars fa-lg"></i>
					</a>
			   </li>
				<li class="menu-level-0 hidden-xs">
					<a href="{{url('/')}}" style="outline: none;padding: 0px;">
						@if(!empty($config['main']['logo']))
							<img src="{{asset($config['main']['logo'])}}" alt="" style="height: 40px;vertical-align: middle;margin: 10px 10px;">
						@else
							<div style="height: 60px; line-height: 60px;padding: 0px 20px;font-weight: bold;font-size: medium;">{{$config['main']['siteName']}}</div>
						@endif
</a>
				</li>
				<li class="menu-level-0"><a href="{{URL::route('@@plural@@')}}" style="outline: none;"><span>{{__('latest')}}</span></a></li>
				<li class="menu-level-0"><a href="{{URL::route('popular@@plural-pascalCase@@')}}" style="outline: none;"><span>{{__('popular')}}</span></a></li>
                @if($categories->count())
                <li class="menu-level-0">
                    <a href="#" hidefocus="true" style="outline: none;"><span>{{__('categories')}}</span></a>
                    <ul class="submenu-1">
                        @foreach($categories as $category)
                            <li class="menu-level-1"><a href="{{route('category', array('category-slug' => $category->slug))}}" hidefocus="true" style="outline: none;">{{ htmlspecialchars($category->name) }}</a></li>
                        @endforeach
                    </ul>
                </li>
                @endif
				@if(!empty($widgets['navbarLinks']))
					@foreach($widgets['navbarLinks'] as $widget)
						<li class="menu-level-0">{{$widget['content']}}</li>
					@endforeach
				@endif
				@if(@$config['main']['enableUserLogin'] && $config['main']['enableUserLogin'] != "false")
				<li id="headerUserMenu" class="menu-level-0 @if($languageDirection == 'rtl') pull-left @else pull-right @endif hide">
					<a id="headerUserLoginLink" href="{{ route('login')}}" style="outline: none;"><span>{{__('loginBtn')}}</span></a>
					<a id="headerUserLogoutLink" href="{{ route('logout')}}" style="outline: none;"><img id="userProfilePicture" alt="user profile picture" class="img-circle" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" width="30" height="30"><span>{{__('logoutBtn')}}</span></a>
				</li>
				@endif
				<script>
					(function(){
						function updateUserMenu(){
							$('#headerUserMenu').removeClass('hide')
							if(User.isLoggedIn()){
								$('#userProfilePicture').attr('src', User.data['photo']);
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
							$('body').trigger('prompt-login');
							e.preventDefault();
						});
					})();
				</script>
				<!--<li class="menu-search last">
					<form id="searchForm" class="menu-search-form" method="post">
						<input type="text" name="search" value="" class="menu-search-field" placeholder="Search the website" style="outline: none;">
						<input type="submit" value="&#xf002;" class="btn menu-search-submit fa" name="search-send" style="outline: none;">
					</form>
				</li>-->
			</ul>
		</div>
		<!--/ Website Menu -->
		<div class="fixed-menu-padding-adjustment"></div>
	</div>
</div>