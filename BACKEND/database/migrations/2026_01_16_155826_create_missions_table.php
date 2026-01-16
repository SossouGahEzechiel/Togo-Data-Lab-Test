<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('missions', function (Blueprint $table) {
			$table->uuid('id')->primary();
			$table->string('label');
			$table->text('description');
			$table->timestamp('from');
			$table->timestamp('to')->nullable();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('missions');
	}
};
