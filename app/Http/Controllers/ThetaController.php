<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;

class ThetaController extends Controller
{

    public function home()
    {
        return view('theta.home', [
            'coins' => Cache::get('coins')
        ]);
    }
}
