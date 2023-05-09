<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\TestResult;
use Illuminate\Support\Facades\Http;

class CreateRandomServerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $regions = Http::withHeaders([
            'Authorization' =>  'Bearer ' . config('app.apikey'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
       ])->get('https://api.cherryservers.com/v1/regions',[
         //'fields' => 'pricing'
       ]);

       $regionarr = $regions -> json();
       $randomregion = array_rand($regionarr);
       $regionslug = $regionarr[$randomregion]["slug"];
       $regionid = $regionarr[$randomregion]["id"];

       $plans = Http::withHeaders([
        'Authorization' =>  'Bearer ' . config('app.apikey'),
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
   ])->get('https://api.cherryservers.com/v1/plans',[
     'region' => $regionslug,
   ]);

   $planarr = $plans -> json();
   $randomplan = array_rand($planarr);
   $planslug = $planarr[$randomplan]["slug"];

   $images = Http::withHeaders([
    'Authorization' =>  'Bearer ' . config('app.apikey'),
    'Content-Type' => 'application/json',
    'Accept' => 'application/json',
])->get('https://api.cherryservers.com/v1/plans/'.$planslug.'/images',[
]);
    $imagearr = $images ->json();
    $randomimage = array_rand($imagearr);
    $imageslug = $imagearr[$randomimage]["slug"];

        $body = [
            'plan' => $planslug,
            'image' => $imageslug,
            'region' => $regionslug,
          ];
            $start = microtime(true);
            $response = Http::withHeaders([
                'Authorization' =>  'Bearer ' . config('app.apikey'),
                 'Content-Type' => 'application/json',
                 'Accept' => 'application/json',
              ])->post('https://api.cherryservers.com/v1/projects/149194/servers',$body);
              $ats = $response -> json();
              //$this->assertEquals(400, $response->getStatusCode());
    
              if ($response->getStatusCode() !== 201) {
                
                $elapsedTime = round(microtime(true) - $start,2);
                $testResult = new TestResult([
                  'Testas' => 'CreateRandomServerTest',
                  'Rezultatas' => 'failed',
                  'Laikas' => $elapsedTime,
                  'Testo_parametrai' => $body,
              ]);
              $testResult->save();
              $this->fail('Serveris nera sukurtas');
            }
              $serverid = $ats["id"];
              $status = $ats["state"];
    
              dump($status);
              while ($status!="active")
              {
                $resp = Http::withHeaders([
                    'Authorization' =>  'Bearer ' . config('app.apikey'),
                     'Content-Type' => 'application/json',
                     'Accept' => 'application/json',
                  ])->get('https://api.cherryservers.com/v1/servers/'.$serverid.'',[
                  ]);
                $respo = $resp ->json();
                $status = $respo["state"];
                dump($status);
              }
              $elapsedTime = round(microtime(true) - $start,2);
              dump ($elapsedTime);
              $testResult = new TestResult([
                'Testas' => 'CreateRandomServerTest',
                'Rezultatas' => 'passed',
                'Laikas' => $elapsedTime,
                'Testo_parametrai' => $body,
                'Sukurtas_serveris' => $respo,
            ]);
            $testResult->save();
    
              $this->assertEquals(200, $resp->getStatusCode());
    
              $delete = Http::withHeaders([
                'Authorization' =>  'Bearer ' . config('app.apikey'),
                 'Content-Type' => 'application/json',
                 'Accept' => 'application/json',
              ])->delete('https://api.cherryservers.com/v1/servers/'.$serverid);
              $this->assertEquals(204, $delete->getStatusCode());
        
    }
}
