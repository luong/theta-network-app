<?php
namespace App\Http\Controllers;

use App\Services\ThetaService;

class ThetaController extends Controller
{

    private $thetaService;

    public function __construct(ThetaService $thetaService)
    {
        $this->thetaService = $thetaService;
    }

    public function home()
    {
        return view('theta.home', [
            'coins' => $this->thetaService->getCoinList(),
            'networkInfo' => $this->thetaService->getNetworkInfo()
        ]);
    }
}
