<!doctype html>
<html lang="de">

@include('partials.head', ['title' => 'Log'])

<body>
    <form id="prepare-print-form" class="container border rounded my-3" method="POST"
        action="{{ route('workers.preparePrint.post', ['worker_id' => $worker->id]) }}">
        @csrf
        <input type="hidden" name="worker_id" value="{{ $worker->id }}">

        <h3 class="mt-2">{{ __('form.print_prepare') }}</h3>

        {{-- ================= PROJEKT-AUSWAHL ================= --}}
        <div class="container my-3 px-0">
            <h5 class="form-label d-block">{{ __('form.project') }}</h5>

            <div id="project-radio-list">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="project" id="project-all" value="all"
                        checked>
                    <label class="form-check-label" for="project-all">
                        {{ __('form.all') }}
                    </label>
                </div>

                @foreach ($projects as $project)
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="project" id="project-{{ $project->id }}"
                            value="{{ $project->id }}">
                        <label class="form-check-label" for="project-{{ $project->id }}">
                            {{ $project->title }}
                        </label>
                    </div>
                @endforeach

            </div>

            {{-- Weitere Projekte laden --}}

            @if ($hasClosedProjects)
                <button type="button" id="load-closed-projects" class="btn btn-link px-0">
                    {{ __('form.load_older_projects') }}
                </button>
            @endif
        </div>

        {{-- ================= ZEITRAUM-AUSWAHL ================= --}}

        <div class="container my-3 px-0">
            <label class="form-label d-block">{{ __('form.timeframe') }}</label>

            <div class="form-check">
                <input class="form-check-input" type="radio" name="timeframe" id="timeframe-whole" value="whole"
                    checked>
                <label class="form-check-label" for="timeframe-whole">
                    {{ __('form.whole_project') }}
                </label>
            </div>

            <div class="form-check d-flex align-items-center gap-2">
                <input class="form-check-input" type="radio" name="timeframe" id="timeframe-month" value="month">
                <label class="form-check-label" for="timeframe-month">
                    {{ __('form.specific_month') }}
                </label>
                <input type="month" name="month" id="month-input" class="form-control w-auto" disabled required
                    pattern="(0[1-9]|1[0-2])/\d{2}" placeholder="mm/yy (z.b. 03/26)">
            </div>
        </div>

        {{-- ================= WORKTYPE CHECKBOXES ================= --}}
        @if ($worker->role->slug !== 'forstwirt')
            <div class="container my-3 px-0">
                <h5 class="form-label d-block">{{ __('form.work_types') }}</h5>
                <p id="work-type-warning" class="text-danger d-none">{{ __('form.work_type_warning') }}</p>
                <div class="form-check">
                    @php $role = $worker->role->slug @endphp
                    <input class="form-check-input work-type-checkbox" type="checkbox" name="work-type-{{ $role }}" id="work-type-{{ $role }}"
                        value="true" checked>
                    <label class="form-check-label" for="work-type-{{ $role }}">
                        {{ __('form.' . $role) }}
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input work-type-checkbox" type="checkbox" name="work-type-forstwirt" id="work-type-forstwirt" value="true" checked>
                    <label class="form-check-label" for="work-type-forstwirt">
                        {{ __('form.forstwirt') }}
                    </label>
                </div>


            </div>
        @endif

        <div class="container my-3 px-0">
            <button type="submit" class="btn btn-primary">{{ __('form.print_view') }}</button>
        </div>
    </form>

    <script>
        (function() {
            const monthInput = document.getElementById('month-input');
            const timeframeRadios = document.querySelectorAll('input[name="timeframe"]');

            function syncMonthInput() {
                const monthSelected = document.getElementById('timeframe-month').checked;
                monthInput.disabled = !monthSelected;
                if (monthSelected) {
                    monthInput.focus();
                }
            }

            timeframeRadios.forEach(radio => radio.addEventListener('change', syncMonthInput));

            // ---- Load closed projects (one-shot AJAX reveal, no pagination) ----
            const loadClosedBtn = document.getElementById('load-closed-projects');
            if (loadClosedBtn) {
                loadClosedBtn.addEventListener('click', function() {
                    const workerId = document.querySelector('input[name="worker_id"]').value;

                    loadClosedBtn.disabled = true;
                    loadClosedBtn.textContent = '{{ __('form.loading') }}';

                    fetch(`{{ route('workers.preparePrint.loadClosedProjects', ['worker_id' => $worker->id]) }}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            const list = document.getElementById('project-radio-list');

                            data.closedProjects.forEach(project => {
                                const wrapper = document.createElement('div');
                                wrapper.className = 'form-check';
                                wrapper.innerHTML = `
                                    <input class="form-check-input" type="radio" name="project"
                                        id="project-${project.id}" value="${project.id}">
                                    <label class="form-check-label" for="project-${project.id}">
                                        ${project.title}
                                    </label>
                                `;
                                list.appendChild(wrapper);
                            });

                            // One-shot reveal: remove the button once closed projects are loaded.
                            loadClosedBtn.remove();
                        })
                        .catch(() => {
                            loadClosedBtn.disabled = false;
                            loadClosedBtn.textContent = '{{ __('form.load_older_projects') }}';
                        });
                });
            }

            const form = document.getElementById('prepare-print-form');
            const workTypeCheckboxes = document.querySelectorAll('.work-type-checkbox');
            const warning = document.getElementById('work-type-warning');

            // ---- Normalize month value to YYYY-MM before submit ----
            // Chrome/Edge (native month picker) already submit "YYYY-MM".
            // Firefox/Safari (plain text fallback, constrained by the
            // pattern attribute) submit "mm/yy". Rewrite the latter so the
            // backend only ever has to handle one format.
            form.addEventListener('submit', function(e) {
                const monthSelected = document.getElementById('timeframe-month').checked;
                if (!monthSelected) {
                    return;
                }

                const raw = monthInput.value.trim();
                const isoMatch = raw.match(/^(\d{4})-(0[1-9]|1[0-2])$/);
                const shortMatch = raw.match(/^(0[1-9]|1[0-2])\/(\d{2})$/);

                if (isoMatch) {
                    // Already in the target format, nothing to do.
                    return;
                }

                if (shortMatch) {
                    const mm = shortMatch[1];
                    const yy = shortMatch[2];
                    monthInput.value = `20${yy}-${mm}`;
                    return;
                }

                // Neither format matched (shouldn't happen if native/pattern
                // validation ran first, but guard anyway).
                e.preventDefault();
                monthInput.reportValidity();
            });


            // ---- Prevent submitting with zero work-type checkboxes selected ----
            if (workTypeCheckboxes.length) {
                form.addEventListener('submit', function(e) {
                    const anyChecked = Array.from(workTypeCheckboxes).some(cb => cb.checked);
                    if (!anyChecked) {
                        e.preventDefault();
                        warning.classList.remove('d-none');
                    }
                });

                workTypeCheckboxes.forEach(cb => cb.addEventListener('change', function() {
                    warning.classList.add('d-none');
                }));
            }


        })();
    </script>
</body>