<?php
/**
 * Migrate Rollback Command
 *
 * Rolls back the last database migration batch.
 * Usage: php webman migrate:rollback
 */

namespace app\command;

use support\Database\MigrationService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('migrate:rollback', 'Rollback the last database migration')]
class MigrateRollbackCommand extends Command
{
    protected function configure(): void
    {
        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'Force the operation to run in production');
        $this->addOption('pretend', null, InputOption::VALUE_NONE, 'Dump SQL queries instead of running them');
        $this->addOption('step', 's', InputOption::VALUE_REQUIRED, 'The number of migrations to rollback', 1);
        $this->addOption('batch', 'b', InputOption::VALUE_REQUIRED, 'Rollback to a specific batch number');
        $this->setHelp('
Rollback the last database migration.

Examples:
  php webman migrate:rollback
  php webman migrate:rollback --step=3
  php webman migrate:rollback --batch=1
  php webman migrate:rollback --pretend
        ');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $pretend = (bool)$input->getOption('pretend');
        $step = (int)$input->getOption('step');
        $batch = $input->getOption('batch');

        try {
            $migrator = MigrationService::getMigrator($output);
            $paths = MigrationService::getMigrationPaths();

            $migrator->rollback($paths, [
                'pretend' => $pretend,
                'step' => $step,
                'batch' => $batch !== null ? (int)$batch : null,
            ]);

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $output->writeln("<error>Rollback failed: {$e->getMessage()}</error>");
            return Command::FAILURE;
        }
    }
}
