<?php
	namespace Schemas;
	
	class @@singular-pascalCase@@ConfigSchema extends BaseSchema {
		public function getSchema(){
			$schemaPath = $this->getSchemaPath('@@singular-pascalCase@@Config');
			if(\File::exists($schemaPath)) {
				return \File::get($schemaPath);
			} else{
				throw new \Exception('Schema file not found');
			}
		}
	}