@props(['log_entries'])

<table class="table">
    <thead>
        <tr>
            <th scope="col">Tag</th>
            <th scope="col">Datum</th>
            <th scope="col">Von</th>
            <th scope="col">Bis</th>
            <th scope="col">Pause</th>
            <th scope="col">Gesamt</th>
            <th scope="col">Baustelle</th>
            <th scope="col">Arbeitsart</th>
            <th scope="col">Anmerkung</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($log_entries as $row)
            <tr>
                <th scope="row">
                    @if ($row->show_date)
                        {{ $row->weekday }}
                    @endif
                </th>
                <td>
                    @if ($row->show_date)
                        {{ $row->date }}
                    @endif
                </td>
                <td>{{ $row->start }}</td>
                <td>{{ $row->end }}</td>
                <td>{{ $row->pause }}</td>
                <td>{{ $row->sum }}</td>
                <td>{{ $row->project_client }} ({{ $row->project_location }})</td>
                <td>{{ $row->working_type_name }}</td>
                <td>{{ $row->comment }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
