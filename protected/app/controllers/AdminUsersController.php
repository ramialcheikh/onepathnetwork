<?php

class AdminUsersController extends AdminBaseController {
	public static $perPage = 20;
	public static $sortableFields = array('created_at');
	public static function processSort(){
		$sort = Input::get('sort') ? Input::get('sort') : 'created_at';
		$sortType = (Input::get('sortType') === 'asc') ? 'asc' : 'desc';
		
		if(!in_array($sort, self::$sortableFields)) {
			Response::notFound();
		}
		View::share(array(
			'sort' => $sort,
			'sortType' => $sortType
		));
		if($sort == 'created_at') {
			$sort = 'users.created_at';
		}
		return array('sort' => $sort, 'sortType' => $sortType);
	}
	public static function addSerialNumbers(&$users){
		$slNo = $users->getFrom();
		foreach($users as $user) {
			$user->slNo = $slNo;
			$slNo++;
		}
	}
	

	
	public static function downloadEmails($usersQuery) {
		
		$usersQuery = $usersQuery->select(array('email', 'name'));
		if(Input::get('downloadLimit')) {
			$usersQuery = $usersQuery->take(Input::get('downloadLimit'));
		}
		$users = $usersQuery->get();
		$downloadData = array();
		foreach($users as $user) {
			$userRow = Input::get('includeName') ? '"' . $user->name . '",' : '';
			$userRow .= $user->email;
			$downloadData[] = $userRow;
		}
		if(Input::get('displayOnScreen') == "true")
			return Response::make(implode("<br>", $downloadData));
		else {
			return Response::make(implode("\n", $downloadData))->header('Content-Type', 'text/csv')->header('Content-Disposition', 'attachment; filename="emails.csv"');
		}
	}
	
	public function index() {
        $filters = Input::get('filters', null);
        //dd($filters);
        $search = !empty($filters['query']) ? $filters['query'] : '';
        $autoApproveOnly = !empty($filters['autoApproveOnly']) ? $filters['autoApproveOnly'] : null;
		$sortOptions = self::processSort();
		$usersQuery = User::with('profiles')->with('autoApproveEnabled')->orderBy($sortOptions['sort'], $sortOptions['sortType']);
		if(Input::get('download')) {
			return self::downloadEmails($usersQuery);
		}

        if($search){
            $usersQuery->where('name', 'like', '%' . $search . '%');
        }
        if($autoApproveOnly){
            $usersQuery->has('autoApproveEnabled');
        }
        $users = $usersQuery->paginate(self::$perPage);
        if($search){
            $users->appends(['search' => $search]);
        }
		//dd(DB::getQueryLog());
		self::addSerialNumbers($users);
		//dd($users->toArray());
		View::share(array(
			'users' => $users,
            'filters' => $filters
		));
		return View::make('admin/users/index')->with(array(
			'currentPage' => 'usersIndex'
		));
	}

    public function postEnableAutoApprove() {
        $userId =   Input::get('userId');
        $redirect = Input::get('redirect', route('adminUsersHome'));
        if(!$userId)
            return Redirect::to($redirect)->with(['error' =>  'Invalid user ID']);
        $user = User::find($userId);
        if(!$user) {
            return Redirect::to($redirect)->with(['error' =>  'Invalid user ID']);
        }
        $user->autoApproveEnabled()->save(new AutoApproveEnabledUsers());
        return Redirect::to($redirect)->with(['status' =>  'Auto approve enabled for: ' . $user->name]);
    }

    public function postDisableAutoApprove() {
        $userId =   Input::get('userId');
        $redirect = Input::get('redirect', route('adminUsersHome'));
        if(!$userId)
            return Redirect::to($redirect)->with(['error' =>  'Invalid user ID']);
        $user = User::find($userId);
        if(!$user) {
            return Redirect::to($redirect)->with(['error' =>  'Invalid user ID']);
        }
        $user->autoApproveEnabled()->delete();
        return Redirect::to($redirect)->with(['status' =>  'Auto approve disabled for: ' . $user->name]);
    }

}