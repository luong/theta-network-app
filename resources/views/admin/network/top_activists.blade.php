<x-admin_layout pageName="validators">
    <div class="edit-validator-page">
        <x-slot name="header">Top Activists in 24H</x-slot>

        <div class="container-sm ms-0 ps-0 me-0 pe-0 d-none d-lg-block">
            <table class="table table-striped table-sm align-middle">
                <thead>
                <tr>
                    <th scope="col">Account</th>
                    <th scope="col" class="text-center">Transaction Count</th>
                    <th scope="col" class="text-end">In/Out Total</th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                @foreach ($activists as $activist)
                    @php
                        $holder = isset($holders[$activist->account]) ? $holders[$activist->account]['name'] : $activist->account;
                    @endphp
                    <tr>
                        <td><a href="{{ Helper::makeThetaAccountURL($activist->account) }}" target="_blank">{{ $holder }}</a></td>
                        <td class="text-center">{{ $activist->times }}</td>
                        <td class="text-end">${{ number_format($activist->usd, 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="container-sm ms-0 ps-0 me-0 pe-0 d-block d-lg-none">
            <table class="table table-striped table-sm align-middle">
                <thead>
                <tr>
                    <th scope="col">Account</th>
                    <th scope="col" class="text-center">Transaction Count</th>
                    <th scope="col" class="text-end">In/Out Total</th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                @foreach ($activists as $activist)
                    @php
                        $holder = isset($holders[$activist->account]) ? $holders[$activist->account]['name'] : $activist->account;
                    @endphp
                    <tr>
                        <td><a href="{{ Helper::makeThetaAccountURL($activist->account) }}" target="_blank">{{ Str::limit($holder, 10) }}</a></td>
                        <td class="text-center">{{ $activist->times }}</td>
                        <td class="text-end">${{ number_format($activist->usd, 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

    </div>

</x-admin_layout>
