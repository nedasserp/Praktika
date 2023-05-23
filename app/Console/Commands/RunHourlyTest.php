<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RunHourlyTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:hourly';

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
        if(config('app.testcount')==null||!ctype_digit(config('app.testcount')))
        {
        $counter = 1;
        }
        else
        {
        $counter =config('app.testcount');
        }
        
        for($i=0;$i<$counter;$i++)
        {
        Artisan::call('test');
        $output = Artisan::output();
        dump($output);
        }
    }
}
