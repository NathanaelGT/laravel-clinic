@php
  use Illuminate\Support\Str;
  use App\Helpers;

  $today = \Carbon\Carbon::now()->dayName;
@endphp

<x-app>
  <div class="mx-xl-2 mx-lg-4 mx-md-3 mx-2 mb-3 mt-5 d-flex justify-content-between">
    <h3>Tabel daftar dokter</h3>
    <div>
      <a class="btn btn-primary" href="{{ route('admin@new-service') }}">Tambahkan layanan baru</a>
      <a
        id="conflict-button"
        class="btn btn-primary @if (!$hasConflict) d-none @endif"
        href="{{ route('admin@conflict') }}"
      >
        Lihat jadwal layanan yang "bermasalah"
      </a>
    </div>
  </div>

  <div class="table-responsive mx-xl-2 mx-lg-4 mx-md-3 m-2 d-flex justify-content-center">
    <table class="table table-bordered table-doctor-list">
      <thead>
        <tr class="text-center">
          <th scope="col" class="col-1">Nama Layanan</th>
          <th scope="col" class="col-1">Nama Dokter</th>
          <th scope="col" class="col-lg-8 col-7">Jadwal</th>
          <th scope="col" class="col-lg-1 col-2">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($data as $service => $doctors)
        @foreach ($doctors as $doctor => $schedules)
        @if ($loop->first && sizeof($doctors) > 1)
        <td rowspan="{{ sizeof($doctors) + 1 }}" data-drag="{{ $ids[$service] }}">{{ $service }}</td>
        @endif
        <tr @if (sizeof($doctors) > 1) data-drag-target="{{ $ids[$service] }}" @endif>
          @if ($loop->first && sizeof($doctors) === 1)
          <td data-drag="{{ $ids[$service] }}">{{ $service }}</td>
          @endif
          <td class="editable" data-type="name" data-id="{{ $ids["$service.$doctor"] }}">{{ $doctor }}</td>
          <td class="doctor-schedule">
            <div class="row">
              @foreach ([['Senin', 'Selasa', 'Rabu'], ['Kamis', 'Jumat', 'Sabtu']] as $days)
              <div class="col-xl-6 col-12 overflow-auto">
                @foreach ($days as $day)
                <div class="schedule d-flex">
                  <span class="day @if ($today === $day) bold @endif">{{ $day }}:</span>
                  <span>
                    @foreach ($schedules[$day] ?? ['Tutup'] as $schedule)
                    @if ($schedule === 'Tutup')
                    <span class="one-line">
                      <span>{{ $schedule }}</span>
                    </span>
                    @else
                    <span
                    title="
                      @if ($schedule['activeDate']->isFuture())
                        Jadwal ini akan berlaku mulai {{ $schedule['activeDate']->isoFormat('dddd, DD MMMM YYYY') }}
                      @elseif (!is_null($schedule['deletedAt']))
                        Jadwal ini akan terhapus pada {{ \Carbon\Carbon::parse($schedule['deletedAt'])->isoFormat('dddd, DD MMMM YYYY') }}
                      @endif
                      "
                      class="one-line
                        @if ($schedule['activeDate']->isFuture())
                          text-decoration-underline
                        @elseif (!is_null($schedule['deletedAt']))
                          text-decoration-line-through grey
                        @endif
                      "
                    >
                      <span
                        class="@if (is_null($schedule['deletedAt'])) editable @endif"
                        data-type="time"
                        data-id="{{ $schedule['id'] }}"
                      >{{ $schedule['time'] }}</span>
                      (per
                      <span
                        class="@if (is_null($schedule['deletedAt'])) editable @endif"
                        data-type="per"
                        data-id="{{ $schedule['id'] }}"
                      >{{ Helpers::formatSlotTime($schedule['quota'], $schedule['time']) }}</span>){{ !$loop->last ? ', ' : '' }}
                      @endif
                    </span>
                    @endforeach
                  </span>
                </div>
                @endforeach
              </div>
              @endforeach
            </div>
          </td>
          <td>
            <form
              class="delete-service"
              action="{{ route('admin@delete-service', $ids["$service.$doctor"]) }}"
              method="POST"
            >
              @csrf
              @method('DELETE')
              <button class="btn btn-danger w-100">Hapus</button>
            </form>
          </td>
        </tr>
        @endforeach
        @empty
        <tr>
          <td class="py-3" colspan="4">Tidak dapat menemukan data</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <x-slot name="script">
    <script>
      window.plusUrl = '{{ asset("svg/plus.svg") }}'
      window.minusUrl = '{{ asset("svg/minus.svg") }}'
    </script>
    <script src="{{ mix('js/admin/doctorList.js') }}"></script>
  </x-slot>
</x-app>
