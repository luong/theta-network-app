<x-admin_layout pageName="editUser">
    <div class="admins">
        <x-slot name="header">Edit Admin</x-slot>
        <x-slot name="menus">
        </x-slot>

        <div class="container-sm ms-0 ps-0">
            @if ($errors->any())
                <div class="alert alert-warning mt-4">
                    <ul class="m-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @elseif (session('message'))
                <div class="alert alert-success mt-4">{{ session('message') }}</div>
            @endif

            <form method="post" action="{{ route('admin.admin.edit', ['id' => $admin->id]) }}" class="col-lg-6">
                @csrf
                <div class="mb-3 mt-3">
                    <label for="username">Username:</label>
                    <input type="name" class="form-control" id="username" placeholder="Enter username" name="username" value="{{ old('username', $admin->username) }}"/>
                </div>
                <div class="mb-3 mt-3">
                    <label for="name">Name:</label>
                    <input type="name" class="form-control" id="name" placeholder="Enter name" name="name" value="{{ old('name', $admin->name) }}"/>
                </div>
                <div class="mb-3 mt-3">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" id="email" placeholder="Enter email" name="email" value="{{ old('email', $admin->email) }}"/>
                </div>
                <div class="mb-3 mt-3">
                    <label for="role">Role:</label>
                    <select class="form-select" name="role" id="role">
                        @foreach ($roles as $role)
                            <option value="{{ $role }}" {{ old('role', $admin->role) == $role ? 'selected' : '' }}>{{ $role }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="text-decoration-underline fst-italic">* Leave password blank if you don't want to change it</div>
                <div class="mb-3 mt-3">
                    <label for="password">New Password:</label>
                    <input type="password" class="form-control" id="password" placeholder="Enter new password" name="password"/>
                </div>
                <div class="mb-3 mt-3">
                    <label for="password_confirmation">Confirm New Password:</label>
                    <input type="password" class="form-control" id="password_confirmation" placeholder="Enter new password again" name="password_confirmation">
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>

    </div>
</x-admin_layout>
