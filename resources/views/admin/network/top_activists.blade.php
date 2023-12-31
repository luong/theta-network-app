<x-admin_layout pageName="top_activists">
    <div class="top-activist-page">
        <x-slot name="header">Top Activists</x-slot>

        <div class="col-lg-6">
            <form method="get">
                <div class="input-group mb-3">
                    <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Search by account">
                    <button class="btn btn-outline-secondary" type="submit">Search</button>
                </div>
            </form>
        </div>

        <div class="mb-2">Found: ({{ number_format($activists->total(), 0) }})</div>

        <div class="container-sm ms-0 ps-0 me-0 pe-0 d-none d-lg-block">
            <table class="table table-striped table-sm align-middle">
                <thead>
                <tr>
                    <th scope="col">Account</th>
                    <th scope="col" class="text-center">Transaction Count</th>
                    <th scope="col" class="text-end">In</th>
                    <th scope="col" class="text-end">Out</th>
                    <th scope="col" class="text-end">In+Out</th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                @foreach ($activists as $activist)
                    @php
                        $account = isset($accounts[$activist->account]) ? $accounts[$activist->account]['name'] : $activist->account;
                    @endphp
                    <tr>
                        <td><a href="/account/{{ $activist->account }}">{{ $account }}</a></td>
                        <td class="text-center">{{ $activist->times }}</td>
                        <td class="text-end">${{ number_format($activist->usd_in, 2) }}</td>
                        <td class="text-end">${{ number_format($activist->usd_out, 2) }}</td>
                        <td class="text-end">${{ number_format($activist->usd, 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $activists->links() }}
        </div>

        <div class="container-sm ms-0 ps-0 me-0 pe-0 d-block d-lg-none">
            <table class="table table-striped table-sm align-middle">
                <thead>
                <tr>
                    <th scope="col">Account</th>
                    <th scope="col" class="text-center">Count</th>
                    <th scope="col" class="text-end">In</th>
                    <th scope="col" class="text-end">Out</th>
                    <th scope="col" class="text-end">In+Out</th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                @foreach ($activists as $activist)
                    @php
                        $account = isset($accounts[$activist->account]) ? $accounts[$activist->account]['name'] : $activist->account;
                    @endphp
                    <tr>
                        <td><a href="/account/{{ $activist->account }}">{{ Str::limit($account, 6) }}</a></td>
                        <td class="text-center">{{ $activist->times }}</td>
                        <td class="text-end">{{ Helper::formatPrice($activist->usd_in, 0, 'K') }}</td>
                        <td class="text-end">{{ Helper::formatPrice($activist->usd_out, 0, 'K') }}</td>
                        <td class="text-end">{{ Helper::formatPrice($activist->usd, 0, 'K') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $activists->links() }}
        </div>

    </div>

</x-admin_layout>
