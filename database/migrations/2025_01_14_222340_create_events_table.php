<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Event;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable(Event::TABLE)) {
            Schema::create(Event::TABLE, function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->text('description');
                $table->datetime('date')->unique();
                $table->integer('availability')->unsigned();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
