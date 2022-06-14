<x-admin_layout pageName="stakes">
    <div class="stakes-page">
        <x-slot name="header">Stakes</x-slot>

        <form method="get">
            <div class="col-lg-6 row">
                <div class="col col-6 col-lg-3">
                    <select name="type" class="form-select" onchange="this.form.submit()">
                        <option value="">Type</option>
                        <option value="vcp" {{ $type == 'vcp' ? 'selected' : '' }}>Validator</option>
                        <option value="gcp" {{ $type == 'gcp' ? 'selected' : '' }}>Guardian</option>
                        <option value="eenp" {{ $type == 'eenp' ? 'selected' : '' }}>Elite</option>
                    </select>
                </div>
                <div class="col col-6 col-lg-3">
                    <select name="withdrawn" class="form-select" onchange="this.form.submit()">
                        <option value="">Withdrawn</option>
                        <option value="yes" {{ $withdrawn == 'yes' ? 'selected' : '' }}>Yes</option>
                        <option value="no" {{ $withdrawn == 'no' ? 'selected' : '' }}>No</option>
                    </select>
                </div>
                <div class="col col-12 col-lg-6 mt-3 mt-lg-0">
                    <div class="input-group mb-3">
                        <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Search by account">
                        <button class="btn btn-outline-secondary" type="submit">Search</button>
                    </div>
                </div>
            </div>
        </form>

        <div class="mb-2">Found: ({{ number_format($stakes->total(), 0) }})</div>

        <div class="stake-list container-sm ms-0 ps-0 me-0 pe-0 d-none d-lg-block">
            <table class="table table-striped table-sm align-middle">
                <thead>
                <tr>
                    <th scope="col">Type</th>
                    <th scope="col">Holder</th>
                    <th scope="col">Staker</th>
                    <th scope="col" class="text-end">Coins</th>
                    <th scope="col" class="text-center">Currency</th>
                    <th scope="col" class="text-end">USD</th>
                    <th scope="col" class="text-center">Withdrawn</th>
                    <th scope="col" class="text-center">Returned At</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($stakes as $stake)
                    @php
                        $holderAccount = isset($accounts[$stake->holder]) ? $accounts[$stake->holder]['name'] : Str::limit($stake->holder, 10);
                        $stakerAccount = isset($accounts[$stake->source]) ? $accounts[$stake->source]['name'] : Str::limit($stake->source, 20);
                    @endphp
                    <tr>
                        <td>{{ $stake->type }}</td>
                        <td><a href="/account/{{ $stake->holder }}">{{ $holderAccount }}</a></td>
                        <td><a href="/account/{{ $stake->source }}">{{ $stakerAccount }}</a></td>
                        <td class="text-end">{{ number_format($stake->coins, 0) }}</a></td>
                        <td class="text-center">{{ $stake->currency }}</td>
                        <td class="text-end">${{ number_format($stake->usd, 2) }}</td>
                        <td class="text-center">{{ $stake->withdrawn ? 'Yes' : 'No' }}</td>
                        <td class="text-center">{{ $stake->returned_at }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $stakes->links() }}
        </div>

        <div class="stake-list mobile container-sm ms-0 ps-0 me-0 pe-0 d-block d-lg-none">
            <table class="table table-striped table-sm align-middle">
                <thead>
                <tr>
                    <th scope="col"></th>
                    <th scope="col">Holder</th>
                    <th scope="col">Staker</th>
                    <th scope="col" class="text-end">Coins</th>
                    <th scope="col" class="text-end">USD</th>
                    <th scope="col" class="text-center">W</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($stakes as $stake)
                    @php
                        $holderAccount = isset($accounts[$stake->holder]) ? $accounts[$stake->holder]['name'] : $stake->holder;
                        $stakerAccount = isset($accounts[$stake->source]) ? $accounts[$stake->source]['name'] : $stake->source;
                    @endphp
                    <tr>
                        <td>{{ $stake->type }}</td>
                        <td><a href="/account/{{ $stake->holder }}">{{ Str::limit($holderAccount, 5, '..') }}</a></td>
                        <td><a href="/account/{{ $stake->source }}">{{ Str::limit($stakerAccount, 5, '..') }}</a></td>
                        <td class="text-end">{{ Helper::formatNumber($stake->coins, 0, 'K') }}<img class="currency-ico" src="/images/{{ $stake->currency }}_flat.png"/></a></td>
                        <td class="text-end">${{ Helper::formatNumber($stake->usd, 0, 'K') }}</td>
                        <td class="text-center">{{ $stake->withdrawn ? 'Yes' : 'No' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $stakes->links() }}
        </div>

    </div>

</x-admin_layout>
