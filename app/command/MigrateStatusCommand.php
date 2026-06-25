<?php
/**
 * Migrate Status Command
 *
 * Show the status of each migration.
 * Usage: php webman migrate:status
 */

namespace app\command;

use support\Database\MigrationService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('migrate:status', 'Show the status of each migration')]
class MigrateStatusCommand extends Command
{
    protected function configure(): void
    {
        $this->setHelp('
Show the status of each migration.

Examples:
  php webman migrate:status
        ');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $migrator = MigrationService::getMigrator($output);
            $repository = MigrationService::getRepository();
            $paths = MigrationService::getMigrationPaths();

            // Get all migration files
            $files = $migrator->getMigrationFiles($paths);

            if (empty($files)) {
                $output->writeln('<comment>No migrations found.</comment>');
                return Command::SUCCESS;
            }

            // Get ran migrations (handle missing repository table gracefully)
            $ran = [];
            $batches = [];
            try {
                $ran = $repository->getRan();
                $batches = $repository->getMigrationBatches();
            } catch (\Throwable $e) {
                // Migrations table doesn't exist yet — all migrations are pending
            }

            $table = new Table($output);
            $table->setHeaders(['Ran?', 'Migration', 'Batch']);

            foreach ($files as $file) {
                $name = $migrator->getMigrationName($file);
                $isRan = in_array($name, $ran);
                $batch = $isRan ? ($batches[$name] ?? '?') : '---';

                $status = $isRan ? '<info>Yes</info>' : '<comment>No</comment>';
                $table->addRow([$status, $name, $batch]);
            }

            $table->render();

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $output->writeln("<error>Status check failed: {$e->getMessage()}</error>");
            return Command::FAILURE;
        }
    }
}
