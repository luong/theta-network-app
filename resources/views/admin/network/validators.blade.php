<x-admin_layout pageName="validators">
    <div class="edit-validator-page">
        <x-slot name="header">Validators</x-slot>

        <div class="mb-2">Found: ({{ number_format(count($validators), 0) }})</div>

        <div class="container-sm ms-0 ps-0 me-0 pe-0">
            <table class="table table-striped table-sm align-middle">
                <thead>
                <tr>
                    <th scope="col" class="text-center">No</th>
                    <th scope="col">Holder</th>
                    <th scope="col" class="text-end">Amount (Theta)</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($validators as $holder => $props)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>
                            <span class="d-none d-lg-inline-block"><a href="/account/{{ $holder }}">{{ $holder }}</a> {{ isset($accounts[$holder]) ? ' (' . $accounts[$holder]['name'] . ')' : '' }}</span>
                            <span class="d-inline-block d-lg-none"><a href="/account/{{ $holder }}">{{ Str::limit($holder, 5) }}</a> {{ isset($accounts[$holder]) ? ' (' . $accounts[$holder]['name'] . ')' : '' }}</span>
                        </td>
                        <td class="text-end">{{ number_format($props['coins'], 0) }}</td>
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
