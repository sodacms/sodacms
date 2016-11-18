<?php

namespace Soda\Cms\Console;

use Illuminate\Console\Command;

class Seed extends Command
{

    protected $signature = 'soda:seed {--class=SeedAll : The class name of the root seeder}';
    protected $description = 'Seed the Soda Database';

    /**
     * Runs seeds for Soda CMS, defaulting to 'SodaSeeder'
     */
    public function handle()
    {
        $this->call('db:seed', [
            '--class' => 'Soda\\Cms\\Seeds\\'.$this->option('class'),
        ]);
    }
}