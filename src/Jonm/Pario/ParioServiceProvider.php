<?php 
		namespace Jonm\Pario;
		use Illuminate\Support\ServiceProvider;

		class ParioServiceProvider extends ServiceProvider {

			/**
			 * Register the service provider.
			 *
			 * @return void
			 */
			public function register()
			{
				$this->registerPario();
			}


			/**
			 * Register the Pario instance.
			 *
			 * @return void
			 */
			protected function registerPario()
			{
				$this->app->bindShared('pario', function($app)
				{
					return new Pario();
				});
			}
			
		}
