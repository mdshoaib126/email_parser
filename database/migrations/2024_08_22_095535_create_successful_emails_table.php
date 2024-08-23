<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuccessfulEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('successful_emails', function (Blueprint $table) {
            $table->id();
            $table->mediumInteger('affiliate_id')->unsigned();
            $table->text('envelope')->nullable();
            $table->string('from', 255)->nullable();
            $table->text('subject')->nullable();
            $table->string('dkim', 255)->nullable();
            $table->string('SPF', 255)->nullable();
            $table->float('spam_score')->nullable();
            $table->longText('email');
            $table->longText('raw_text')->nullable();
            $table->string('sender_ip', 50)->nullable();
            $table->text('to')->nullable();
            $table->integer('timestamp')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('successful_emails');
    }
}
