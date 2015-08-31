<?php

class PageController extends BaseController {
	
	public function viewPage($urlString) {
		try {
			$page = Page::where('urlString', $urlString)->first();
			if(!$page) {
				return Response::notFound();
			}
			$page = Page::decodePageJson($page);
			return View::make('pages/viewPage')->with(array(
				'page' => $page,
				'currentPage' => 'viewPage',
				'ogImage' => $page['ogData']['ogImage'],
				'ogTitle' => $page['ogData']['ogTitle'],
				'title' => $page['title'] ? $page['title'] : '',
				'ogDescription' => $page['ogData']['ogDescription'],
				'description' => $page['description'] ? $page['description'] : ''
			));
		}catch(Illuminate\Database\Eloquent\ModelNotFoundException $e) {
			return Response::notFound('Page not found');
		}
	}
}