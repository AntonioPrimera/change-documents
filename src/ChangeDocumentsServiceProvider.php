<?php

namespace AntonioPrimera\ChangeDocuments;

use Illuminate\Support\ServiceProvider;

class ChangeDocumentsServiceProvider extends ServiceProvider
{
	
	public function register()
	{
		$this->mergeConfigFrom(
			__DIR__ . '/../config/change-documents.php',
			'change-documents'
		);
	}
	
	public function boot()
	{
		$this->loadMigrationsFrom(__DIR__ . '/../database');
	}
}