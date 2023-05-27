<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use App\Models\TestResult;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Console\Input\InputOption;

class CreateRandomServerTest extends TestCase
{
    public function test_randomserver()
    {
      $input = [
        config('app.timeout'), //Laiko tarpo, per kurį serveris turi tapti aktyvus, įvestis
        config('app.region'), //regiono įvestis
        config('app.plan'), //plano įvestis
        config('app.image'), //atvaizdo įvestis
      ];
      $timeout = $input[0] ?? 900; // Tikrinama, ar buvo įvestas laiko tarpas, jei ne, priskiriamas numatytasis laiko tarpas
      $inputregion = $input[1] ?? null; // Tikrinama, ar buvo įvestas regionas
      $inputplan = $input[2] ?? null; // Tikrinama, ar buvo įvestas planas
      $inputimage = $input[3] ?? null; // Tikrinama, ar buvo įvestas atvaizdas

      try{
      $respo = null; // Serverio informacija
      $start = 0; // Pradžios laikas
       $regionslug = $inputregion; // Priskiriamas regiono slug
       $planslug = $inputplan; // Priskiriamas plano slug
       $imageslug = $inputimage; // Priskiriamas atvaizdo slug

       
       if($regionslug==null) // Jei joks regionas nebuvo įvestas
       {
        $regions = Http::withHeaders([ // Siunčiama užklausa regionams gauti
          'Authorization' =>  'Bearer ' . config('app.apikey'),
          'Content-Type' => 'application/json',
          'Accept' => 'application/json',
        ])->get('https://api.cherryservers.com/v1/regions',[
        ]);
        if ($regions->getStatusCode() !== 200) { // Jei gaunamas klaidos kodas
          $message = $regions; // Žinutei priskiriama gauta klaida
        $this->fail($message); // Testo sustabdymas su žinute, kodėl jis buvo sustabdomas
      }
        $regionarr = $regions -> json(); // Iš atsakymo gaunamas regionų masyvas
        $randomregion = array_rand($regionarr); // Atsitiktiniu būdu išrenkamas regionas
        $regionslug = $regionarr[$randomregion]["slug"]; // Priskiriamas išrinkto regiono slug
       }
       if($planslug==null) // Jei joks planas nebuvo įvestas
       {
        $plans = Http::withHeaders([ // Siunčiama užklausa planams gauti
          'Authorization' =>  'Bearer ' . config('app.apikey'),
          'Content-Type' => 'application/json',
          'Accept' => 'application/json',
        ])->get('https://api.cherryservers.com/v1/plans',[
       'region' => $regionslug,
        ]);
        if ($plans->getStatusCode() !== 200) { // Jei gaunamas klaidos kodas
          $message = $plans; // Žinutei priskiriama gauta klaida
        $this->fail($message); // Testo sustabdymas su žinute, kodėl jis buvo sustabdomas
      }
        $planarr = $plans -> json(); // Iš atsakymo gaunamas planų masyvas
        $randomplan = array_rand($planarr); // Atsitiktiniu būdu išrenkamas planas
        $planslug = $planarr[$randomplan]["slug"]; // Priskiriamas išrinkto plano slug
       }

       if($imageslug==null) // Jei joks atvaizdas nebuvo įvestas
       {
        $images = Http::withHeaders([ // Siunčiama užklausa atvaizdams gauti
          'Authorization' =>  'Bearer ' . config('app.apikey'),
          'Content-Type' => 'application/json',
          'Accept' => 'application/json',
      ])->get('https://api.cherryservers.com/v1/plans/'.$planslug.'/images',[
      ]);
      if ($images->getStatusCode() !== 200) { // Jei gaunamas klaidos kodas
        $message = $images; // Žinutei priskiriama gauta klaida
      $this->fail($message); // Testo sustabdymas su žinute, kodėl jis buvo sustabdomas
    }
          $imagearr = $images ->json(); // Iš atsakymo gaunamas atvaizdų masyvas
          $randomimage = array_rand($imagearr); // Atsitiktiniu būdu išrenkamas atvaizdas
          $imageslug = $imagearr[$randomimage]["slug"]; // Priskiriamas išrinkto atvaizdo slug
       }
        $body = [
            'plan' => $planslug, // Plano slug, su kuriuo yra bandoma sukurti serverį
            'image' => $imageslug, // atvaizdo slug, su kuriuo yra bandoma sukurti serverį
            'region' => $regionslug, // regiono slug, su kuriuo yra bandoma sukurti serverį
            'timeout' => $timeout, // Laiko tarpas, su kuriuo yra bandoma sukurti serverį
          ];
          $start = microtime(true); // Laiko skaičiavimo pradžia
         
            $response = Http::withHeaders([
                'Authorization' =>  'Bearer ' . config('app.apikey'),
                 'Content-Type' => 'application/json',
                 'Accept' => 'application/json',
              ])->post('https://api.cherryservers.com/v1/projects/'.config('app.projectid').'/servers',$body);
              $ats = $response -> json(); // Iš atsakymo gaunama informacija apie serverį
              if ($response->getStatusCode() !== 201) { // Jei gaunamas klaidos kodas
                
                $elapsedTime = round(microtime(true) - $start,2); // Praėjusio laiko skaičiavimas
                $message = "Serveris nera sukurtas del netinkamu parametru";
              $this->fail($message); // Testo sustabdymas su žinute, kodėl jis buvo sustabdomas
            }
              $serverid = $ats["id"]; // Sukurto serverio ID
              $status = $ats["state"]; // Sukurto serverio būsena
    
              dump($status);
              while ($status!="active") // Kol serveris netapo aktyvus
              {
                $resp = Http::withHeaders([
                    'Authorization' =>  'Bearer ' . config('app.apikey'),
                     'Content-Type' => 'application/json',
                     'Accept' => 'application/json',
                  ])->get('https://api.cherryservers.com/v1/servers/'.$serverid.'',[
                  ]);
                  if ($resp->getStatusCode() !== 200) { // Jei gaunamas klaidos kodas
                    $message = $resp; // Žinutei priskiriama gauta klaida
                  $this->fail($message); // Testo sustabdymas su žinute, kodėl jis buvo sustabdomas
                }
                $elapsedTime = round(microtime(true) - $start,2); // Praėjusio laiko skaičiavimas
                $respo = $resp ->json(); // Gaunama informacija apie serverį
                $status = $respo["state"]; // Serverio būsena
                dump($status);
                if($elapsedTime>$timeout) // Jei praėjęs laikas viršyja nustatytą laiko tarpą
                {
                
                $delete = Http::withHeaders([ // Siunčiama užklausa serverio ištrynimui
                  'Authorization' =>  'Bearer ' . config('app.apikey'),
                   'Content-Type' => 'application/json',
                   'Accept' => 'application/json',
                ])->delete('https://api.cherryservers.com/v1/servers/'.$serverid);
                $message = "Serveris netapo aktyvus per atitinkama timeout laika";
                $this->fail($message); // Testo sustabdymas su žinute, kodėl jis buvo sustabdomas
                }
                
              }
              $elapsedTime = round(microtime(true) - $start,2); // Praėjusio laiko skaičiavimas
              dump ($elapsedTime);
              $message = "Serveris buvo sekmingai sukurtas";
              $testResult = new TestResult([ // Rezultatai įvedami į duomenų bazę
                'Testas' => 'CreateRandomServerTest',
                'Rezultatas' => 'passed',
                'Laikas' => $elapsedTime,
                'Testo_parametrai' => $body,
                'Sukurtas_serveris' => $respo,
                'Zinute' => $message,
            ]);
            $testResult->save();
    
              $this->assertEquals(200, $resp->getStatusCode()); // Patvirtinimas, jog nebuvo gautas klaidos kodas
    
              $delete = Http::withHeaders([ // Siunčiama užklausa serverio ištrynimui
                'Authorization' =>  'Bearer ' . config('app.apikey'),
                 'Content-Type' => 'application/json',
                 'Accept' => 'application/json',
              ])->delete('https://api.cherryservers.com/v1/servers/'.$serverid);
              $this->assertEquals(204, $delete->getStatusCode()); // Patvirtinimas, jog serveris buvo ištrintas
              }
              catch(\Throwable $e){
                $body = [ // Parametrai, su kuriais buvo bandoma sukurti serverį
                  'plan' => $planslug,
                  'image' => $imageslug,
                  'region' => $regionslug,
                  'timeout' => $timeout,
                ];
                $testResult = new TestResult([ // Rezultatai įvedami į duomenų bazę
                  'Testas' => 'CreateRandomServerTest',
                  'Rezultatas' => 'failed',
                  'Laikas' => round(microtime(true) - $start,2), 
                  'Testo_parametrai' => $body,
                  'Sukurtas_serveris' => $respo,
                  'Zinute' => $message,
              ]);
              $testResult->save();
      
              $this->fail($message); // Testo sustabdymas su žinute, kodėl jis buvo sustabdomas
              }
    }
}
