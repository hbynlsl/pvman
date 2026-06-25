<?php
/**
 * Base Migration Class
 *
 * Laravel-style migration base class for webman.
 * All migration files should extend this class.
 * 
 * Usage:
 * ```php
 * return new class extends Migration {
 *     public function up(): void
 *     {
 *         Schema::create('users', function ($table) {
 *             $table->id();
 *             $table->timestamps();
 *         });
 *     }
 *     public function down(): void
 *     {
 *         Schema::dropIfExists('users');
 *     }
 * };
 * ```
 */

namespace support\Database;

use Illuminate\Database\Migrations\Migration as BaseMigration;
use Illuminate\Support\Facades\Schema;

abstract class Migration extends BaseMigration
{
    /**
     * Run the migrations.
     */
    abstract public function up(): void;

    /**
     * Reverse the migrations.
     */
    abstract public function down(): void;

    /**
     * Get a schema builder instance for the connection.
     *
     * @param string|null $connection
     * @return \Illuminate\Database\Schema\Builder
     */
    protected function schema(?string $connection = null)
    {
        return Schema::connection($connection ?? $this->getConnection());
    }
}
