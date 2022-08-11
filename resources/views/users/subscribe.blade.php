<x-layout title="Subscribe" pageName="subscribe">
    <div class="track-account-page">
        <div class="container">
            @if ($errors->any())
                <div class="alert alert-warning mt-4">
                    <ul class="m-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @elseif (session('message'))
                <div class="alert alert-success mt-4">{{ session('message') }}</div>
            @endif


            <div class="container mt-lg-4">
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-4">
                        <form method="post">
                            @csrf
                            <div class="mb-3 mt-3">
                                <label for="email">Email:</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}"/>
                            </div>
                            <div class="mb-3 mt-3">
                                <label for="address1">Theta wallet address 1:</label>
                                <input type="text" class="form-control" id="address1" name="address1" value="{{ old('address1') }}"/>
                            </div>
                            <div class="mb-3 mt-3">
                                <label for="address2">Theta wallet address 2:</label>
                                <input type="text" class="form-control" id="address2" name="address2" value="{{ old('address2') }}"/>
                            </div>
                            <button type="submit" formaction="{{ route('trackWallet') }}" class="btn btn-primary w-25">Track</button>
                            <button type="submit" formaction="{{ route('untrackWallet') }}" class="btn btn-secondary w-25 ms-2">Untrack</button>
                        </form>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="notes">
                            <div class="mt-4 fw-bold">** Notes:</div>
                            <div class="text">
                                <ul>
                                    <li>This function is for tracking your wallet's activities, then email to you.</li>
                                    <li>We never ask you for passwords, private keys, mnemonic words.</li>
                                    <li>Your emails are only used to receiving notifications about your wallets.</li>
                                    <li>Tracking events: transfers, stakes, unstakes.</li>
                                    <li>Only transactions with amount above {{ Constants::USER_WALLET_TRACK_AMOUNT }} USD notified.</li>
                                    <li>For limited resources, we just send one email for you per day.</li>
                                    <li>Use Untrack button to delete your data from our database.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>

