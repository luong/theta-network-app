<x-admin_layout pageName="validators">
    <div class="edit-validator-page">
        <x-slot name="header">Validators</x-slot>
        <x-slot name="menus">
            <a class="btn btn-outline-primary" href="{{ route('admin.validator.add') }}">Add New Validator</a>
        </x-slot>

        <div class="col-lg-6">
            <form method="get">
                <div class="input-group mb-3">
                    <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Search by holder or name">
                    <button class="btn btn-outline-secondary" type="submit">Search</button>
                </div>
            </form>
        </div>

        <div class="container-sm ms-0 ps-0 me-0 pe-0 d-none d-lg-block">
            <table class="table table-striped table-sm align-middle">
                <thead>
                <tr>
                    <th scope="col" class="text-center">No</th>
                    <th scope="col">Holder ({{ count($validators) }})</th>
                    <th scope="col">Name</th>
                    <th scope="col" class="text-end">Amount (Theta)</th>
                    <th scope="col" class="text-center">Created At</th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                @foreach ($validators as $validator)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td><a href="/account/{{ $validator->holder }}">{{ $validator->holder }}</a></td>
                        <td>{{ $validator->name }}</td>
                        <td class="text-end">{{ number_format($validator->amount, 0) }}</td>
                        <td class="text-center">{{ $validator->created_at->format('Y-m-d') }}</td>
                        <td class="text-end">
                            <a class="btn btn-outline-primary" href="{{ route('admin.validator.edit', ['id' => $validator->id]) }}">Edit</a>
                            <a class="btn btn-outline-primary" href="javascript:void(0)" onclick="confirmDelete('{{ route('admin.validator.delete', ['id' => $validator->id]) }}')">Delete</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="container-sm ms-0 ps-0 me-0 pe-0 d-block d-lg-none">
            <table class="table table-striped table-sm align-middle">
                <thead>
                <tr>
                    <th scope="col" class="text-center">No</th>
                    <th scope="col">Name ({{ count($validators) }})</th>
                    <th scope="col" class="text-end">Amount</th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                @foreach ($validators as $validator)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td><a href="/account/{{ $validator->holder }}">{{ $validator->name }}</a></td>
                        <td class="text-end">{{ number_format($validator->amount, 0) }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.validator.edit', ['id' => $validator->id]) }}">Edit</a>
                            <a href="javascript:void(0)" onclick="confirmDelete('{{ route('admin.validator.delete', ['id' => $validator->id]) }}')">Delete</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function confirmDelete(url) {
            if (confirm('Do you want to delete this validator?')) {
                location.href = url;
            }
        }
    </script>
</x-admin_layout>
