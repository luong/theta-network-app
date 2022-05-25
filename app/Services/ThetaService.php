<?php

namespace App\Services;

use App\Models\DailyChain;
use App\Models\Holder;
use App\Models\NodeValidator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class ThetaService
{

    public function caching()
    {
        $this->cacheHolders();
        $this->cacheValidators();
        $this->cacheCoinList();
    }

    public function cacheHolders()
    {
        $holders = Holder::all()->keyBy('code')->toArray();
        Cache::put('holders', $holders);
        return $holders;
    }

    public function getHolders()
    {
        $holders = Cache::get('holders');
        if (empty($holders)) {
            $holders = $this->cacheHolders();
        }
        return $holders;
    }

    public function cacheValidators()
    {
        $validators = NodeValidator::all()->keyBy('holder')->toArray();
        Cache::put('validators', $validators);
        return $validators;
    }

    public function getValidators()
    {
        $validators = Cache::get('validators');
        if (empty($validators)) {
            $validators = $this->cacheValidators();
        }
        return $validators;
    }

    public function cacheCoinList()
    {
        $onChainServer = resolve(OnChainService::class);
        $coins = $onChainServer->getCoinList();
        Cache::put('coins', $coins);
        return $coins;
    }

    public function getCoinList()
    {
        $coins = Cache::get('coins');
        if (empty($coins)) {
            $coins = $this->cacheCoinList();
        }
        return $coins;
    }

    public function updateDailyValidators(int $validators)
    {
        $chain = DailyChain::where('date', Carbon::today())->where('chain', 'theta')->first();
        if (!empty($chain) && empty($chain->validators)) {
            $chain->validators = $validators;
            $chain->save();
        }
    }

}
