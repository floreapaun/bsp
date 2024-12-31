<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateCategoriesTable extends Migration
{
    public function up()
    {
        // Create the categories table
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Seed the categories table with some default data
        DB::table('categories')->insert([
            ['name' => 'Technology'],
            ['name' => 'Health'],
            ['name' => 'Lifestyle'],
            ['name' => 'Business'],
            ['name' => 'Entertainment'],
            ['name' => 'Education'],
            ['name' => 'Sports'],
            ['name' => 'Travel'],
            ['name' => 'Food & Drink'],
            ['name' => 'Fashion'],
            ['name' => 'Art & Culture'],
            ['name' => 'Science'],
            ['name' => 'Music'],
            ['name' => 'Gaming'],
            ['name' => 'Automotive'],
            ['name' => 'Real Estate'],
            ['name' => 'Finance'],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
