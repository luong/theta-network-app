<x-layout title="Accounts" pageName="accounts">
    <div class="whales-page">
        @if (count($trackingAccounts) > 0)
            <div class="whales d-flex flex-row justify-content-center flex-wrap gap-4 gap-md-3 mt-3">
                @foreach ($trackingAccounts as $trackingAccount)
                    <div class="whale bg-light p-1 text-center">
                        <div class="fw-semibold"><a href="{{ Helper::makeSiteAccountURL($trackingAccount->code) }}" class="text-decoration-none text-secondary">{{ Str::limit(isset($accounts[$trackingAccount->code]) ? $accounts[$trackingAccount->code]['name'] : $trackingAccount->code, 12) }}</a></div>
                        <div><x-currency type="theta" top="2"/> {{ Helper::formatNumber($trackingAccount->balance_theta, 0) }}</div>
                        <div><x-currency type="tfuel" top="2"/> {{ Helper::formatNumber($trackingAccount->balance_tfuel, 0) }}</div>
                        <div><x-currency type="tdrop" top="2"/> {{ Helper::formatNumber($trackingAccount->balance_tdrop, 0) }}</div>
                        <div><x-currency type="usd" top="2"/> {{ Helper::formatNumber($trackingAccount->balance_usd, 0) }}</div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="not-found">No accounts found. Please set a valid tag. </div>
        @endif
    </div>
</x-layout>
