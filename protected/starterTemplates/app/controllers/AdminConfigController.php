<?php

class AdminConfigController extends BaseController{

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

    public function @@singular@@Config(){
        $@@singular@@ConfigSchema = new \Schemas\@@singular-pascalCase@@ConfigSchema();
        $@@singular@@ConfigDataRow = SiteConfig::where('name', '@@singular@@')->first();
        if(!$@@singular@@ConfigDataRow) {
            $@@singular@@ConfigDataRow = new SiteConfig();
            $@@singular@@ConfigDataRow->name = '@@singular@@';
        }

        if(Request::ajax() && Request::isMethod('post')) {
            $@@singular@@ConfigData = Input::get('@@singular@@Config', array());

            $@@singular@@ConfigDataRow->value = json_encode($@@singular@@ConfigData);
            $@@singular@@ConfigDataRow->save();
            return Response::json(array(
                'success' => 1,
                '@@singular@@Config' => $@@singular@@ConfigData
            ));
        } else{
            return View::make('admin/config/@@singular@@')->with(array(
                '@@singular@@ConfigSchema' => $@@singular@@ConfigSchema->getSchema(),
                '@@singular@@ConfigData' => $@@singular@@ConfigDataRow->value ? $@@singular@@ConfigDataRow->value : '{@@singular@@Config:[]}'
            ));
        }
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