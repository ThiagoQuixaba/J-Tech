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
 * @since         1.7.4
 * @license       https://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Bake\Command;

use Cake\Console\Arguments;
use Cake\Utility\Inflector;

/**
 * Console Command generator.
 */
class CommandCommand extends SimpleBakeCommand
{
    /**
     * Task name used in path generation.
     *
     * @var string
     */
    public string $pathFragment = 'Command/';

    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return 'command';
    }

    /**
     * @inheritDoc
     */
    public function fileName(string $name): string
    {
        return $name . 'Command.php';
    }

    /**
     * @inheritDoc
     */
    public function template(): string
    {
        return 'Bake.Command/command';
    }

    /**
     * Get template data.
     *
     * @param \Cake\Console\Arguments $arguments Arguments object.
     * @return array
     * @phpstan-return array<string, mixed>
     */
    public function templateData(Arguments $arguments): array
    {
        $data = parent::templateData($arguments);

        $data['command_name'] = Inflector::underscore(str_replace(
            '.',
            ' ',
            $arguments->getArgument('name'),
        ));

        return $data;
    }
}
