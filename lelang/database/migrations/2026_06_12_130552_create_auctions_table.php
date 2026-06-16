<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('auctions', function (Blueprint $table) {
        $table->id();

        $table->foreignId('user_id')
              ->constrained()
              ->cascadeOnDelete();

        $table->string('title');

        $table->text('description');

        $table->string('image')
              ->nullable();

        $table->decimal('starting_price',15,2);

        $table->decimal('bid_increment',15,2);

        $table->dateTime('start_time');

        $table->dateTime('end_time');

        $table->enum(
            'status',
            [
                'scheduled',
                'active',
                'ended'
            ]
        )->default('scheduled');

        $table->timestamps();
        
    });
}



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auctions');
    }
};
