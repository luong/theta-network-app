<x-admin_layout pageName="accounts">
    <div class="account-list-page">
        <x-slot name="header">Accounts</x-slot>
        <x-slot name="menus">
            <a class="btn btn-outline-primary" href="{{ route('admin.account.add') }}">Add New Account</a>
        </x-slot>

        <div class="col-lg-6">
            <form method="get">
                <div class="input-group mb-3">
                    <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Search by code or name">
                    <button class="btn btn-outline-secondary" type="submit">Search</button>
                </div>
            </form>
        </div>

        <div class="mb-2">Found: ({{ number_format($accounts->total(), 0) }})</div>

        <div class="container-sm ms-0 ps-0 me-0 pe-0 d-none d-lg-block">
            <table class="table table-striped table-sm align-middle">
                <thead>
                <tr>
                    <th scope="col">Code</th>
                    <th scope="col">Name</th>
                    <th scope="col" class="text-center">Created At</th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                @foreach ($accounts as $account)
                    <tr>
                        <td><a href="/account/{{ $account->code }}">{{ $account->code }}</a></td>
                        <td>{{ $account->name }}</td>
                        <td class="text-center">{{ $account->created_at->format('Y-m-d') }}</td>
                        <td class="text-end">
                            <a class="btn btn-outline-primary" href="{{ route('admin.account.edit', ['id' => $account->id]) }}">Edit</a>
                            <a class="btn btn-outline-primary" href="javascript:void(0)" onclick="confirmDelete('{{ route('admin.account.delete', ['id' => $account->id]) }}')">Delete</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $accounts->links() }}
        </div>

        <div class="container-sm ms-0 ps-0 me-0 pe-0 d-block d-lg-none">
            <table class="table table-striped table-sm align-middle">
                <thead>
                <tr>
                    <th scope="col">Code</th>
                    <th scope="col">Name</th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                @foreach ($accounts as $account)
                    <tr>
                        <td><a href="/account/{{ $account->code }}">{{ Str::limit($account->code, 10) }}</a></td>
                        <td>{{ Str::limit($account->name, 10) }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.account.edit', ['id' => $account->id]) }}">Edit</a>
                            <a href="javascript:void(0)" onclick="confirmDelete('{{ route('admin.account.delete', ['id' => $account->id]) }}')">Delete</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $accounts->links() }}
        </div>

    </div>

    <script>
        function confirmDelete(url) {
            if (confirm('Do you want to delete this account?')) {
                location.href = url;
            }
        }
    </script>
</x-admin_layout>
