@php
  use Illuminate\Support\Str;

  $today = \Carbon\Carbon::now()->dayName;
@endphp

<x-app>
  <div class="table-responsive my-xl-5 m-lg-4 m-md-3 m-2 d-flex justify-content-center">
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
        @php $id = 0 @endphp
        @php $worktimesId = 0 @endphp
        @foreach ($services as $service)
        @foreach ($doctors[$loop->index] as $doctor)
        @php $_loop = $loop @endphp
        <tr>
          @if ($loop->first)
          <td rowspan="{{ sizeof($doctors[$loop->parent->index]) }}">{{ $service }}</td>
          @endif
          <td class="editable" data-type="name" data-id="{{ ++$id }}">{{ $doctor }}</td>
          <td class="doctor-schedule">
            <div class="row">
              @foreach ([['Senin', 'Selasa', 'Rabu'], ['Kamis', 'Jumat', 'Sabtu']] as $days)
              <div class="col-xl-6 col-12 overflow-auto">
                @foreach ($days as $day)
                <div class="schedule d-flex">
                  <span class="day @if ($today === $day) bold @endif">{{ $day }}:</span>
                  <span>
                    @foreach ($schedules[$_loop->parent->index][$_loop->index][$day] ?? ['Tutup'] as $schedule)
                    <span class="one-line">
                      @if ($schedule === 'Tutup')
                      <span>{{ $schedule }}</span>
                      @else
                      <span
                        class="editable"
                        data-type="time"
                        data-id="{{ ++$worktimesId }}"
                      >{{ $schedule }}</span>
                      (per
                      <span
                        class="editable"
                        data-type="per"
                        data-id="{{ $worktimesId }}"
                      >{{ $slot[$_loop->parent->index][$_loop->index][$day][$loop->index] }} menit</span>
                      ){{  !$loop->last ? ', ' : ''  }}
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
            <a href="#" class="btn btn-danger w-100">Hapus</a>
          </td>
        </tr>
        @endforeach
        @endforeach
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