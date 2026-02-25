<!doctype html>
<html lang="de">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <title>Harvester</title>
  <link rel="icon" href="{{ asset('media/icons/wood.svg') }}" type="image/svg+xml">
</head>

<body>
  <form class="container">
    <!-- Date -->
    <div class="container my-3 px-0">
      <label for="date" class="form-label">Datum</label>
      <input id="date" class="form-control" type="date" />
      <script>
        document.addEventListener("DOMContentLoaded", function () {
          const input = document.getElementById("date");
          const today = new Date().toISOString().split("T")[0];  // YYYY-MM-DD
          input.value = today;
        });
      </script>
    </div>
    <div class="accordion" id="accordionExample">
      <div class="accordion-item">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
            data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
            Baustelle #1
          </button>
        </h2>
        <div id="collapseOne" class="accordion-collapse collapse " data-bs-parent="#accordionExample">
          <div class="accordion-body px-2">
            <!-- Arbeitszeit -->
            <div class="mb-2">
              <h3>Arbeitszeit</h3>

              <div class="row mb-3">
                <div class="col-6">
                  <label for="start" class="form-label">Von</label>
                  <input type="time" id="start" class="form-control">
                </div>

                <div class="col-6">
                  <label for="end" class="form-label">Bis</label>
                  <input type="time" id="end" class="form-control">
                </div>
              </div>

            </div>
            <!-- Betriebsstunden -->
            <div class="mb-2">
              <h3>Betriebsstunden</h3>

              <div class="row mb-3">
                <div class="col-5 pe-2 me-0">
                  <label for="start" class="form-label">Von</label>
                  <input type="number" id="start" class="form-control">
                </div>

                <div class="col-5 ps-1">
                  <label for="end" class="form-label">Bis</label>
                  <input type="number" id="end" class="form-control">
                </div>

                <div class="col-2 ps-0">
                  <label for="end" class="form-label">Diff.</label>
                  <input type="text" id="end" class="form-control" readonly>
                </div>
              </div>

            </div>

            <hr class="mx-3">

            <!-- Festmeter -->
            <div class="mb-2">
              <h3 class="mt-2">Festmeter</h3>
              <div class="row">
                <div class="col-4">
                  <label for="stueckzahl" class="form-label">Stückzahl</label>
                  <input type="number" id="stueckzahl" class="form-control">
                </div>
                <div class="col-4">
                  <label for="fm_gesamt" class="form-label">Gesamt fm</label>
                  <input type="number" id="fm_gesamt" class="form-control">
                </div>
                <div class="col-4">
                  <label for="day_fm" class="form-label">fm/Tag</label>
                  <input type="text" id="day_fm" class="form-control" read-only>
                </div>

              </div>
            </div>

            <hr class="mx-3">

            <div class="mb-2">
              <h3 class="mt-2">Weitere Arbeiten</h3>

              <div class="row mb-2">
                <div class="col-6">
                  <label for="worktype-dropdown" class="form-label"> <strong>Arbeitsart</strong></label>
                  <div class="dropdown" id="worktype-dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                      aria-expanded="false">
                      Motorsäge
                    </button>

                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item" href="#">Freischneider</a></li>
                      <li><a class="dropdown-item" href="#">Seilmaschine</a></li>
                      <li><a class="dropdown-item" href="#">Messkluppe</a></li>
                      <li><a class="dropdown-item" href="#">Reparatur</a></li>
                      <li><a class="dropdown-item" href="#">Sonstiges</a></li>
                    </ul>
                  </div>
                </div>
                <div class="col-6">
                  <label for="hours" class="form-label">Stunden</label>
                  <input type="number" id="hours" class="form-control">
                </div>
              </div>


              <div class="mb-3">
                <label for="exampleFormControlTextarea1" class="form-label">Erklärung</label>
                <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
              </div>

              <hr class="mx-3">

              <!--Arbeitsart 2-->
              <div class="d-none" id="arbeitsart_3">
                <div class="row mb-2">
                  <div class="col-6">
                    <label for="worktype-dropdown" class="form-label"> <strong>Arbeitsart</strong></label>
                    <div class="dropdown" id="worktype-dropdown">
                      <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        Freischneider
                      </button>

                      <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Seilmaschine</a></li>
                        <li><a class="dropdown-item" href="#">Messkluppe</a></li>
                        <li><a class="dropdown-item" href="#">Reparatur</a></li>
                        <li><a class="dropdown-item" href="#">Sonstiges</a></li>
                      </ul>
                    </div>
                  </div>
                  <div class="col-6">
                    <label for="hours" class="form-label">Stunden</label>
                    <input type="number" id="hours" class="form-control">
                  </div>
                </div>


                <div class="mb-3">
                  <label for="exampleFormControlTextarea2" class="form-label">Erklärung</label>
                  <textarea class="form-control" id="exampleFormControlTextarea2" rows="3"></textarea>
                </div>

                <hr class="mx-3">
              </div>

              <div class="mt-2 mx-3">
                <button type="button" class="btn btn-primary" onclick="show_arbeitsart()">Klicke, um weiter Arbeitsart
                  hinzuzufügen</button>
                <script>
                  function show_arbeitsart() {
                    document.getElementById("arbeitsart_3").classList.remove("d-none");
                  }
                </script>
              </div>
            </div>


          </div>
        </div>
      </div>

      <div class="accordion-item">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
            data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
            Baustelle #2
          </button>
        </h2>
        <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
          <div class="accordion-body px-2">
            <!-- Arbeitszeit -->
            <div class="mb-2">
              <h3>Arbeitszeit</h3>

              <div class="row mb-3">
                <div class="col-6">
                  <label for="start" class="form-label">Von</label>
                  <input type="time" id="start" class="form-control">
                </div>

                <div class="col-6">
                  <label for="end" class="form-label">Bis</label>
                  <input type="time" id="end" class="form-control">
                </div>
              </div>

            </div>
            <!-- Betriebsstunden -->
            <div class="mb-2">
              <h3>Betriebsstunden</h3>

              <div class="row mb-3">
                <div class="col-5 pe-2 me-0">
                  <label for="start" class="form-label">Von</label>
                  <input type="number" id="start" class="form-control">
                </div>

                <div class="col-5 ps-1">
                  <label for="end" class="form-label">Bis</label>
                  <input type="number" id="end" class="form-control">
                </div>

                <div class="col-2 ps-0">
                  <label for="end" class="form-label">Diff.</label>
                  <input type="text" id="end" class="form-control" readonly>
                </div>
              </div>

            </div>

            <hr class="mx-3">

            <!-- Festmeter -->
            <div class="mb-2">
              <h3 class="mt-2">Festmeter</h3>
              <div class="row">
                <div class="col-4">
                  <label for="stueckzahl" class="form-label">Stückzahl</label>
                  <input type="number" id="stueckzahl" class="form-control">
                </div>
                <div class="col-4">
                  <label for="fm_gesamt" class="form-label">Gesamt fm</label>
                  <input type="number" id="fm_gesamt" class="form-control">
                </div>
                <div class="col-4">
                  <label for="day_fm" class="form-label">fm/Tag</label>
                  <input type="text" id="day_fm" class="form-control" read-only>
                </div>

              </div>
            </div>

            <hr class="mx-3">

            <div class="mb-2">
              <h3 class="mt-2">Weitere Arbeiten</h3>

              <div class="row mb-2">
                <div class="col-6">
                  <label for="worktype-dropdown" class="form-label"> <strong>Arbeitsart</strong></label>
                  <div class="dropdown" id="worktype-dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                      aria-expanded="false">
                      Motorsäge
                    </button>

                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item" href="#">Freischneider</a></li>
                      <li><a class="dropdown-item" href="#">Seilmaschine</a></li>
                      <li><a class="dropdown-item" href="#">Messkluppe</a></li>
                      <li><a class="dropdown-item" href="#">Reparatur</a></li>
                      <li><a class="dropdown-item" href="#">Sonstiges</a></li>
                    </ul>
                  </div>
                </div>
                <div class="col-6">
                  <label for="hours" class="form-label">Stunden</label>
                  <input type="number" id="hours" class="form-control">
                </div>
              </div>


              <div class="mb-3">
                <label for="exampleFormControlTextarea1" class="form-label">Erklärung</label>
                <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
              </div>

              <hr class="mx-3">


              <div class="mt-2 mx-3">
                <button type="button" class="btn btn-primary">Klicke, um weiter Arbeitsart
                  hinzuzufügen</button>
              </div>
            </div>


          </div>

        </div>
      </div>

      <div class="accordion-item">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
            data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
            Baustelle #3
          </button>
        </h2>
        <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
          <div class="accordion-body px-2">
            <!-- Arbeitszeit -->
            <div class="mb-2">
              <h3>Arbeitszeit</h3>

              <div class="row mb-3">
                <div class="col-6">
                  <label for="start" class="form-label">Von</label>
                  <input type="time" id="start" class="form-control">
                </div>

                <div class="col-6">
                  <label for="end" class="form-label">Bis</label>
                  <input type="time" id="end" class="form-control">
                </div>
              </div>

            </div>
            <!-- Betriebsstunden -->
            <div class="mb-2">
              <h3>Betriebsstunden</h3>

              <div class="row mb-3">
                <div class="col-5 pe-2 me-0">
                  <label for="start" class="form-label">Von</label>
                  <input type="number" id="start" class="form-control">
                </div>

                <div class="col-5 ps-1">
                  <label for="end" class="form-label">Bis</label>
                  <input type="number" id="end" class="form-control">
                </div>

                <div class="col-2 ps-0">
                  <label for="end" class="form-label">Diff.</label>
                  <input type="text" id="end" class="form-control" readonly>
                </div>
              </div>

            </div>

            <hr class="mx-3">

            <!-- Festmeter -->
            <div class="mb-2">
              <h3 class="mt-2">Festmeter</h3>
              <div class="row">
                <div class="col-4">
                  <label for="stueckzahl" class="form-label">Stückzahl</label>
                  <input type="number" id="stueckzahl" class="form-control">
                </div>
                <div class="col-4">
                  <label for="fm_gesamt" class="form-label">Gesamt fm</label>
                  <input type="number" id="fm_gesamt" class="form-control">
                </div>
                <div class="col-4">
                  <label for="day_fm" class="form-label">fm/Tag</label>
                  <input type="text" id="day_fm" class="form-control" read-only>
                </div>

              </div>
            </div>

            <hr class="mx-3">

            <div class="mb-2">
              <h3 class="mt-2">Weitere Arbeiten</h3>

              <div class="row mb-2">
                <div class="col-6">
                  <label for="worktype-dropdown" class="form-label"> <strong>Arbeitsart</strong></label>
                  <div class="dropdown" id="worktype-dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                      aria-expanded="false">
                      Motorsäge
                    </button>

                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item" href="#">Freischneider</a></li>
                      <li><a class="dropdown-item" href="#">Seilmaschine</a></li>
                      <li><a class="dropdown-item" href="#">Messkluppe</a></li>
                      <li><a class="dropdown-item" href="#">Reparatur</a></li>
                      <li><a class="dropdown-item" href="#">Sonstiges</a></li>
                    </ul>
                  </div>
                </div>
                <div class="col-6">
                  <label for="hours" class="form-label">Stunden</label>
                  <input type="number" id="hours" class="form-control">
                </div>
              </div>


              <div class="mb-3">
                <label for="exampleFormControlTextarea1" class="form-label">Erklärung</label>
                <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
              </div>

              <hr class="mx-3">


              <div class="mt-2 mx-3">
                <button type="button" class="btn btn-primary">Klicke, um weiter Arbeitsart
                  hinzuzufügen</button>
              </div>
            </div>


          </div>

        </div>
      </div>

    </div>

    <div class="container d-flex justify-content-center">
      <button class="btn btn-success my-3" type="submit">Eintrag speichern</button>
    </div>
  </form>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
    crossorigin="anonymous"></script>
  <script>
    function calculateWorkingHours() {
      const start = document.getElementById("start").value;
      const end = document.getElementById("end").value;
      const pause = parseInt(document.getElementById("pause").value || 0, 10);

      if (!start || !end) {
        document.getElementById("summe").value = "";
        return;
      }

      const startDate = new Date(`1970-01-01T${start}:00`);
      const endDate = new Date(`1970-01-01T${end}:00`);

      let diff = (endDate - startDate) / 60000; // minutes

      // Handle end after midnight if needed:
      if (diff < 0) diff += 24 * 60;

      diff -= pause; // subtract pause
      if (diff < 0) diff = 0;

      const hours = Math.floor(diff / 60);
      const minutes = diff % 60;

      document.getElementById("summe").value =
        `${String(hours).padStart(2, "0")}:${String(minutes).padStart(2, "0")}`;
    }

    ["start", "end", "pause"].forEach(id =>
      document.getElementById(id).addEventListener("input", calculateWorkingHours)
    );
  </script>
</body>

</html>