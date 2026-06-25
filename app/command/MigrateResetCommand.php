<?php
/**
 * Migrate Reset Command
 *
 * Rolls back all database migrations.
 * Usage: php webman migrate:reset
 */

namespace app\command;

use support\Database\MigrationService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('migrate:reset', 'Rollback all database migrations')]
class MigrateResetCommand extends Command
{
    protected function configure(): void
    {
        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'Force the operation to run in production');
        $this->addOption('pretend', null, InputOption::VALUE_NONE, 'Dump SQL queries instead of running them');
        $this->setHelp('
Rollback all database migrations.

Examples:
  php webman migrate:reset
  php webman migrate:reset --pretend
        ');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $pretend = (bool)$input->getOption('pretend');

        try {
            $migrator = MigrationService::getMigrator($output);
            $paths = MigrationService::getMigrationPaths();

            $migrator->reset($paths, $pretend);

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $output->writeln("<error>Reset failed: {$e->getMessage()}</error>");
            return Command::FAILURE;
        }
    }
}
