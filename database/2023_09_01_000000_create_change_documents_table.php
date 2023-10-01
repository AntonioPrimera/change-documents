<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('change_documents', function (Blueprint $table) {
			$table->id();
			
			$table->nullableMorphs('model');				//model is optional (can be some other scope e.g. system, config etc.)
			
			$table->string('change_type')->nullable();
			$table->json('changes')->nullable();
			
			$table->foreignId('changed_by')
				->nullable()
				->constrained(Config::get('change-documents.user_table', 'users'))
				->onDelete('cascade');
			$table->string('medium')->nullable();		//web, api, cli, etc.
			$table->string('ip_address')->nullable();	//ipv4 or ipv6
			$table->string('user_agent')->nullable();	//browser, device, etc.
			$table->string('url')->nullable();			//url of the request (if applicable)
			$table->string('method')->nullable();		//method of the request (if applicable)
			$table->string('route')->nullable();			//route of the request (if applicable)
			$table->string('command')->nullable();		//command that was run (if applicable)
			
			$table->string('comment')->nullable();		//comment about the change
			
			$table->timestamps();
		});
	}
	
	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('change_documents');
	}
};
