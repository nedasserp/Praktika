<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UserInputCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:input';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $planas = $this->ask('Plan slug');
        $imagas = $this->ask('Image slug');

        // You can perform any necessary processing or validation here

        // Return the user input
        $this->line('Planas: ' . $planas);
        $this->line('Image: ' . $imagas);
        $this->line('');

        return [$planas, $imagas];
    }
}
