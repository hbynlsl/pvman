<?php
/**
 * Migration Configuration
 *
 * Laravel-style database migration settings.
 */
return [
    // Migration tracking table
    'table' => 'migrations',

    // Migration file storage path (relative to base_path)
    'path' => 'database/migrations',

    // Table to store migration records
    'repository_table' => 'migrations',

    // Database connection to use for migration tracking (null = default connection)
    'connection' => null,
];
