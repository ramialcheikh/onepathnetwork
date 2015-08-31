<?php
use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;
use Facebook\FacebookJavaScriptLoginHelper;
use Illuminate\Support\Facades\Redirect;

class UserController extends BaseController {
    public static $rules = ['name' => 'required|max:100', 'email' => 'required|email|unique:users,email', 'password' => 'required|between:6,12'];
	public function getLogin() {
		return View::make('login')->with('redirectUrl', Session::get('url.intended', route('home')));
	}

    public function postLogin() {
        $input = Input::all();

        $attempt = Auth::attempt( array('email' => $input['email'], 'password' => $input['password'], 'confirmed' => 1) );

        if($attempt) {
            if(Request::ajax()){
                return Response::json(array('user' => Auth::user()));
            } else {
                return Redirect::intended('home');
            }
        } else {
            //Attempt again without checking 'confirmed'
            $attempt = Auth::validate( array('email' => $input['email'], 'password' => $input['password']) );
            if($attempt) {
                //Credentials are correct. but email not verified
                $error = __('emailNotConfirmedYet');
                $emailNotConfirmed = true;
            } else {
                $error = __('emailOrPasswordIncorrect');
            }
            if(Request::ajax()){
                return Response::json(array(
                    'error' => $error,
                    'emailNotConfirmed'   =>  !empty($emailNotConfirmed) ? true : false
                ), 400);
            } else {
                return Redirect::to(route('login'))->with('login:errors', [$error])->withInput();
            }
        }
    }

    public function postSignup() {
        $input = Input::all();

        $validator = Validator::make($input, self::getValidateRules());
        if($validator->fails()) {
            //Validation failed - Respond with error
            return Response::make(__('someErrorsInInput') . implode('<br>', $validator->messages()->all()), 400);
        }

        $user = new User;
        $confirmationCode = str_random(30);
        $user->name = Input::get('name');
        $user->email = Input::get('email');
        $user->password = Hash::make(Input::get('password'));
        $user->confirmation_code = $confirmationCode;
        $user->save();

        //Send confirmation email
        $confirmEmailNotification = new Notifications\ConfirmEmail($user, $confirmationCode);
        $confirmEmailNotification->send();

        return Redirect::to('users/login')->with('registration_success_message', __('registrationSuccessfulConfirmEmailNow'));
    }

    public function confirmEmail($confirmationCode) {
        try {
            if(!$confirmationCode)
            {
                throw new InvalidConfirmationCodeException;
            }

            $user = User::whereConfirmationCode($confirmationCode)->first();

            if (!$user)
            {
                throw new InvalidConfirmationCodeException;
            }

            self::markUserAsConfirmed($user);
            $user->save();

            Session::flash('email_confirmation_message', __('successfullyVerifiedEmail'));

            return Redirect::route('login');
        } catch(InvalidConfirmationCodeException $e) {
            return Response::error(__('invalidConfirmationCode'));
        }
    }

    public function getResendConfirmationEmail() {
        return View::make('users.resend-confirmation-email');
    }

    public function postResendConfirmationEmail() {
        $email = Input::get('email');
        $user = User::whereEmail($email)->first();

        if (!$user)
        {
            return Redirect::action('UserController@getResendConfirmationEmail')->with(['error'   =>  __('userNotFound')]);
        }

        if(empty($user->confirmation_code)) {
            $confirmationCode = str_random(30);
            $user->confirmation_code = $confirmationCode;
            $user->save();
        }
        //Send confirmation email
        $confirmEmailNotification = new Notifications\ConfirmEmail($user, $user->confirmation_code);
        $confirmEmailNotification->send();
        return Redirect::action('UserController@getResendConfirmationEmail')->with(['status' => __('confirmationEmailSent')]);
    }

	public function loginWithFb() {
		$redirectUrl = Input::get('redirect_url');
		$facebookBaseConfig = Config::get('facebook');
		$config = app('siteConfig');
		$facebookConfig = $config['main']['social']['facebook'];
		$facebookConfig['appId'] = empty($facebookConfig['appId']) ? '' : $facebookConfig['appId'];
		$facebookConfig['secret'] = empty($facebookConfig['secret']) ? '' : $facebookConfig['secret'];
		FacebookSession::setDefaultApplication($facebookConfig['appId'], $facebookConfig['secret']);
		$helper = new FacebookJavaScriptLoginHelper();
		$session = null;

		function getUserDataFromFb($session){
			$request = new FacebookRequest($session, 'GET', '/me', array('fields' => 'id,name,email'));
			$response = $request->execute();
			$graphObject = $response->getGraphObject()->asArray();
			return $graphObject;
		}

		try {
			$session = $helper->getSession();
		} catch(FacebookRequestException $ex) {
			// When Facebook returns an error
		} catch(\Exception $ex) {
			// When validation fails or other local issues
		}
		if(Request::ajax()) {
			if ($session) {
			  // Logged in.
				$uid = $session->getUserId();
				$accessToken = $session->getToken();
				$profile = Profile::whereUid($uid)->first();
				if (empty($profile)) {
					$me = getUserDataFromFb($session);	
					$user = new User;
					$user->name = $me['name'];
					$user->email = $me['email'];
					$user->photo = 'https://graph.facebook.com/'.$uid.'/picture?type=large';

					$user->save();

					$profile = new Profile();
					$profile->uid = $uid;
					//$profile->username = $me['username']; //Username not available in the new Facebook API
					$profile->access_token = $accessToken;
					$profile = $user->profiles()->save($profile);
					
				}
				else {
					$profile->access_token = $accessToken;
					$profile->save();
				}
				$user = $profile->user;
				Auth::login($user);
				return Response::json(array('user' => $user));
			} else {
				return Response::make('Not loggedin', 400);
			}
		} else{
			if ($session) {
				if($redirectUrl) {
					return Redirect::to($redirectUrl);
				} else{
					return Redirect::route('home');
				}
			}
			return Redirect::route('login');
		}
	}
	
	public function logout(){
		Auth::logout();
		return Redirect::to('/');
	}

    public function profile($userId, $nameString) {
        $me = Auth::user();
        try {
            $user = User::findOrFail($userId);
            View::share(self::getProfileOgData($user));
            $listsQuery = $user->lists();
            //If not logged in or if the profile is not of the logged in user, show approved lists only
            if(!$me || $me->id != $userId) {
                $listsQuery = $listsQuery->approved();
            }
            return View::make('users.profile')->with([
                'user'  =>  $user,
                'lists' =>  $listsQuery->latest()->simplePaginate(ListController::getPerPageLimit()),
                'listsCount'    =>  $user->lists()->count(),
                'showListItemStatus'    =>  true
            ]);
        }catch(Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return Response::notFound();
        }
    }

    public function myProfile() {
        $user = Auth::user();
        if(!$user)
            return;
        return Redirect::to(UserHelpers::userProfileUrl($user));
    }

    public function myProfileSettings() {
        $user = Auth::user();
        $validateRules = [
            'name'  =>  'required|min:3|max:30',
            'photo'  =>  'required'
        ];

        if(!$user)
            return;
        if(Request::isMethod('get')) {
            return View::make('users.profile-settings')->with('user', $user);
        } else {
            $input = Input::only(['name','photo']);
            $validator = Validator::make($input, $validateRules);
            if($validator->fails()) {
                return Redirect::back()->withErrors($validator);
            }
            $user->name = $input['name'];
            $user->photo = $input['photo'];
            $user->save();
            return Redirect::back();
        }
    }

    public function postUploadProfilePic() {
        $fileMaxSize = Helpers::getPermittedFileMaxSize();
        $user = Auth::user();
        $targetFileRelativePath = 'media/uploads/' . $user->id .'/' . uniqid(). '.jpg';
        $targetFilePath = public_path($targetFileRelativePath);
        $targetDirname = dirname($targetFilePath);
        if(!File::exists($targetDirname)) {
            mkdir($targetDirname, 0777, true);
        }
        try {
            $file = Input::file('file');
            if(empty($_FILES)) {
                /*If the file exceeds post_max_size, no exception is thrown, but the POST data will be empty.
                See this: http://stackoverflow.com/questions/2133652/how-to-gracefully-handle-files-that-exceed-phps-post-max-size*/
                return Response::json('invalidOrBig', 400);
            }
            if($file->getSize() > ($fileMaxSize * 1000000)) {
                return Response::json('tooBig', 400);
            }
            $img = Image::make($file);
            $img->fit(200, 200);
            $img->save($targetFilePath);
        } catch(Intervention\Image\Exception\NotReadableException $e) {
            return Response::json('invalidImage', 400);
        } catch(Intervention\Image\Exception\NotWritableException $e) {
            return Response::json('Error saving image! Permission issue! Contact site admin');
        } catch(Exception $e) {
            return Response::json('Error uploading image!' . $e->getMessage(), 400);
        }
        return Response::json($targetFileRelativePath, 200);
    }

    public function postChangePassword(){
        $user = Auth::user();
        $rules = array(
            'old_password' => 'required',
            'password' => 'required|alphaNum|between:6,15|confirmed'
        );

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails())
        {
            return Redirect::back()->withErrors($validator);
        }
        else
        {
            if (!Hash::check(Input::get('old_password'), $user->password))
            {
                return Redirect::back()->withErrors(__('yourCurrentPasswordIsIncorrect'));
            }
            else
            {
                $user->password = Hash::make(Input::get('password'));
                $user->save();
                return Redirect::back()->with('successMessage', __('passwordChanged'));
            }
        }
    }

    public static function getProfileOgData($user) {
        $profileUrl = UserHelpers::userProfileUrl($user);
        return [
            'ogType'    => 'profile',
            'ogImage' => $user->photo,
            'ogTitle' => $user->name,
            'ogUrl' => $profileUrl,
            'title' => $user->name . ' - ' . Config::get('siteConfig')['main']['siteName'],
            'description' => $user->name,
            'canonicalUrl' => $profileUrl
        ];
    }

    public static function getValidateRules() {
        return self::$rules;
    }

    /**
     * @param $user
     */
    public static function markUserAsConfirmed($user)
    {
        $user->confirmed = 1;
        $user->confirmation_code = null;
    }
}