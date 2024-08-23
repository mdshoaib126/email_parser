<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

class AddTestUserToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        User::create([
            'name' => 'Test User',
            'email' => 'testemail@gmail.com',
            'password' => bcrypt('123456'),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        User::where('email', 'testemail@gmail.com')->delete();
    }
}
