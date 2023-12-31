<x-layout title="Volumes" pageName="volumes">
    <div class="volumes-page">
        <form method="get">
        <div class="row col-lg-4 ms-auto me-auto mt-3 mb-2">
            <div class="col-4 mt-2">
                <select name="days" class="form-select" onchange="this.form.submit()">
                    <option value="1D" {{ $days == '1D' ? 'selected' : '' }}>1 Day</option>
                    <option value="3D" {{ $days == '3D' ? 'selected' : '' }}>3 Days</option>
                    <option value="7D" {{ $days == '7D' ? 'selected' : '' }}>7 Days</option>
                    <option value="30D" {{ $days == '30D' ? 'selected' : '' }}>30 Days</option>
                </select>
            </div>
            <div class="col-4 mt-2">
                <select name="currency" class="form-select" onchange="this.form.submit()">
                    <option value="">Currency</option>
                    <option value="theta" {{ $currency == 'theta' ? 'selected' : '' }}>Theta</option>
                    <option value="tfuel" {{ $currency == 'tfuel' ? 'selected' : '' }}>Tfuel</option>
                    <option value="tdrop" {{ $currency == 'tdrop' ? 'selected' : '' }}>Tdrop</option>
                </select>
            </div>
            <div class="col-4 mt-2">
                <select name="sort" class="form-select" onchange="this.form.submit()">
                    <option value="transactions" {{ $sort == 'transactions' ? 'selected' : '' }}>By Transactions</option>
                    <option value="volume_in" {{ $sort == 'volume_in' ? 'selected' : '' }}>By Volume In</option>
                    <option value="volume_out" {{ $sort == 'volume_out' ? 'selected' : '' }}>By Volume Out</option>
                    <option value="remaining" {{ $sort == 'remaining' ? 'selected' : '' }}>By Remaining</option>
                </select>
            </div>
        </div>
        </form>

        <div class="container transactions d-none d-lg-block ps-0 pe-0 mt-4">
            <table class="table table-striped table-sm w-100">
                <thead>
                <tr>
                    <th scope="col" class="truncate-cell">Account</th>
                    <th scope="col" class="text-center fit-cell">Trans</th>
                    <th scope="col" class="text-end fit-cell">Volume In</th>
                    <th scope="col" class="text-end fit-cell">Volume Out</th>
                    <th scope="col" class="text-end fit-cell">Remaining</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($volumes as $volume)
                    <tr>
                        <td class="truncate-cell"><a href="/account/{{ $volume->account }}">{{ isset($accounts[$volume->account]) ? $accounts[$volume->account]['name'] : $volume->account }}</a></td>
                        <td class="text-center fit-cell">{{ number_format($volume->times) }}</td>
                        <td class="fit-cell text-end">
                            <a href="javascript:void(0)" data-bs-toggle="tooltip" title="{{ Helper::formatNumber($volume->in_theta_coins, 2, 'auto') }} theta, {{ Helper::formatNumber($volume->in_tfuel_coins, 2, 'auto') }} tfuel, {{  Helper::formatNumber($volume->in_tdrop_coins, 2, 'auto') }} tdrop">{{ Helper::formatPrice($volume->usd_in, 2, 'auto') }}</a>
                        </td>
                        <td class="fit-cell text-end">
                            <a href="javascript:void(0)" data-bs-toggle="tooltip" title="{{ Helper::formatNumber($volume->out_theta_coins, 2, 'auto') }} theta, {{ Helper::formatNumber($volume->out_tfuel_coins, 2, 'auto') }} tfuel, {{  Helper::formatNumber($volume->out_tdrop_coins, 2, 'auto') }} tdrop">{{ Helper::formatPrice($volume->usd_out, 2, 'auto') }}</a>
                        </td>
                        <td class="fit-cell text-end">{{ $volume->remaining > 0 ? Helper::formatPrice($volume->remaining, 0, 'auto') : '-' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $volumes->links() }} (Transactions in the last 30 days)
        </div>

        <div class="container transactions mobile d-block d-lg-none ps-0 pe-0 mt-4">
            <table class="table table-striped table-sm w-100">
                <thead>
                <tr>
                    <th scope="col" class="truncate-cell">Account</th>
                    <th scope="col" class="text-center fit-cell">Trans</th>
                    <th scope="col" class="text-end fit-cell">Volume In / Out</th>
                    <th scope="col" class="text-end fit-cell">Remaining</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($volumes as $volume)
                    <tr>
                        <td class="truncate-cell align-middle"><a href="/account/{{ $volume->account }}">{{ isset($accounts[$volume->account]) ? $accounts[$volume->account]['name'] : $volume->account }}</a></td>
                        <td class="text-center fit-cell align-middle">{{ number_format($volume->times) }}</td>
                        <td class="fit-cell text-end align-middle">
                            <a href="javascript:void(0)" data-bs-toggle="tooltip" title="{{ Helper::formatNumber($volume->in_theta_coins, 2, 'auto') }} theta, {{ Helper::formatNumber($volume->in_tfuel_coins, 2, 'auto') }} tfuel, {{  Helper::formatNumber($volume->in_tdrop_coins, 2, 'auto') }} tdrop">{{ Helper::formatPrice($volume->usd_in, 2, 'auto') }}</a>
                            <br/>
                            <a href="javascript:void(0)" data-bs-toggle="tooltip" title="{{ Helper::formatNumber($volume->out_theta_coins, 2, 'auto') }} theta, {{ Helper::formatNumber($volume->out_tfuel_coins, 2, 'auto') }} tfuel, {{  Helper::formatNumber($volume->out_tdrop_coins, 2, 'auto') }} tdrop">{{ Helper::formatPrice($volume->usd_out, 2, 'auto') }}</a>
                        </td>
                        <td class="fit-cell text-end">{{ $volume->remaining > 0 ? Helper::formatPrice($volume->remaining, 0, 'auto') : '-' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $volumes->links() }} (Transactions in the last 30 days)
        </div>

    </div>
</x-layout>
