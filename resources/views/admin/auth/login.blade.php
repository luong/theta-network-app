<x-admin_layout pageName="login">
    <div class="login-page">
        <x-slot name="header">Login</x-slot>

        <div class="container-fluid ms-0 ps-0 login-form">
            @if ($errors->any())
                <div class="alert alert-warning">
                    @foreach ($errors->all() as $error)
                        {{ $error }} <br/>
                    @endforeach
                </div>
            @endif

            <form method="post" action="{{ route('admin.login') }}">
                <div class="col-md-8 col-lg-4">
                    @csrf
                    <div class="mb-3 mt-3">
                        <label for="username">Username:</label>
                        <input type="text" class="form-control" id="username" placeholder="Enter username" name="username"/>
                    </div>
                    <div class="mb-3">
                        <label for="pwd">Password:</label>
                        <input type="password" class="form-control" id="pwd" placeholder="Enter password" name="password">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</x-admin_layout>
