<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Reservation;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable(Reservation::TABLE)) {
            Schema::create(Reservation::TABLE, function (Blueprint $table) {
                $table->foreignId('user_id')->constrained('users');
                $table->foreignId('event_id')->constrained('events');
                $table->integer('number_of_tickets')->unsigned();
                $table->timestamps(); 
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
