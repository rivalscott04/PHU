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
        // Add foreign key constraint for users table if it doesn't exist
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'travel_id')) {
                $table->unsignedBigInteger('travel_id')->nullable();
            }
            
            // Check if foreign key constraint already exists
            $foreignKeys = Schema::getConnection()
                ->getDoctrineSchemaManager()
                ->listTableForeignKeys('users');
            
            $constraintExists = false;
            foreach ($foreignKeys as $foreignKey) {
                if (in_array('travel_id', $foreignKey->getLocalColumns())) {
                    $constraintExists = true;
                    break;
                }
            }
            
            if (!$constraintExists) {
                $table->foreign('travel_id')->references('id')->on('travels')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['travel_id']);
        });
    }
};
