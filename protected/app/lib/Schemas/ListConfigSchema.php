<?php
	namespace Schemas;
	
	class ListConfigSchema extends BaseSchema {
		public function getSchema(){
			$schemaPath = $this->getSchemaPath('ListConfig');
			if(\File::exists($schemaPath)) {
				return \File::get($schemaPath);
			} else{
				throw new \Exception('Schema file not found');
			}
		}
	}