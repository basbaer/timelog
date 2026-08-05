<nav class="navbar navbar-expand bg-body-tertiary">
    <div class="container-fluid">
        <div class="collapse navbar-collapse justify-content-center" id="navbarNavDropdown">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link {{ ($active ?? '') === 'projects' ? 'active' : '' }}" aria-current="page"
                        href="/admin/projects">
                        Projekte
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ ($active ?? '') === 'workers' ? 'active' : '' }}" href="/admin/workers">
                        Mitarbeiter
                    </a>
                </li>
            </ul>
        </div>
        <!-- Logout Button -->
        <div class="d-flex justify-content-end">
            <a href="/logout" class="btn btn-outline-danger">Logout</a>
        </div>
    </div>
</nav>
