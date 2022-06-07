<x-admin_layout pageName="home">
    <div class="home-page">
        <x-slot name="header">Home</x-slot>

        <div class="container-fluid ms-0 ps-0 col-lg-6">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Command</th>
                        <th scope="col">Last Run</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($command_trackers as $command => $tracker)
                        <tr class="{{ date('Y-m-d', $tracker['last_run']) != date('Y-m-d') ? 'bg-warning' : '' }}">
                            <td>{{ $command }}</td>
                            <td>{{ $tracker['last_run'] ? date('Y-m-d H:i:s', $tracker['last_run']) : '' }}</td>
                            <td><a href="javascript:void(0)" onclick="runCommand('{{ $tracker['command'] }}')">Run</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function runCommand(command) {
            $.ajax({
                method: 'post',
                url: '/admin/run',
                data: { command: command }
            }).done(function( msg ) {
                alert('Running done.');
            });
        }
    </script>
</x-admin_layout>
