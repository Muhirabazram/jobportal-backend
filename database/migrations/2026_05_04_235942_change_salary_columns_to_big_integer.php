<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('job_listings', function (Blueprint $table) {
            $table->bigInteger('salary_min')->nullable()->change();
            $table->bigInteger('salary_max')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('job_listings', function (Blueprint $table) {
            $table->decimal('salary_min', 10, 2)->nullable()->change();
            $table->decimal('salary_max', 10, 2)->nullable()->change();
        });
    }
};
