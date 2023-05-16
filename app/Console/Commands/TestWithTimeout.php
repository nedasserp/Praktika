<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\Application;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

class TestWithTimeout extends Command
{
   /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:timeout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run tests with a specified timeout.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $counter =1;
        dump($counter);
        for($i=0;$i<$counter;$i++)
        {
        $exitcode =Artisan::call('test');
        $output = Artisan::output();
        dump($output);
        }
    }

}
