<x-app>
  <div class="table-responsive my-xl-5 m-lg-4 m-md-3 m-2 d-flex justify-content-center">
    <table class="table table-bordered">
      <thead class="text-center align-middle">
        <tr>
          <th scope="col" class="col-2" rowspan="3">Tanggal</th>
          <th scope="col" class="col-5" colspan="5">Data</th>
          <th scope="col" class="col-2" rowspan="3">Nama Pasien</th>
          <th scope="col" class="col-3" rowspan="3" colspan="2">Aksi</th>
        </tr>
        <tr>
          <th scope="col" class="col-1" rowspan="2">Nama Dokter</th>
          <th scope="col" class="col-2" colspan="2">Tersimpan</th>
          <th scope="col" class="col-2" colspan="2">Draft</th>
        </tr>
        <tr>
          <th scope="col" class="col-1">Jam</th>
          <th scope="col" class="col-1">Kuota</th>
          <th scope="col" class="col-1">Jam</th>
          <th scope="col" class="col-1">Kuota</th>
        </tr>
      </thead>
      <tbody class="text-center align-middle">
        @forelse ($conflicts as $index => $conflict)
        @foreach ($conflict['serviceAppointment']['patientAppointment'] as $patientAppointment)
        @if ($loop->first)
        <tr>
          <td rowspan="{{ $loop->count }}">
            {{ \Carbon\Carbon::parse($conflict['serviceAppointment']['date'])->isoFormat('dddd, D MMMM YYYY') }}
          </td>

          <td rowspan="{{ $loop->count }}">
            {{ $conflict['doctorWorktime']['doctorService']['doctor_name'] }}
          </td>

          <td rowspan="{{ $loop->count }}">
            {{ "{$conflict['doctorWorktime']['time_start']} - {$conflict['doctorWorktime']['time_end']}" }}
          </td rowspan="{{ $loop->count }}">

          <td rowspan="{{ $loop->count }}">
            {{ $conflict['doctorWorktime']['quota'] }}
          </td>

          <td rowspan="{{ $loop->count }}">
            {{ $conflict['time_start'] . ' - ' . $conflict['time_end'] }}
          </td rowspan="{{ $loop->count }}">

          <td rowspan="{{ $loop->count }}">
            {{ $conflict['quota'] }}
          </td>

          <td>
            {{ $patientAppointment['patient']['name'] }}
          </td>

          <td>
            <a
              href="{{ route('admin@patient-reschedule', ['patientAppointment' => $patientAppointment['id']]) }}"
              class="btn btn-warning text-white"
            >
              Ubah
            </a>
          </td>

          <td rowspan="{{ $loop->count }}">
            <a
              href="{{ route('admin@conflict-cancel', ['conflict' => $conflict['id']]) }}"
              class="btn btn-danger"
            >
              Batalkan perubahan
            </a>
            <a
              href="{{ route('admin@conflict-nextweek', ['conflict' => $conflict['id']]) }}"
              class="btn btn-primary my-2"
            >
              Berlakukan mulai minggu selanjutnya
            </a>
            @php
              $url = '';
              if (!in_array('-1', $conflict['serviceAppointment']['quota'])) {
                $url = route(
                  'admin@conflict-close',
                  ['serviceAppointment' => $conflict['serviceAppointment']['id']]
                );
              }
            @endphp
            @if ($url)
            <a href="{{ $url }}" class="btn btn-primary">
              Jangan izinkan untuk mendaftar lagi
            </a>
            @else
            <button disabled aria-disabled class="btn btn-primary">
              Jangan izinkan untuk mendaftar lagi
            </button>
            @endif
          </td>
        </tr>
        @else
        <tr>
          <td>{{ $patientAppointment['patient']['name'] }}</td>
          <td>
            <a
              href="{{ route('admin@patient-reschedule', ['patientAppointment' => $patientAppointment['id']]) }}"
              class="btn btn-warning text-white"
            >
              Ubah
            </a>
          </td>
        </tr>
        @endif
        @endforeach
        @empty
        <tr>
          <td class="py-3" colspan="9">Tidak ada jadwal yang mempunyai masalah</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <x-slot name="script">
    <script>
      (table => {
        const data = {}
        table.querySelectorAll('tr').forEach(tr => {
          const element = tr.firstElementChild
          const signature = element.innerHTML + tr.classList

          if (data?.signature === signature) {
            const rowSpan = Number(element.rowSpan) || 1
            data.element.rowSpan += rowSpan
            element.classList.add('rowspan-remove')
          }
          else {
            data.signature = signature
            data.element = element
          }
        })

        table.querySelectorAll('.rowspan-remove').forEach(element => {
          element.remove()
        })
      })(document.querySelector('table'))
    </script>
  </x-slot>
</x-app>