<nav class="navbar navbar-expand-md sticky-top navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand ms-sm-5" href="{{ route('home') }}">Klinik Foo Bar</a>

    @auth
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav ms-auto me-5">
        @foreach ([
          ['route' => route('admin@patient-list'), 'name' => 'Daftar Pasien'],
          ['route' => route('admin@doctor-list'), 'name' => 'Daftar Dokter'],
          ['route' => route('logout'), 'name' => 'Keluar']
        ] as $nav)
        <li class="nav-item">
          <a
            class="nav-link @if (URL::current() === $nav['route']) active @endif"
            @if (URL::current() === $nav['route'])
            aria-current="page"
            @endif
            href="{{ $nav['route'] }}"
          >
            {{ $nav['name'] }}
          </a>
        </li>
        @endforeach
      </ul>
    </div>
    @endauth
  </div>
</nav>