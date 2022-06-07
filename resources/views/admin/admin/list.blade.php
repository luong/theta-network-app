<x-admin_layout pageName="admins">
    <div class="login-page">
        <x-slot name="header">Admins</x-slot>
        <x-slot name="menus">
            <a class="btn btn-outline-primary" href="{{ route('admin.admin.add') }}">Add New Admin</a>
        </x-slot>

        <div class="container-sm ms-0 ps-0 d-none d-lg-block">
            <table class="table table-striped table-sm align-middle">
                <thead>
                <tr>
                    <th scope="col">Username</th>
                    <th scope="col">Email</th>
                    <th scope="col">Name</th>
                    <th scope="col">Role</th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                @foreach ($admins as $admin)
                    <tr>
                        <td>{{ $admin->username }}</td>
                        <td>{{ $admin->email }}</td>
                        <td>{{ $admin->name }}</td>
                        <td>{{ $admin->role }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.admin.edit', ['id' => $admin->id]) }}">Edit</a>
                            <a class="ms-2" href="javascript:void(0)" onclick="confirmDelete('{{ route('admin.admin.delete', ['id' => $admin->id]) }}')">Delete</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="container-sm ms-0 ps-0 pe-0 d-block d-lg-none">
            <table class="table table-striped table-sm align-middle">
                <thead>
                <tr>
                    <th scope="col">Username</th>
                    <th scope="col">Name</th>
                    <th scope="col">Role</th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                @foreach ($admins as $admin)
                    <tr>
                        <td>{{ $admin->username }}</td>
                        <td>{{ $admin->name }}</td>
                        <td>{{ $admin->role }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.admin.edit', ['id' => $admin->id]) }}">Edit</a>
                            <a class="ms-2" href="javascript:void(0)" onclick="confirmDelete('{{ route('admin.admin.delete', ['id' => $admin->id]) }}')">Delete</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

    </div>

    <script>
        function confirmDelete(url) {
            if (confirm('Do you want to delete this admin?')) {
                location.href = url;
            }
        }
    </script>
</x-admin_layout>
