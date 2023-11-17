<?php

// app/Http/Controllers/MapController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class MapController extends Controller
{
    public function showMap()
    {
        return view('map');
    }
}

