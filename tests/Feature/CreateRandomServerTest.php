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
  public function test_amount()
  {
    $input = [
      '300', //timeout
      '', //region
      '', //plan
      'ubuntu_18_04', //image
      '2', //testcount
    ];
    if($input[0]==null||!ctype_digit($input[0]))
       {
        $timeout = 900;
       }
       else
       {
        $timeout = $input[0];
       }
    if($input[1]!=null)
       {
        $inputregion = $input[1];
       }
       else
       {
        $inputregion = null;
       }
       if($input[2]!=null)
       {
        $inputplan = $input[2];
       }
       else
       {
        $inputplan = null;
       }
       if($input[3]!=null)
       {
        $inputimage = $input[3];
       }
       else
       {
        $inputimage = null;
       }
    if ($input[4]==null||!ctype_digit($input[4]))
    {
      $count = 1;
    }
    else
    {
      $count = $input[4];
    }
    for ($i=0;$i<$count;$i++)
    {
      $this->randomserver($input, $timeout,$inputregion,$inputplan,$inputimage);
    }
  }
    private function randomserver($input, $timeout,$inputregion,$inputplan,$inputimage)
    {
      
      try{
       $regionslug = $inputregion;
       $planslug = $inputplan;
       $imageslug = $inputimage;
      
       if($regionslug==null)
       {
        $regions = Http::withHeaders([
          'Authorization' =>  'Bearer ' . config('app.apikey'),
          'Content-Type' => 'application/json',
          'Accept' => 'application/json',
        ])->get('https://api.cherryservers.com/v1/regions',[
        ]);
        $regionarr = $regions -> json();
        $randomregion = array_rand($regionarr);
        $regionslug = $regionarr[$randomregion]["slug"];
       }
       if($planslug==null)
       {
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
       }

       if($imageslug==null)
       {
        $images = Http::withHeaders([
          'Authorization' =>  'Bearer ' . config('app.apikey'),
          'Content-Type' => 'application/json',
          'Accept' => 'application/json',
      ])->get('https://api.cherryservers.com/v1/plans/'.$planslug.'/images',[
      ]);
          $imagearr = $images ->json();
          $randomimage = array_rand($imagearr);
          $imageslug = $imagearr[$randomimage]["slug"];
       }
        $body = [
            'plan' => $planslug,
            'image' => $imageslug,
            'region' => $regionslug,
            'timeout' => $timeout,
          ];
          $start = microtime(true);
          $respo = null;
            $response = Http::withHeaders([
                'Authorization' =>  'Bearer ' . config('app.apikey'),
                 'Content-Type' => 'application/json',
                 'Accept' => 'application/json',
              ])->post('https://api.cherryservers.com/v1/projects/'.config('app.projectid').'/servers',$body);
              $ats = $response -> json();
              if ($response->getStatusCode() !== 201) {
                
                $elapsedTime = round(microtime(true) - $start,2);
                $message = "Serveris nera sukurtas del netinkamu parametru";
              $this->fail($message);
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
                $elapsedTime = round(microtime(true) - $start,2);
                $respo = $resp ->json();
                $status = $respo["state"];
                dump($status);
                if($elapsedTime>$timeout)
                {
                
                $delete = Http::withHeaders([
                  'Authorization' =>  'Bearer ' . config('app.apikey'),
                   'Content-Type' => 'application/json',
                   'Accept' => 'application/json',
                ])->delete('https://api.cherryservers.com/v1/servers/'.$serverid);
                $message = "Serveris netapo aktyvus per atitinkama timeout laika";
                $this->fail($message);
                }
                
              }
              $elapsedTime = round(microtime(true) - $start,2);
              dump ($elapsedTime);
              $message = "Serveris buvo sekmingai sukurtas";
              $testResult = new TestResult([
                'Testas' => 'CreateRandomServerTest',
                'Rezultatas' => 'passed',
                'Laikas' => $elapsedTime,
                'Testo_parametrai' => $body,
                'Sukurtas_serveris' => $respo,
                'Zinute' => $message,
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
              catch(\Throwable $e){
                $body = [
                  'plan' => $planslug,
                  'image' => $imageslug,
                  'region' => $regionslug,
                  'timeout' => $timeout,
                ];
                $testResult = new TestResult([
                  'Testas' => 'CreateRandomServerTest',
                  'Rezultatas' => 'failed',
                  'Laikas' => round(microtime(true) - $start,2), 
                  'Testo_parametrai' => $body,
                  'Sukurtas_serveris' => $respo,
                  'Zinute' => $message,
              ]);
              $testResult->save();
      
              $this->fail($message);
              }
    }
}
