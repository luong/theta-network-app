<x-layout title="Accounts" pageName="accounts">
    <div class="whales-page">
        <div class="filter col-lg-3 ms-auto me-auto ps-2 pe-2">
            <select class="tags-select form-select mt-3" onchange="location.href = this.value">
                <option value="">Select Tags</option>
                <option value="/accounts?tags=whales" {{ $tags == 'whales' ? 'selected' : '' }}>Whales</option>
                <option value="/accounts?tags=thetalabs" {{ $tags == 'thetalabs' ? 'selected' : '' }}>ThetaLabs</option>
                <option value="/accounts?tags=exchange" {{ $tags == 'exchange' ? 'selected' : '' }}>Exchanges</option>
                <option value="/accounts?tags=validator" {{ $tags == 'validator' ? 'selected' : '' }}>Validators</option>
            </select>
        </div>
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

