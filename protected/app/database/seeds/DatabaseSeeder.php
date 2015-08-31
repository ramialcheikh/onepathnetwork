<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();
        try {
            //dd(__FILE__ . 'import-data.sql');
            $sqlData = file_get_contents(dirname(__FILE__) . '/import-data.sql');
            DB::unprepared($sqlData);
        } catch(Exception $e) {

        }

		// $this->call('UserTableSeeder');
	}

}
