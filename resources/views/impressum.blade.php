<!doctype html>
<html lang="de">
@include('partials.head', ['title' => 'Impressum', 'withBootstrapIcons' => true])

<body>
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8 col-xl-7">

                <h1 class="mb-4">Impressum</h1>

                <p class="text-muted mb-4">
                    Hinweis: Diese Anwendung dient ausschließlich der firmeninternen Nutzung
                    und ist nicht öffentlich zugänglich. Die folgenden Angaben erfolgen
                    gemäß § 5 DDG (Digitale-Dienste-Gesetz).
                </p>

                <h2 class="h4 mt-4">Angaben gemäß § 5 DDG</h2>
                <p class="mb-1">{{-- Vor- und Nachname bzw. Firmenname --}}</p>
                <p class="mb-1">August Schweitzer</p>
                <p class="mb-0">Lange Gasse 10</p>
                <p class="mb-0">96337 Ludwigsstadt</p>
                <p class="mb-0">Deutschland</p>

                <h2 class="h4 mt-4">Kontakt</h2>
                <p class="mb-0">
                    E-Mail:
                    <a href="mailto:info@schweitzer-forst.de">info@schweitzer-forst.de</a>
                </p>

                <h2 class="h4 mt-4">Umsatzsteuer</h2>
                <p class="mb-0">
                    Gemäß § 19 UStG wird aufgrund der Kleinunternehmerregelung keine
                    Umsatzsteuer berechnet. Eine Umsatzsteuer-Identifikationsnummer
                    liegt daher nicht vor.
                </p>

                {{--
                Optional, falls vorhanden – sonst Block entfernen:

                <h2 class="h4 mt-4">Registereintrag</h2>
                <p class="mb-1">Handelsregister: [Registergericht]</p>
                <p class="mb-0">Registernummer: [HRB/HRA ...]</p>
            --}}

                

                <h2 class="h4 mt-4">Haftungshinweis</h2>
                <p class="small text-muted">
                    Trotz sorgfältiger inhaltlicher Kontrolle übernehmen wir keine Haftung
                    für die Inhalte externer Links. Für den Inhalt der verlinkten Seiten
                    sind ausschließlich deren Betreiber verantwortlich. Diese Seite ist
                    nur für interne Mitarbeiterinnen und Mitarbeiter bestimmt.
                </p>

            </div>
        </div>
    </div>
</body>
