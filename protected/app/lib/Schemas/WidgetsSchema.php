<?php
	namespace Schemas;
	
	class WidgetsSchema extends BaseSchema {
		public function getSchema(){
			$schemaPath = $this->getSchemaPath('Widgets');
			if(\File::exists($schemaPath)) {
				return \File::get($schemaPath);
			} else{
				throw new \Exception('Schema file not found');
			}
		}
	}