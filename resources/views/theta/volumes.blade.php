<x-layout title="Volumes" pageName="volumes">
    <div class="volumes-page">
        <form method="get">
        <div class="row col-lg-4 ms-auto me-auto mt-3 mb-2">
            <div class="col-6 mt-2">
                <select name="days" class="form-select" onchange="this.form.submit()">
                    <option value="1D" {{ $days == '1D' ? 'selected' : '' }}>1 Day</option>
                    <option value="7D" {{ $days == '7D' ? 'selected' : '' }}>7 Days</option>
                    <option value="30D" {{ $days == '30D' ? 'selected' : '' }}>30 Days</option>
                </select>
            </div>
            <div class="col-6 mt-2">
                <select name="sort" class="form-select" onchange="this.form.submit()">
                    <option value="transactions" {{ $sort == 'transactions' ? 'selected' : '' }}>By Transactions</option>
                    <option value="usd_in" {{ $sort == 'usd_in' ? 'selected' : '' }}>By USD In</option>
                    <option value="usd_out" {{ $sort == 'usd_out' ? 'selected' : '' }}>By USD Out</option>
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
                    <th scope="col" class="text-end fit-cell">USD In</th>
                    <th scope="col" class="text-end fit-cell">USD Out</th>
                    <th scope="col" class="text-end fit-cell">Remaining</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($volumes as $volume)
                    <tr>
                        <td class="truncate-cell"><a href="/account/{{ $volume->account }}">{{ isset($accounts[$volume->account]) ? $accounts[$volume->account]['name'] : $volume->account }}</a></td>
                        <td class="text-center fit-cell">{{ number_format($volume->times) }}</td>
                        <td class="fit-cell text-end">{{ number_format($volume->usd_in, 0) }}</td>
                        <td class="fit-cell text-end">{{ number_format($volume->usd_out, 0) }}</td>
                        <td class="fit-cell text-end">{{ number_format($volume->remaining, 0) }}</td>
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
                    <th scope="col" class="text-end fit-cell">USD In / Out</th>
                    <th scope="col" class="text-end fit-cell">Remaining</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($volumes as $volume)
                    <tr>
                        <td class="truncate-cell align-middle"><a href="/account/{{ $volume->account }}">{{ isset($accounts[$volume->account]) ? $accounts[$volume->account]['name'] : $volume->account }}</a></td>
                        <td class="text-center fit-cell align-middle">{{ number_format($volume->times) }}</td>
                        <td class="fit-cell text-end align-middle">
                            {{ number_format($volume->usd_in, 0) }} <br/>
                            {{ number_format($volume->usd_out, 0) }}
                        </td>
                        <td class="fit-cell text-end align-middle">{{ number_format($volume->remaining, 0) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $volumes->links() }} (Transactions in the last 30 days)
        </div>

    </div>
</x-layout>
