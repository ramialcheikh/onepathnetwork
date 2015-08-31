<?php
	namespace Schemas;
	
	class WidgetsDataSchema extends BaseSchema {
		public function getSchema(){
			$schemaPath = $this->getSchemaPath('WidgetsData');
            if(!file_exists($schemaPath)) {
                $schemaPath = $this->getSchemaPath('widgetsData');
            }
			if(\File::exists($schemaPath)) {
				return \File::get($schemaPath);
			} else{
				throw new \Exception('Schema file not found');
			}
		}
	}