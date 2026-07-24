<!doctype html>
<html lang="de">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <title>{{ $title ?? config('app.name', 'Timelog') }}</title>
    @stack('head')
    @include('partials.favicon')
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #111;
            margin: 0;
            padding: 1.5rem;
        }

        .no-print {
            margin-bottom: 1rem;
        }

        .meta {
            margin-bottom: 1rem;
        }

        .meta dl {
            display: grid;
            grid-template-columns: max-content 1fr;
            gap: 0.15rem 0.75rem;
            margin: 0;
        }

        .meta dt {
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        thead th {
            border-bottom: 2px solid #333;
            text-align: left;
            padding: 3px 6px;
            background: #f2f2f2;
        }

        tbody td {
            border-bottom: 1px solid #ddd;
            padding: 3px 6px;
        }

        tfoot td {
            border-top: 2px solid #333;
            font-weight: bold;
            padding: 4px 6px;
        }

        /* Prevents a row from being split across a page break */
        tr {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                padding: 0;
            }

            @page {
                size: A4;
                margin: 1.5cm;
            }
        }
    </style>
</head>

<body>
    <div class="no-print d-flex justify-content-between align-items-center gap-3 mx-3">
        <button type="button" onclick="window.print()" class="btn btn-primary">
            {{ __('form.print') }}
        </button>
        <button type="button"
            onclick="window.location.href = '{{ route('workers.preparePrint', ['worker_id' => $worker->id]) }}'"
            class="btn btn-secondary">
            {{ __('form.back') }}
        </button>
    </div>


    <div class="meta container-fluid d-flex align-items-center justify-content-center gap-5 px-3 mb-3 ms-2">
        @if ($project !== 'all')
            <div>
                {{ __('form.project') }}: {{ $project->title }}
            </div>
        @endif
        <div>
            {{ __('form.worker') }}: {{ $worker->first_name }} {{ $worker->last_name }}
        </div>
        @if ($timeframe !== 'whole')
            <div>
                {{ __('form.timeframe') }}: {{ $timeframe }}
            </div>
        @endif
    </div>

    @php
        $role = $worker->role->slug;
        $worker_id = $worker->id;
    @endphp

    @if (!$hasMultipleLogTypes)
        <table>
            <thead>
                <tr>
                    @foreach ($tableHeaders->get($role) as $header)
                        <th>{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>

            <tbody>
                @foreach ($logs as $log)
                <tr>
                    @foreach ($tableHeaders->get($role) as $header => $headerLabel)
                        <td>{{ $log->$header }}</td>
                    @endforeach
                    </tr>
                @endforeach
            </tbody>

        </table>


    @endif


    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
    </script>

</body>
