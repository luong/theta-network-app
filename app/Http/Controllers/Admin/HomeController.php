<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ThetaService;
use Illuminate\Support\Facades\Artisan;

class HomeController extends Controller
{

    private $thetaService;

    public function __construct(ThetaService $thetaService) {
        $this->thetaService = $thetaService;
    }

    public function index()
    {
        return view('admin.home.index', [
            'command_trackers' => $this->thetaService->getCommandTrackers()
        ]);
    }

    public function run()
    {
        $command = request('command');
        Artisan::call($command);
        return response()->json(['result' => 'success']);
    }
}
