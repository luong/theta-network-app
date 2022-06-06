<x-admin_layout pageName="validators">
    <div class="login-page">
        <x-slot name="header">
            Edit Validator
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

            <form method="post" action="{{ route('admin.validator.edit', ['id' => $validator->id]) }}" class="col-lg-6">
                @csrf
                <div class="mb-3 mt-3">
                    <label for="holder">Holder:</label>
                    <input type="text" class="form-control" id="holder" placeholder="Enter holder" name="holder" value="{{ old('holder', $validator->holder) }}"/>
                </div>
                <div class="mb-3 mt-3">
                    <label for="name">Name:</label>
                    <input type="text" class="form-control" id="name" placeholder="Enter name" name="name" value="{{ old('name', $validator->name) }}"/>
                </div>
                <div class="mb-3 mt-3">
                    <label for="amount">Amount:</label>
                    <input type="text" class="form-control" id="amount" placeholder="Enter amount" name="amount" value="{{ old('amount', $validator->amount) }}"/>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>

    </div>
</x-admin_layout>
