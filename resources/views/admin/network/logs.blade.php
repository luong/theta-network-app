<x-admin_layout pageName="logs">
    <div class="logs-page">
        <x-slot name="header">Logs in 7 Days</x-slot>

        <div class="container-sm ms-0 ps-0 me-0 pe-0 d-none d-lg-block">
            <table class="table table-striped table-sm align-middle">
                <thead>
                <tr>
                    <th scope="col" class="text-center">Level ({{ $logs->total() }})</th>
                    <th scope="col">Message</th>
                    <th scope="col" class="text-center">Created</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($logs as $log)
                    <tr>
                        <td class="text-center">{{ $log->level }}</td>
                        <td>{{ $log->message }}</td>
                        <td class="text-center">{{ $log->created_at }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $logs->links() }}
        </div>

        <div class="container-sm ms-0 ps-0 me-0 pe-0 d-block d-lg-none">
            <table class="table table-striped table-sm align-middle">
                <thead>
                <tr>
                    <th scope="col" class="text-center">Level ({{ $logs->total() }})</th>
                    <th scope="col">Message</th>
                    <th scope="col" class="text-center">Created</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($logs as $log)
                <tr>
                    <td class="text-center">{{ $log->level }}</td>
                    <td>{{ $log->message }}</td>
                    <td class="text-center">{{ $log->created_at }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
            {{ $logs->links() }}
        </div>

    </div>

</x-admin_layout>
