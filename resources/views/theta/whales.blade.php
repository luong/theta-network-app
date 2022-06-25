<x-layout title="ThetaNetworkApp" pageName="whales">
    <div class="whales-page">
        <div class="whales d-flex flex-row justify-content-center flex-wrap gap-4 gap-md-3 mt-3">
            @foreach ($trackingAccounts as $trackingAccount)
                <div class="whale bg-light p-1 text-center">
                    <div class="fw-semibold"><a href="{{ Helper::makeSiteAccountURL($trackingAccount['code']) }}" class="text-decoration-none text-secondary">{{ Str::limit(isset($accounts[$trackingAccount['code']]) ? $accounts[$trackingAccount['code']]['name'] : $trackingAccount['code'], 12) }}</a></div>
                    <div><x-currency type="theta" top="2"/> {{ Helper::formatNumber($trackingAccount['balance_theta'], 0) }}</div>
                    <div><x-currency type="tfuel" top="2"/> {{ Helper::formatNumber($trackingAccount['balance_tfuel'], 0) }}</div>
                    <div><x-currency type="usd" top="2"/> {{ Helper::formatNumber($trackingAccount['balance_usd'], 0) }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="modal add-whale-model" id="addWhaleModel">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Add Whale Wallet (min. {{ Helper::formatPrice(Constants::WHALE_MIN_BALANCE) }} in balance)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-bod text-break ps-3 pb-3 pe-3">
                    <form method="post" action="{{ route('whales.add') }}">
                        <div>
                            @csrf
                            <div class="mb-3 mt-3">
                                <label for="whaleWalletAddress">Wallet address:</label>
                                <input type="text" class="form-control" id="whaleWalletAddress" name="address"/>
                            </div>
                            <div class="mb-3">
                                <label for="whaleName">Name (Optional):</label>
                                <input type="text" class="form-control" id="whaleName" name="name">
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layout>

