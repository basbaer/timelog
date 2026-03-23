<!doctype html>
<html lang="de">

@include('partials.head', ['title' => 'Dashboard'])

<body>
    <nav class="navbar navbar-expand bg-body-tertiary">
        <div class="container-fluid">

            <div class="collapse navbar-collapse justify-content-center" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Projekte</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Mitarbeiter</a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>


    <!-- Project Item -->
    <div class="container px-0 mt-3" id="projectItem">
        <h2>Ottendorf | 01/26 | Stängel</h2>
        <div class="accordion mx-0 p-0" id="accordionExample">

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                        <div class="container p-0 m-0">
                            <div class="row p-0 m-0">
                                <h5 class="col-8 p-0 m-0">Harvester</h5>
                                <h5 class="col-2 text-end border-end py-0 m-0" id="harvester_hours">18h</h5>
                                <h5 class="col-2 text-start py-0 m-0" id="harvester_hours">12fm</h5>
                            </div>
                        </div>
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse " data-bs-parent="#accordionExample">
                    <div class="accordion-body p-1">
                        <table class="table p-0">
                            <thead>
                                <tr>
                                    <th scope="col">Datum</th>
                                    <th scope="col">Arbeiter</th>
                                    <th scope="col">Zeit</th>
                                    <th scope="col">Stk.</th>
                                    <th scope="col">fm/Tag</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td scope="row">06.12.25</td>
                                    <td>B. Bär</td>
                                    <td>8h</td>
                                    <td>10</td>
                                    <td>5</td>
                                </tr>
                                <tr>
                                    <td scope="row">07.12.25</td>
                                    <td>B. Bär</td>
                                    <td>10h</td>
                                    <td>13</td>
                                    <td>7</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        <div class="container p-0 m-0">
                            <div class="row p-0 m-0">
                                <h5 class="col-9 p-0 m-0">Rückezug</h5>
                                <h5 class="col-2 text-end p-0 m-0" id="rueckezug_hours">3h</h5>
                            </div>
                        </div>
                    </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse " data-bs-parent="#accordionExample">
                    <div class="accordion-body p-1">
                        <table class="table p-0">
                            <thead>
                                <tr>
                                    <th scope="col">Datum</th>
                                    <th scope="col">Arbeiter</th>
                                    <th scope="col">Zeit</th>
                                    <th scope="col">Fuhren</th>
                                    <th scope="col">m</th>

                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td scope="row">06.12.25</td>
                                    <td>B. Bär</td>
                                    <td>3h</td>
                                    <td>10</td>
                                    <td>200</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        <div class="container p-0 m-0">
                            <div class="row p-0 m-0">
                                <h5 class="col-9 p-0 m-0">Seilmaschine</h5>
                                <h5 class="col-2 text-end p-0 m-0" id="seilmaschine_hours">11h</h5>
                            </div>
                        </div>
                    </button>
                </h2>
                <div id="collapseThree" class="accordion-collapse collapse " data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <table class="table p-0">
                            <thead>
                                <tr>
                                    <th scope="col">Datum</th>
                                    <th scope="col">Mitarbeiter</th>
                                    <th scope="col">Zeit</th>
                                    <th scope="col">Bemerkung</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>06.12.25</td>
                                    <td>Rumäne #1</td>
                                    <td>3h</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>06.12.25</td>
                                    <td>Rumäne #2</td>
                                    <td>8h</td>
                                    <td>blablablba</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                        <div class="container p-0 m-0">
                            <div class="row p-0 m-0">
                                <h5 class="col-9 p-0 m-0">Motorsäge</h5>
                                <h5 class="col-2 text-end p-0 m-0" id="motorsaege_hours">5h</h5>
                            </div>
                        </div>
                    </button>
                </h2>
                <div id="collapseFour" class="accordion-collapse collapse " data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <table class="table p-0">
                            <thead>
                                <tr>
                                    <th scope="col">Datum</th>
                                    <th scope="col">Mitarbeiter</th>
                                    <th scope="col">Zeit</th>
                                    <th scope="col">Bemerkung</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>06.12.25</td>
                                    <td>Rumäne #1</td>
                                    <td>5h</td>
                                    <td>Langer Tag, schwere Arbeit</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                        <div class="container p-0 m-0">
                            <div class="row p-0 m-0">
                                <h5 class="col-9 p-0 m-0">Freischneider</h5>
                                <h5 class="col-2 text-end p-0 m-0" id="freischneider_hours">8h</h5>
                            </div>
                        </div>
                    </button>
                </h2>
                <div id="collapseFive" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Datum</th>
                                    <th scope="col">Mitarbeiter</th>
                                    <th scope="col">Zeit</th>
                                    <th scope="col">Bemerkung</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td scope="row">06.12.25</td>
                                    <td>Rumäne #3</td>
                                    <td>8h</td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>


            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                        <div class="container p-0 m-0">
                            <div class="row p-0 m-0">
                                <h5 class="col-9 p-0 m-0">Sonstige Arbeiten</h5>
                            </div>
                        </div>
                    </button>
                </h2>
                <div id="collapseSix" class="accordion-collapse collapse" data-bs-parent="#accordionExample">

                    <div class="accordion-body p-1">
                        <table class="table p-0">
                            <thead>
                                <tr>
                                    <th scope="col">Datum</th>
                                    <th scope="col">Mitarbeiter</th>
                                    <th scope="col">Zeit</th>
                                    <th scope="col">Bemerkung</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>06.12.25</td>
                                    <td>Gustl Schweitzer</td>
                                    <td>3h</td>
                                    <td>Christbaum abgesägt</td>
                                </tr>
                                <tr>
                                    <td>06.12.25</td>
                                    <td>Birgit Bär</td>
                                    <td>4h</td>
                                    <td>Entastung</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>


            </div>


        </div>
    </div>

    <div class="container mt-4">
        <div class="row d-flex justify-content-between gap-3">
            <div class="form-check col-6">
                <input class="form-check-input" type="checkbox" value="" id="defaultCheck1">
                <label class="form-check-label" for="defaultCheck1">
                    Projekt abgeschlossen
                </label>
            </div>
            <a href="dash-bau-overview.html" class="btn btn-primary col-4">Projekt bearbeiten</a>
        </div>
    </div>
    <div class="container my-4">
        <hr>
    </div>

    <div class="container d-flex justify-content-center">
        <a href="dash-bau-overview.html" class="btn btn-primary">Zurück zur Übersicht</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

</body>

</html>