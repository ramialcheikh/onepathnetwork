<?php

class AdminConfigController extends AdminBaseController{

    public function index(){
        $configMainSchema = new \Schemas\ConfigMainSchema();
        $mainConfigRow = SiteConfig::where('name', 'main')->first();
        if(!$mainConfigRow) {
            $mainConfigRow = new SiteConfig();
            $mainConfigRow->name = 'main';
        }
        if(Request::ajax() && Request::isMethod('post')) {
            $configData = Input::get('config', array());
            $mainConfigData = $configData;
            $mainConfigRow->value = json_encode($mainConfigData);
            $mainConfigRow->save();
            return Response::json(array(
                'success' => 1,
                'config' => $configData
            ));
        } else{
            return View::make('admin/config/index')->with(array(
                'configMainSchema' => $configMainSchema->getSchema(),
                'mainConfigData' => $mainConfigRow->value ? $mainConfigRow->value : array()
            ));
        }
    }

    public function widgets(){
        $widgetsSchema = new \Schemas\WidgetsSchema();
        $widgetsDataRow = SiteConfig::where('name', 'widgets')->first();
        if(!$widgetsDataRow) {
            $widgetsDataRow = new SiteConfig();
            $widgetsDataRow->name = 'widgets';
        }

        $this->_addMissingWidgetData($widgetsDataRow);

        if(Request::ajax() && Request::isMethod('post')) {
            $widgetsData = Input::get('widgets', array());

            $widgetsDataRow->value = json_encode($widgetsData);
            $widgetsDataRow->save();
            return Response::json(array(
                'success' => 1,
                'widgets' => $widgetsData
            ));
        } else{
            return View::make('admin/config/widgets')->with(array(
                'widgetsSchema' => $widgetsSchema->getSchema(),
                'widgetsData' => $widgetsDataRow->value ? $widgetsDataRow->value : array('widgets'=> array())
            ));
        }
    }

    public function languages() {
        $languagesSchema = new \Schemas\LanguagesSchema();
        $languagesDataRow = SiteConfig::where('name', 'languages')->first();
        if(Request::ajax() && Request::isMethod('post')) {
            $languagesData = Input::get('languages', array());

            $languagesDataRow->value = json_encode($languagesData);
            $languagesDataRow->save();
            return Response::json(array(
                'success' => 1,
                'languages' => $languagesData
            ));
        } else {
            return View::make('admin/config/languages')->with(array(
                'languagesSchema' => $languagesSchema->getSchema(),
                'languagesData' => $languagesDataRow->value ? $languagesDataRow->value : array()
            ));
        }
    }

    public function listConfig(){
        $listConfigSchema = new \Schemas\ListConfigSchema();
        $listConfigDataRow = SiteConfig::where('name', 'list')->first();
        if(!$listConfigDataRow) {
            $listConfigDataRow = new SiteConfig();
            $listConfigDataRow->name = 'list';
        }

        if(Request::ajax() && Request::isMethod('post')) {
            $listConfigData = Input::get('listConfig', array());

            $listConfigDataRow->value = json_encode($listConfigData);
            $listConfigDataRow->save();
            return Response::json(array(
                'success' => 1,
                'listConfig' => $listConfigData
            ));
        } else{
            return View::make('admin/config/list')->with(array(
                'listConfigSchema' => $listConfigSchema->getSchema(),
                'listConfigData' => $listConfigDataRow->value ? $listConfigDataRow->value : '{listConfig:[]}'
            ));
        }
    }

    public function emailConfig(){
        $emailConfigSchema = new \Schemas\EmailConfigSchema();
        $emailConfigDataRow = SiteConfig::where('name', 'email')->first();
        if(!$emailConfigDataRow) {
            $emailConfigDataRow = new SiteConfig();
            $emailConfigDataRow->name = 'email';
        }

        if(Request::ajax() && Request::isMethod('post')) {
            $emailConfigData = Input::get('emailConfig', array());

            $emailConfigDataRow->value = json_encode($emailConfigData);
            $emailConfigDataRow->save();
            return Response::json(array(
                'success' => 1,
                'emailConfig' => $emailConfigData
            ));
        } else{
            return View::make('admin/config/email')->with(array(
                'emailConfigSchema' => $emailConfigSchema->getSchema(),
                'emailConfigData' => $emailConfigDataRow->value ? $emailConfigDataRow->value : '{emailConfig:[]}'
            ));
        }
    }

    public function previewOgImage() {
        $imageObject = Image::make(public_path('images/sample-og-image.jpg'));
        $userImage = public_path('images/sample-profile-pic.jpg');
        ListController::processImageWithCredits($imageObject, $userImage, "John Doe");
        // create response and add encoded image data
        $response = Response::make($imageObject->encode('jpg'));

        // set content-type
        $response->header('Content-Type', 'image/png');

        // output
        return $response;
    }

    public function _addMissingWidgetData(&$widgetsDataRow) {
        $widgetsData = json_decode($widgetsDataRow->value);
        $widgetsDataSchemaObj = new \Schemas\WidgetsDataSchema();
        $widgetsDataSchema = json_decode($widgetsDataSchemaObj->getSchema());

        function hasWidgetSection($sectionId, $widgetsData) {
            $hasSection = false;
            array_map(function($widgetSection) use(&$hasSection, $sectionId){
                if($widgetSection->id == $sectionId) {
                    $hasSection = true;
                }
            }, $widgetsData->widgets);
            return $hasSection;
        }

        foreach ($widgetsDataSchema->widgets as $widgetSection) {
            if(!hasWidgetSection($widgetSection->id, $widgetsData)) {
                $widgetsData->widgets[] = $widgetSection;
            }
        }

        //Save the changes back
        $widgetsDataRow->value = json_encode($widgetsData);
    }
}