<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Migrations\Command;

use Bake\Command\SimpleBakeCommand;
use Bake\Utility\TemplateRenderer;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Plugin;
use Cake\Utility\Inflector;
use Migrations\Util\Util;

/**
 * Task class for generating migration snapshot files.
 */
abstract class BakeSimpleMigrationCommand extends SimpleBakeCommand
{
    public const DEFAULT_MIGRATION_FOLDER = 'Migrations';

    protected const RESERVED_KEYWORDS = [
        'abstract', 'and', 'array', 'as', 'break', 'callable', 'case', 'catch', 'class', 'clone', 'const',
        'continue', 'declare', 'default', 'die', 'do', 'echo', 'else', 'elseif', 'empty', 'enddeclare', 'endfor',
        'endforeach', 'endif', 'endswitch', 'endwhile', 'eval', 'exit', 'extends', 'final', 'finally', 'for', 'foreach',
        'function', 'global', 'goto', 'if', 'implements', 'include', 'include_once', 'instanceof', 'insteadof', 'interface',
        'isset', 'list', 'namespace', 'new', 'or', 'parent', 'private', 'protected', 'public', 'return','static',
    ];

    /**
     * path to Migration directory
     *
     * @var string
     */
    public string $pathFragment = 'config';

    /**
     * Console IO
     *
     * @var \Cake\Console\ConsoleIo|null
     */
    protected ?ConsoleIo $io = null;

    /**
     * Arguments
     *
     * @var \Cake\Console\Arguments|null
     */
    protected ?Arguments $args = null;

    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return 'migration';
    }

    /**
     * @inheritDoc
     */
    public function fileName($name): string
    {
        $name = $this->getMigrationName($name);
        $timestamp = Util::getCurrentTimestamp();
        $suffix = '_' . Inflector::camelize($name) . '.php';

        $path = $this->getPath($this->args);
        $offset = 0;
        while (glob($path . $timestamp . '_*.php')) {
            $timestamp = Util::getCurrentTimestamp(++$offset);
        }

        return $timestamp . $suffix;
    }

    /**
     * @inheritDoc
     */
    public function getPath(Arguments $args): string
    {
        $migrationFolder = $this->pathFragment . DS . $args->getOption('source') . DS;
        $path = ROOT . DS . $migrationFolder;
        if ($this->plugin) {
            $path = $this->_pluginPath($this->plugin) . $migrationFolder;
        }

        return str_replace('/', DS, $path);
    }

    /**
     * @inheritDoc
     */
    public function execute(Arguments $args, ConsoleIo $io): ?int
    {
        if (!Plugin::isLoaded('Bake')) {
            $io->err('Bake plugin is not loaded. Please load it first to generate a migration.');
            $this->abort();
        }
        $this->extractCommonProperties($args);
        $name = $args->getArgumentAt(0);
        if (!$name) {
            $io->err('You must provide a name to bake a ' . $this->name());
            $this->abort();
        }
        $name = $this->_getName($name);
        $name = Inflector::camelize($name);
        $this->bake($name, $args, $io);

        return static::CODE_SUCCESS;
    }

    /**
     * @inheritDoc
     */
    public function bake(string $name, Arguments $args, ConsoleIo $io): void
    {
        $this->io = $io;
        $this->args = $args;
        if ($this->isReservedKeyword($name)) {
            $prefix = $io->ask('Reserved keywords cannot be used for class names. What prefix would you like to use? Defaults to `Migration`.', 'Migration');
            $name = $prefix . ucfirst($name);
        }

        $migrationWithSameName = glob($this->getPath($args) . '*_' . $name . '.php');
        if ($migrationWithSameName) {
            $force = $args->getOption('force');
            if (!$force) {
                $io->abort(
                    sprintf(
                        'A migration with the name `%s` already exists. Please use a different name.',
                        $name,
                    ),
                );
            }

            $io->info(sprintf('A migration with the name `%s` already exists, it will be deleted.', $name));
            foreach ($migrationWithSameName as $migration) {
                $io->info(sprintf('Deleting migration file `%s`...', $migration));
                if (unlink($migration)) {
                    $io->success(sprintf('Deleted `%s`', $migration));
                } else {
                    $io->err(sprintf('An error occurred while deleting `%s`', $migration));
                }
            }
        }

        $renderer = new TemplateRenderer($this->theme);
        $renderer->set('name', $name);
        $renderer->set($this->templateData($args));
        $contents = $renderer->generate($this->template());

        $path = $this->getPath($args);
        $filename = $path . $this->fileName($name);
        $this->createFile($filename, $contents, $args, $io);

        $emptyFile = $path . '.gitkeep';
        $this->deleteEmptyFile($emptyFile, $io);
    }

    /**
     * @param string $path Where to put the file.
     * @param string $contents Content to put in the file.
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return bool Success
     */
    protected function createFile(string $path, string $contents, Arguments $args, ConsoleIo $io): bool
    {
        return $io->createFile($path, $contents);
    }

    /**
     * Returns a class name for the migration class
     *
     * If the name is invalid, the task will exit
     *
     * @param string|null $name Name for the generated migration
     * @return string Name of the migration file
     */
    protected function getMigrationName(?string $name = null): string
    {
        if (!$name) {
            $this->io->abort('Choose a migration name to bake in CamelCase format');
        }

        $name = $this->_getName($name);
        $name = Inflector::camelize($name);

        if (!preg_match('/^[A-Z]{1}[a-zA-Z0-9]+$/', $name)) {
            $this->io->abort('The className is not correct. The className can only contain "A-Z" and "0-9" and has to start with a letter.');
        }

        return $name;
    }

    /**
     * Gets the option parser instance and configures it.
     *
     * @param \Cake\Console\ConsoleOptionParser $parser Option parser to update.
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser = $this->_setCommonOptions($parser);

        $parser->setDescription(
            'Bake migration class.',
        )->addOption('no-test', [
            'boolean' => true,
            'help' => 'Do not generate a test skeleton.',
        ])->addOption('source', [
            'short' => 's',
            'default' => self::DEFAULT_MIGRATION_FOLDER,
            'help' => 'Name of the folder in which the migration should be saved.',
        ]);

        $options = $parser->options();
        if (!isset($options['force'])) {
            $parser->addOption('force', [
                'short' => 'f',
                'boolean' => true,
                'help' => 'Force overwriting existing file if a migration already exists with the same name.',
            ]);
        }

        return $parser;
    }

    /**
     * If reserved PHP keyword.
     *
     * @param string $name
     * @return bool
     */
    protected function isReservedKeyword(string $name): bool
    {
        return in_array(strtolower($name), static::RESERVED_KEYWORDS);
    }
}
