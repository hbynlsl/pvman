<?php
/**
 * Migrate Fresh Command
 *
 * Drops all tables and re-runs all migrations.
 * Usage: php webman migrate:fresh
 */

namespace app\command;

use support\Database\MigrationService;
use support\Db;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('migrate:fresh', 'Drop all tables and re-run all migrations')]
class MigrateFreshCommand extends Command
{
    protected function configure(): void
    {
        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'Force the operation to run in production');
        $this->addOption('drop-views', null, InputOption::VALUE_NONE, 'Drop all views as well');
        $this->addOption('drop-types', null, InputOption::VALUE_NONE, 'Drop all types as well');
        $this->setHelp('
Drop all tables and re-run all migrations.

Examples:
  php webman migrate:fresh
  php webman migrate:fresh --drop-views
        ');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dropViews = (bool)$input->getOption('drop-views');
        $dropTypes = (bool)$input->getOption('drop-types');

        try {
            $connection = Db::connection();
            $schema = $connection->getSchemaBuilder();

            $output->writeln('<info>Dropping all tables...</info>');

            // Disable foreign key checks for MySQL/SQLite
            $driver = $connection->getDriverName();
            if ($driver === 'mysql') {
                $connection->statement('SET FOREIGN_KEY_CHECKS=0');
            } elseif ($driver === 'sqlite') {
                $connection->statement('PRAGMA foreign_keys = OFF');
            }

            // Drop all tables
            $schema->dropAllTables();

            // Drop all views if requested
            if ($dropViews) {
                $output->writeln('<info>Dropping all views...</info>');
                $schema->dropAllViews();
            }

            // Drop all types if requested (PostgreSQL)
            if ($dropTypes && $driver === 'pgsql') {
                $output->writeln('<info>Dropping all types...</info>');
                $schema->dropAllTypes();
            }

            // Re-enable foreign key checks
            if ($driver === 'mysql') {
                $connection->statement('SET FOREIGN_KEY_CHECKS=1');
            } elseif ($driver === 'sqlite') {
                $connection->statement('PRAGMA foreign_keys = ON');
            }

            $output->writeln('<info>All tables dropped successfully.</info>');

            // Now run migrations
            MigrationService::ensureRepositoryExists();
            $migrator = MigrationService::getMigrator($output);
            $paths = MigrationService::getMigrationPaths();

            $migrator->run($paths);

            $output->writeln('<info>Migration fresh completed successfully.</info>');

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $output->writeln("<error>Fresh migration failed: {$e->getMessage()}</error>");
            return Command::FAILURE;
        }
    }
}
