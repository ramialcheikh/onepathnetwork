<?php
	namespace Schemas;
	
	class EmailConfigSchema extends BaseSchema {
		public function getSchema(){
			$schemaPath = $this->getSchemaPath('EmailConfig');
			if(\File::exists($schemaPath)) {
				return \File::get($schemaPath);
			} else{
				throw new \Exception('Schema file not found');
			}
		}
	}