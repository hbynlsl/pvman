<?php
/**
 * Make Migration Command
 *
 * Creates a new migration file in the database/migrations directory.
 * Usage: php webman make:migration create_users_table
 */

namespace app\command;

use support\Database\MigrationService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('make:migration', 'Create a new migration file')]
class MakeMigrationCommand extends Command
{
    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The name of the migration (e.g. create_users_table)');
        $this->addOption('create', 'c', InputOption::VALUE_OPTIONAL, 'The table to be created');
        $this->addOption('table', 't', InputOption::VALUE_OPTIONAL, 'The table to migrate');
        $this->addOption('path', 'p', InputOption::VALUE_OPTIONAL, 'The location where the migration file should be created');
        $this->setHelp('
Creates a new migration file.

Examples:
  php webman make:migration create_users_table
  php webman make:migration add_status_to_users_table --table=users
  php webman make:migration create_posts_table --create=posts
        ');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $create = $input->getOption('create');
        $table = $input->getOption('table');
        $pathOption = $input->getOption('path');

        $path = $pathOption ? base_path($pathOption) : MigrationService::getMigrationPath();

        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        $creator = MigrationService::getCreator();

        try {
            if ($create) {
                $file = $creator->create($name, $path, $create, true);
            } elseif ($table) {
                $file = $creator->create($name, $path, $table);
            } else {
                $file = $creator->create($name, $path);
            }

            $relativePath = str_replace(base_path() . '/', '', $file);
            $output->writeln("<info>Created migration:</info> {$relativePath}");
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");
            return Command::FAILURE;
        }
    }
}
