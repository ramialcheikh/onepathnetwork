<?php namespace Jitheshgopan\AppInstaller;

use Illuminate\Support\ServiceProvider;

class AppInstallerServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('jitheshgopan/app-installer');
		define('INSTALLER_VENDOR_PATH', 'jitheshgopan/app-installer');
		define('INSTALLER_VENDOR_NAME', 'jitheshgopan');
		define('INSTALLER_NAMESPACE', 'app-installer');
		include __DIR__ . '/../../routes.php';
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
