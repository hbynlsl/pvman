<?php
/**
 * Migration Service
 *
 * Initializes and manages the Laravel-style migration infrastructure for webman.
 * Provides a unified way to access the Migrator, Schema builder, and migration repository.
 */

namespace support\Database;

use Illuminate\Container\Container as IlluminateContainer;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminate\Database\Migrations\MigrationCreator;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Facade;
use support\Db;

class MigrationService
{
    /**
     * @var Migrator|null
     */
    protected static ?Migrator $migrator = null;

    /**
     * @var MigrationCreator|null
     */
    protected static ?MigrationCreator $creator = null;

    /**
     * @var DatabaseMigrationRepository|null
     */
    protected static ?DatabaseMigrationRepository $repository = null;

    /**
     * @var ConnectionResolverInterface|null
     */
    protected static ?ConnectionResolverInterface $resolver = null;

    /**
     * @var bool
     */
    protected static bool $initialized = false;

    /**
     * Initialize the migration infrastructure.
     *
     * @return void
     */
    public static function init(): void
    {
        if (self::$initialized) {
            return;
        }
        self::$initialized = true;

        $container = IlluminateContainer::getInstance();

        // Register 'db' binding if not already present (maps to DatabaseManager)
        if (!$container->bound('db')) {
            static::registerDbBinding($container);
        }

        // Register db.schema binding if not already registered
        if (!$container->bound('db.schema')) {
            $container->singleton('db.schema', function ($app) {
                return $app['db']->connection()->getSchemaBuilder();
            });
        }

        // Set facade application container so facades like Schema:: work
        Facade::setFacadeApplication($container);
    }

    /**
     * Register the 'db' binding in the container.
     *
     * @param IlluminateContainer $container
     * @return void
     */
    protected static function registerDbBinding(IlluminateContainer $container): void
    {
        // Access the DatabaseManager from the Capsule's global instance via reflection.
        // PHP 8.1+ makes protected properties accessible without setAccessible().
        try {
            $ref = new \ReflectionProperty(\Illuminate\Database\Capsule\Manager::class, 'instance');
            $capsule = $ref->getValue(null);
            if ($capsule && method_exists($capsule, 'getDatabaseManager')) {
                $container->instance('db', $capsule->getDatabaseManager());
                return;
            }
        } catch (\ReflectionException) {
            // Fall through to adapter
        }

        // Fallback: create a thin adapter wrapping Db
        $container->instance('db', static::createFallbackResolver());
    }

    /**
     * Get the migration repository.
     *
     * @return DatabaseMigrationRepository
     */
    public static function getRepository(): DatabaseMigrationRepository
    {
        if (self::$repository === null) {
            self::init();
            $resolver = static::getConnectionResolver();
            $table = config('migration.repository_table', 'migrations');
            self::$repository = new DatabaseMigrationRepository($resolver, $table);
        }
        return self::$repository;
    }

    /**
     * Get the Migrator instance.
     *
     * @param OutputInterface|null $output
     * @return Migrator
     */
    public static function getMigrator($output = null): Migrator
    {
        if (self::$migrator === null) {
            self::init();
            $files = new Filesystem();
            $resolver = static::getConnectionResolver();
            $repository = self::getRepository();

            self::$migrator = new Migrator($repository, $resolver, $files);

            // Attempt to set event dispatcher if available
            try {
                $container = IlluminateContainer::getInstance();
                if ($container->bound('events')) {
                    self::$migrator->setEventDispatcher($container->make('events'));
                }
            } catch (\Throwable) {
                // Events not available, continue without them
            }
        }

        if ($output !== null) {
            self::$migrator->setOutput($output);
        }

        return self::$migrator;
    }

    /**
     * Get the MigrationCreator instance.
     *
     * @return MigrationCreator
     */
    public static function getCreator(): MigrationCreator
    {
        if (self::$creator === null) {
            self::init();
            $files = new Filesystem();
            $customStubPath = dirname(__DIR__, 2) . '/vendor/illuminate/database/Migrations/stubs';
            self::$creator = new MigrationCreator($files, $customStubPath);
        }
        return self::$creator;
    }

    /**
     * Get the migration path.
     *
     * @return string
     */
    public static function getMigrationPath(): string
    {
        return base_path(config('migration.path', 'database/migrations'));
    }

    /**
     * Get all migration paths.
     *
     * @return array
     */
    public static function getMigrationPaths(): array
    {
        return [self::getMigrationPath()];
    }

    /**
     * Ensure the migration repository exists.
     *
     * @return void
     */
    public static function ensureRepositoryExists(): void
    {
        $repository = self::getRepository();
        if (!$repository->repositoryExists()) {
            $repository->createRepository();
        }
    }

    /**
     * Get the connection resolver.
     *
     * @return ConnectionResolverInterface
     */
    protected static function getConnectionResolver(): ConnectionResolverInterface
    {
        if (self::$resolver === null) {
            $container = IlluminateContainer::getInstance();

            // Try to use DatabaseManager from 'db' container binding
            if ($container->bound('db')) {
                $db = $container->make('db');
                if ($db instanceof ConnectionResolverInterface) {
                    self::$resolver = $db;
                    return self::$resolver;
                }
            }

            // Fallback: create a simple resolver adapting Db static API
            self::$resolver = static::createFallbackResolver();
        }
        return self::$resolver;
    }

    /**
     * Create a fallback connection resolver that wraps Db.
     *
     * @return ConnectionResolverInterface
     */
    protected static function createFallbackResolver(): ConnectionResolverInterface
    {
        return new class implements ConnectionResolverInterface {
            private string $defaultConnection = '';

            public function connection($name = null)
            {
                return Db::connection($name ?: $this->getDefaultConnection());
            }

            public function getDefaultConnection()
            {
                if ($this->defaultConnection === '') {
                    $this->defaultConnection = config('database.default', 'mysql');
                }
                return $this->defaultConnection;
            }

            public function setDefaultConnection($name)
            {
                $this->defaultConnection = $name;
            }
        };
    }

    /**
     * Reset all singleton instances (useful for testing).
     *
     * @return void
     */
    public static function reset(): void
    {
        self::$migrator = null;
        self::$creator = null;
        self::$repository = null;
        self::$resolver = null;
        self::$initialized = false;
    }
}
