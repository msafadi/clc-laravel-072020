<?php

namespace App\Http\Controllers;

use App\Product;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index()
    {
        return view('welcome', [
            'products' => Product::take(5)->latest()->get(),
        ]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function home()
    {

        $curl = curl_init('https://api.openweathermap.org/data/2.5/weather?q=gaza,ps&appid=5b111e3737b3102908663fd62419fd0b');
        curl_setopt_array($curl, [
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
        ]);
        $res = (object) json_decode(curl_exec($curl));
        curl_close($curl);

        //$client = new Client();
        //$res = $client->request('GET', 'https://api.openweathermap.org/data/2.5/weather?q=gaza,ps&appid=5b111e3737b3102908663fd62419fd0b', ['allow_redirects' => false]);
        
        return view('home', [
            'weather' => $res,
        ]);
    }
}
