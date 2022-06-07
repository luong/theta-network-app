<x-admin_layout pageName="holders">
    <div class="holder-list-page">
        <x-slot name="header">Holders</x-slot>
        <x-slot name="menus">
            <a class="btn btn-outline-primary" href="{{ route('admin.holder.add') }}">Add New Holder</a>
        </x-slot>

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
                @foreach ($holders as $holder)
                    <tr>
                        <td><a href="{{ Helper::makeThetaAccountURL($holder->code) }}" target="_blank">{{ $holder->code }}</a></td>
                        <td>{{ $holder->name }}</td>
                        <td class="text-center">{{ $holder->created_at->format('Y-m-d') }}</td>
                        <td class="text-end">
                            <a class="btn btn-outline-primary" href="{{ route('admin.holder.edit', ['id' => $holder->id]) }}">Edit</a>
                            <a class="btn btn-outline-primary" href="javascript:void(0)" onclick="confirmDelete('{{ route('admin.holder.delete', ['id' => $holder->id]) }}')">Delete</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $holders->links() }}
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
                @foreach ($holders as $holder)
                    <tr>
                        <td><a href="{{ Helper::makeThetaAccountURL($holder->code) }}" target="_blank">{{ Str::limit($holder->code, 10) }}</a></td>
                        <td>{{ Str::limit($holder->name, 10) }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.holder.edit', ['id' => $holder->id]) }}">Edit</a>
                            <a href="javascript:void(0)" onclick="confirmDelete('{{ route('admin.holder.delete', ['id' => $holder->id]) }}')">Delete</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

    </div>

    <script>
        function confirmDelete(url) {
            if (confirm('Do you want to delete this holder?')) {
                location.href = url;
            }
        }
    </script>
</x-admin_layout>
