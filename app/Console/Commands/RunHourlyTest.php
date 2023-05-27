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
        if(config('app.testcount')==null||!ctype_digit(config('app.testcount'))) // Tikrinama testų skaičiaus įvestis
        {
        $counter = 1; // Jei nebuvo įvesta
        }
        else
        {
        $counter =config('app.testcount'); // Jei buvo įvesta
        }
        
        for($i=0;$i<$counter;$i++)
        {
        Artisan::call('test'); // Kviečiama komanda
        $output = Artisan::output(); // Atsakymas
        dump($output);
        }
    }
}
