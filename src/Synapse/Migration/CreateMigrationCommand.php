<?php

namespace Synapse\Migration;

use Synapse\Command\CommandInterface;
use Synapse\View\Migration\Create as CreateMigrationView;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * CLI command for creating database migrations. Based on Kohana Minion task-migrations.
 *
 * Example usage:
 *     ./console migrations:create 'Add email field to users table'
 */
class CreateMigrationCommand implements CommandInterface
{
    /**
     * View for new migration files
     *
     * @var \Synapse\View\Migration\Create
     */
    protected $newMigrationView;

    protected $migrationNamespace = 'Application\\Migrations\\';

    /**
     * Set the injected new migration view, call the parent constructor
     *
     * @param CreateMigrationView $newMigrationView
     */
    public function __construct(CreateMigrationView $newMigrationView)
    {
        $this->newMigrationView = $newMigrationView;
    }

    /**
     * Set the namespace for the migrations
     *
     * @param string $namespace
     *
     * @return $this
     */
    public function setMigrationNamespace($namespace)
    {
        $this->migrationNamespace = $namespace;
        return $this;
    }

    /**
     * Execute this console command, in order to create a new migration
     *
     * @param  InputInterface  $input  Command line input interface
     * @param  OutputInterface $output Command line output interface
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $description = $input->getArgument('description');
        $time        = date('YmdHis');
        $classname   = $this->generateClassName($time, $description);
        $filepath    = APPDIR.'/src/'.$this->namespaceToPath().$classname.'.php';

        if (! is_dir(dirname($filepath))) {
            mkdir(dirname($filepath), 0775, true);
        }

        $view = $this->newMigrationView;

        $view->description($description);
        $view->classname($classname);
        $view->timestamp($time);

        file_put_contents($filepath, (string) $view);

        $message = '  Created migration file '.$filepath;

        // Output message padded by newlines
        $output->write(['', $message, ''], true);
    }

    /**
     * Get the name of the new migration class
     *
     * Converts description to PascalCase and prepends constant string and timestamp.
     * Example:
     *     // From:
     *     Example description of a migration
     *
     *     // To:
     *     Migration20140220001906ExampleDescriptionOfAMigration
     *
     * @param  string $time        Timestamp
     * @param  string $description User-provided description of new migration
     * @return string
     */
    protected function generateClassName($time, $description)
    {
        $description = substr(strtolower($description), 0, 30);
        $description = ucwords($description);
        $description = preg_replace('/[^a-zA-Z]+/', '', $description);
        return 'Migration'.$time.$description;
    }

    /**
     * Returns the path to the migration namespace
     *
     * @return string
     */
    protected function namespaceToPath()
    {
        return str_replace('\\', '/', $this->migrationNamespace);
    }
}
