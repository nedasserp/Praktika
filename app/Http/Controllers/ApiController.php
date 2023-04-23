<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApiController extends Controller
{
   public function regions()
   {
    $api = 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXUyJ9.eyJjIjoxMDk1MjksImkiOiIiLCJyIjoicmNmIiwidCI6InVjIiwiYSI6MCwiaWF0IjoxNjgxNjY1OTI2fQ.bquvZumX8X_ySBonPkZ_wFEiDUvCMjRrqwQQPJXTPlB1cODHuHH8f-YT0Uw7QJ-BK31EBtMRk01ixtL450NbMKZx7m-hbKxWbzj6kimENYg5_wau58332_eKwPXWkT7EpvqddTOzGWnEqYGrYEE1ZaY9WXystLqS6_-aGDn_wyINNaO25wdBVbid8rDM8YQfmpWM6ReJHYhOlEI5sUUoWfO-2YA_sosIQs_XvjkJ8aw84V2wXl_qpADfB6Cqj-YXqlCdMH6Pv2nZxvX1ePDn6GGr3nuT6R84qLl_T1XyK-KeVJfMPZkfwwy_QNdUCzqdSTMQj8AE7ngiRsn4rN6dPA';
       
    $response = Http::withHeaders([
        'Authorization' =>  'Bearer ' . $api,
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
   ])->get('https://api.cherryservers.com/v1/regions');
    
    return view('index')->with('response', json_decode($response, true));
   }
}
