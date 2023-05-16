<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use App\Models\TestResult;

class CreateServerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
      $body = [
        'plan' => 'cloud_vps_1',
        'image' => 'ubuntu_22_04',
        'region' => 'eu_nord_1',
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
              'Testas' => 'CreateServerTest',
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
            'Testas' => 'CreateServerTest',
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
