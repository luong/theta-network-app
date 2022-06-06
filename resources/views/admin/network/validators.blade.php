<x-admin_layout pageName="validators">
    <div class="edit-validator-page">
        <x-slot name="header">Validators</x-slot>
        <x-slot name="menus">
            <a class="btn btn-outline-primary" href="{{ route('admin.validator.add') }}">Add New Validator</a>
        </x-slot>

        <div class="container-sm ms-0 ps-0 d-none d-lg-block">
            <table class="table table-striped table-sm align-middle">
                <thead>
                <tr>
                    <th scope="col" class="text-center">No</th>
                    <th scope="col">Holder</th>
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
                        <td><a href="{{ Helper::makeThetaAccountURL($validator->holder) }}" target="_blank">{{ $validator->holder }}</a></td>
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

        <div class="container-sm ms-0 ps-0 d-block d-lg-none">
            <table class="table table-striped table-sm align-middle">
                <thead>
                <tr>
                    <th scope="col" class="text-center">No</th>
                    <th scope="col">Name</th>
                    <th scope="col" class="text-end">Amount (Theta)</th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                @foreach ($validators as $validator)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td><a href="{{ Helper::makeThetaAccountURL($validator->holder) }}" target="_blank">{{ $validator->name }}</a></td>
                        <td class="text-end">{{ number_format($validator->amount, 0) }}</td>
                        <td class="text-end">
                            <a class="btn btn-outline-primary" href="{{ route('admin.validator.edit', ['id' => $validator->id]) }}">Edit</a>
                            <a class="btn btn-outline-primary" href="javascript:void(0)" onclick="confirmDelete('{{ route('admin.validator.delete', ['id' => $validator->id]) }}')">Delete</a>
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
