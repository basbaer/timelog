<!doctype html>
<html lang="de">

@include('partials.head', ['title' => 'Dashboard'])

<body>
    @include('partials.admin_navbar', ['active' => 'workers'])
    
    <div class="mt-3">
        <div class="row">
            <div class="col justify-content-start d-flex">
                <h2 class="h2 m-3">Birgit Bär</h2>
            </div>
            <div class="col justify-content-end d-flex">
                <a href="dash-workerCard.html" type="button" class="btn btn-secondary m-3">Bearbeiten</a>
            </div>
        </div>
    </div>
        <div class=" m-3">
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
                        <th scope="col">Stunden</th>
                        <th scope="col">Anmerkung</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row">Di</th>
                        <td>16.12.25</td>
                        <td>08:00</td>
                        <td>17:00</td>
                        <td>00:30</td>
                        <td>8,5h</td>
                        <td>Baustelle unten beim Heinzle</td>
                        <td>Motorsäge</td>
                        <td>4h</td>
                        <td></td>
                    </tr>
                    <tr>
                        <th scope="row"></th>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>Baustelle beim Schester</td>
                        <td>Freischneider</td>
                        <td>4,5h</td>
                        <td></td>
                    </tr>
                    <tr>
                        <th scope="row">Mo</th>
                        <td>15.12.25</td>
                        <td>08:00</td>
                        <td>16:30</td>
                        <td>00:30</td>
                        <td>8h</td>
                        <td>Baustelle beim Schester</td>
                        <td>Motorsäge</td>
                        <td>8h</td>
                        <td></td>
                    </tr>
                    <tr>

                        <th scope="row">Fr</th>
                        <td>12.12.25</td>
                        <td>08:00</td>
                        <td>16:30</td>
                        <td>00:30</td>
                        <td>8h</td>
                        <td>Baustelle beim Schester</td>
                        <td>Motorsäge</td>
                        <td>8h</td>
                        <td></td>
                    </tr>
                    <tr>
                        <th scope="row">Do</th>
                        <td>11.12.25</td>
                        <td>08:00</td>
                        <td>16:30</td>
                        <td>00:30</td>
                        <td>8h</td>
                        <td>Baustelle beim Schester</td>
                        <td>Motorsäge</td>
                        <td>8h</td>
                        <td></td>
                </tbody>
            </table>
        </div>
        <div class="container mt-3 d-flex justify-content-center">
            <a href="log-forstwirt.html" role="button" class="btn btn-primary">Eintrag hinzufügen</a>
        </div>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
            crossorigin="anonymous"></script>

</body>

</html>