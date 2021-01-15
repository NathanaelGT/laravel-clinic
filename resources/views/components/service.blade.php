<div class="mt-3">
  <div class="services">
    <div class="card">
      <h3 class="card-header text-center">{{ $service }}</h3>

      <div class="card-body service-card">
        <h5 class="card-title d-flex justify-content-around doctors-name">
          @foreach ($doctors as $doctor)
          <span>Dr. {{ $doctor }}</span>
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