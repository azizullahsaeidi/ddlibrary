<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToResourceCommentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('resource_comments', function(Blueprint $table)
		{
			$table->foreign('resource_id', 'resource_comments_ibfk_1')->references('id')->on('resources')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('resource_comments', function(Blueprint $table)
		{
			if (DB::getDriverName() !== 'sqlite') {
				$table->dropForeign('resource_comments_ibfk_1');
			}
		});
	}

}
