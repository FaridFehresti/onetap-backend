<?php

// database/migrations/xxxx_xx_xx_create_actions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('actions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('action_type',['portfolio','contract','crm','redirect','booking']);
            $table->enum('status', ['active', 'inactive']);
            $table->string('link');
            $table->string('primary_color')->nullable();
            $table->string('secondary_color')->nullable();
            $table->string('tertiary_color')->nullable();
            $table->string('text_color')->nullable();
            $table->text('description')->nullable();
            $table->string('header_text')->nullable();
            $table->string('footer_text')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('address')->nullable();
            $table->string('company_name')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('position')->nullable();
            $table->string('person_title')->nullable();
            $table->string('contact_link')->nullable();
            $table->integer('maximum_participants')->nullable();
            $table->integer('minimum_participants')->nullable();
            $table->integer('duration')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('currency')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('booking_link')->nullable();
            $table->string('avatar')->nullable();
            $table->foreignId('card_id')->constrained('my_cards')->cascadeOnDelete();
            $table->unsignedBigInteger('scan_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actions');
    }
};
