<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use App\Services\OnChainService;
use App\Services\ThetaService;
use Illuminate\Validation\ValidationException;

class UsersController extends Controller
{

    private $thetaService;
    private $onChainService;

    public function __construct(ThetaService $thetaService, OnChainService $onChainService)
    {
        $this->thetaService = $thetaService;
        $this->onChainService = $onChainService;
    }

    public function subscribe()
    {
        return view('users.subscribe');
    }

    public function trackWallet()
    {
        request()->validate([
            'email' => ['bail', 'required', 'string', 'email']
        ]);

        $addresses = [];

        $address1 = request('address1');
        if (!empty($address1)) {
            $account1Obj = $this->onChainService->getAccount($address1, false);
            if (empty($account1Obj)) {
                throw ValidationException::withMessages([
                    'address1' => 'The address1 field is not a valid theta wallet.'
                ]);
            }
            $addresses[] = $address1;
        }

        $address2 = request('address2');
        if (!empty($address2)) {
            $account2Obj = $this->onChainService->getAccount($address2, false);
            if (empty($account2Obj)) {
                throw ValidationException::withMessages([
                    'address2' => 'The address2 field is not a valid theta wallet.'
                ]);
            }
            $addresses[] = $address2;
        }

        if (empty($addresses)) {
            throw ValidationException::withMessages([
                'address1' => 'Please input wallet addresses.'
            ]);
        }

        $user = User::with('wallets')->firstOrCreate([
            'email' => request('email')
        ]);
        $user->wallets()->delete();
        foreach ($addresses as $address) {
            $user->wallets()->save(new Wallet(['address' => $address]));
        }
        $this->thetaService->cacheWallets();
        return back()->with('message', 'Wallets tracked: ' . implode(', ', $addresses));
    }

    public function untrackWallet()
    {
        request()->validate([
            'email' => ['bail', 'required', 'string', 'email', 'exists:users'],
        ]);
        $user = User::with('wallets')->where('email', request('email'))->first();
        $user->delete();
        $this->thetaService->cacheWallets();
        return back()->with('message', 'Your data was deleted from our database.');
    }

}
