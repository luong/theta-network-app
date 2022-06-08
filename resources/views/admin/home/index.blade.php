<x-admin_layout pageName="home">
    <div class="home-page">
        <x-slot name="header">Home</x-slot>

        <div class="container-fluid ms-0 ps-0 pe-0 col-lg-6">
            <div class="mb-2">Current time is <span class="text-decoration-underline">{{ date('Y-m-d H:i:s') }}</span></div>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Command</th>
                        <th scope="col">Last Run</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($command_trackers as $name => $tracker)
                        <tr class="{{ !empty($tracker['last_run']) && date('Y-m-d', $tracker['last_run']) == date('Y-m-d') ? '' : 'bg-warning' }}" command_name="{{ $name }}">
                            <td>{{ $name }}</td>
                            <td>{{ $tracker['last_run'] ? date('Y-m-d H:i:s', $tracker['last_run']) : '' }}</td>
                            <td><span class="spinner spinner-border spinner-border-sm d-none"></span> <a class="link" href="javascript:void(0)" onclick="runCommand('{{ $name }}', '{{ $tracker['command'] }}')">Run</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function runCommand(name, command) {
            let tr = $('tr[command_name=' + name + ']');
            tr.find('.spinner').removeClass('d-none').addClass('d-inline-block');
            tr.find('.link').removeClass('d-inline-block').addClass('d-none');
            $.ajax({
                method: 'post',
                url: '/admin/run',
                data: { command: command }
            }).done(function( msg ) {
                tr.find('.link').removeClass('d-none').addClass('d-inline-block');
                tr.find('.spinner').removeClass('d-inline-block').addClass('d-none');
            });
        }
    </script>
</x-admin_layout>
