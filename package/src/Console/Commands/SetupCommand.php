<?php

namespace InetStudio\Instagram\Console\Commands;

use InetStudio\AdminPanel\Base\Console\Commands\BaseSetupCommand;

/**
 * Class SetupCommand
 * @package InetStudio\Instagram\Console\Commands
 */
class SetupCommand extends BaseSetupCommand
{
    /**
     * Имя команды.
     *
     * @var string
     */
    protected $name = 'inetstudio:instagram:setup';

    /**
     * Описание команды.
     *
     * @var string
     */
    protected $description = 'Setup instagram package';

    /**
     * Инициализация команд.
     *
     * @return void
     */
    protected function initCommands(): void
    {
        $this->calls = [
            [
                'type' => 'artisan',
                'description' => 'Instagram comments setup',
                'command' => 'inetstudio:instagram:comments:setup',
            ],
            [
                'type' => 'artisan',
                'description' => 'Instagram posts setup',
                'command' => 'inetstudio:instagram:posts:setup',
            ],
            [
                'type' => 'artisan',
                'description' => 'Instagram users setup',
                'command' => 'inetstudio:instagram:users:setup',
            ],
        ];
    }
}
