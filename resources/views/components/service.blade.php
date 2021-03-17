<div class="pt-4">
  <div class="services">
    <div class="card">
      <h3 class="card-header text-center">{{ $service }}</h3>

      <div class="card-body service-card">
        <h5 class="card-title d-flex flex-column align-items-center doctors-name">
          @foreach (array_keys($doctors) as $doctor)
          <span
            class="my-1 @if ($loop->first) bold @else text-muted @endif"
            data-schedule="{{ json_encode($doctors[$doctor]) }}"
          >
            Dr. {{ $doctor }}
          </span>
          @endforeach
        </h5>
      </div>

      <div class="card-footer d-grid">
        <a
          href="{{ route('patient-registration', ['layanan' => $service]) }}"
          class="btn btn-primary"
        >
          Ajukan permintaan datang
        </a>
      </div>
    </div>
  </div>
</div>
