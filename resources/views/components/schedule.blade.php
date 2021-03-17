<div class="overflow-auto">
  <div class="schedule d-flex">
    <span class="day @if (now()->dayName === $day) bold @endif">{{ $day }}:</span>
    <span>
      @forelse ($schedules[$day] ?? [] as $schedule)
        @if ($schedule['replacedWith'] && $schedule['replacedWith']['quota'] === 0)
          @continue($loop->count > 1)
          <span class="one-line">
            <span
              class="text-warning"
              title="Sudah ada pasien yang mendaftar pada jadwal ini{{ "\n" }}Jadwal asli: {{ $schedule['time'] }}"
            >
              Tutup
            </span>
          </span>
        @else
          <x-active-schedule :schedule="$schedule" />
        @endif
      @empty
        <span class="one-line">
          <span>Tutup</span>
        </span>
      @endforelse
    </span>
  </div>
</div>
