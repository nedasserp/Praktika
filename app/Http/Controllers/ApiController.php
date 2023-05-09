<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApiController extends Controller
{
   public function create()
   {
    $api = config('app.apikey');

   $teams = Http::withHeaders([
      'Authorization' =>  'Bearer ' . $api,
      'Content-Type' => 'application/json',
      'Accept' => 'application/json',
   ])->get('https://api.cherryservers.com/v1/teams',[
      //'fields' => 'team',

   ]);
    $team = $teams -> json();
    $teamid = $team[0]['id'];

    $projects = Http::withHeaders([
      'Authorization' =>  'Bearer ' . $api,
      'Content-Type' => 'application/json',
      'Accept' => 'application/json',
   ])->get('https://api.cherryservers.com/v1/teams/'.$teamid.'/projects',[
      //'fields' => 'team',

   ]);
   $project = $projects -> json();
   $projectid = $project[0]['id'];

   $plans = Http::withHeaders([
      'Authorization' =>  'Bearer ' . $api,
      'Content-Type' => 'application/json',
      'Accept' => 'application/json',
 ])->get('https://api.cherryservers.com/v1/plans',[
   //'fields' => 'pricing'
 ]);

 $plan = $plans -> json();
 $test = $plan[0];
 $planslug = $plan[0]['slug'];
 $regionslug=$plan[0]['available_regions'][0]['slug'];

 $images = Http::withHeaders([
   'Authorization' =>  'Bearer ' . $api,
   'Content-Type' => 'application/json',
   'Accept' => 'application/json',
])->get('https://api.cherryservers.com/v1/plans/'.$planslug.'/images',[
//'fields' => 'pricing'
]);
$image = $images -> json();
$imageslug = $image[0]['slug'];




//$server = Http::withHeaders([
  // 'Authorization' =>  'Bearer ' . $api,
  // 'Content-Type' => 'application/json',
   //'Accept' => 'application/json',
//])->post('https://api.cherryservers.com/v1/projects/'.$projectid.'/servers',[
//'plan' => $planslug,
//'image' => $imageslug,
//'region' => $regionslug,
//]);





    return view('index')
    ->with('teams', json_decode($teams, true))
    ->with('projects', json_decode($projects, true))
    ->with('plans', $plans[0])
    ->with('images', json_decode($images, true))
    ->with('region',$regionslug);
    //->with('server',json_decode($server, true));
   }



   public function plans()
   {
      $api = config('app.apikey');
       
    
    
    return view('index')->with();
   }
}
