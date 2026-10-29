<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Editable site content. Each row overrides one section of
     * config/portfolio.php (by dot-path key) with the admin's version.
     */
    public function up(): void
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            // Plain text, not a JSON column: MySQL's JSON type re-sorts object
            // keys, and the order of saved content matters on the site.
            $table->longText('value');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
