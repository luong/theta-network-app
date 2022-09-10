<x-admin_layout pageName="accounts">
    <div class="add-account-page">
        <x-slot name="header">
            Add New Account
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

            <form method="post" action="{{ route('admin.account.add') }}" class="col-lg-6">
                @csrf
                <div class="mb-3 mt-3">
                    <label for="code">Code:</label>
                    <input type="text" class="form-control" id="code" placeholder="Enter code" name="code" value="{{ old('code') }}"/>
                </div>
                <div class="mb-3 mt-3">
                    <label for="name">Name:</label>
                    <input type="text" class="form-control" id="name" placeholder="Enter name" name="name" value="{{ old('name') }}"/>
                </div>
                <div class="mb-3 mt-3">
                    <label for="tags">Tags:</label>
                    <div>
                        <input class="form-check-input" type="checkbox" name="tags[]" value="validator" id="validatorCheck"/> <label class="form-check-label" for="validatorCheck">Validator</label>
                        <input class="form-check-input" type="checkbox" name="tags[]" value="exchange" id="exchangeCheck"/> <label class="form-check-label" for="exchangeCheck">Exchange</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>

    </div>
</x-admin_layout>
