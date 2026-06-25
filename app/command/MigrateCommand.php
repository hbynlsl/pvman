<?php
/**
 * Migrate Command
 *
 * Runs all pending migrations.
 * Usage: php webman migrate
 */

namespace app\command;

use support\Database\MigrationService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('migrate', 'Run the database migrations')]
class MigrateCommand extends Command
{
    protected function configure(): void
    {
        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'Force the operation to run in production');
        $this->addOption('pretend', null, InputOption::VALUE_NONE, 'Dump SQL queries instead of running them');
        $this->addOption('step', 's', InputOption::VALUE_OPTIONAL, 'Force the migrations to be run so they can be rolled back individually', false);
        $this->setHelp('
Run all pending database migrations.

Examples:
  php webman migrate
  php webman migrate --force
  php webman migrate --pretend
        ');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $pretend = (bool)$input->getOption('pretend');
        $step = $input->getOption('step');

        try {
            // Ensure the migration repository exists
            MigrationService::ensureRepositoryExists();

            $migrator = MigrationService::getMigrator($output);
            $paths = MigrationService::getMigrationPaths();

            // Run migrations
            $migrator->run($paths, [
                'pretend' => $pretend,
                'step' => $step !== false ? (int)$step : false,
            ]);

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $output->writeln("<error>Migration failed: {$e->getMessage()}</error>");
            $output->writeln("<error>{$e->getTraceAsString()}</error>");
            return Command::FAILURE;
        }
    }
}
