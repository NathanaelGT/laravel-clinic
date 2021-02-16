<x-app>
  <div class="mx-xl-2 mx-lg-4 mx-md-3 mx-2 mb-3 mt-5 d-flex justify-content-between">
    <h3>Tabel daftar jadwal yang bermasalah</h3>
  </div>

  <div class="table-responsive mx-xl-2 mx-lg-4 mx-md-3 m-2 d-flex justify-content-center">
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
        @foreach ($conflict->serviceAppointment->patientAppointment as $patientAppointment)
        @if ($loop->first)
        <tr>
          @foreach ([
            \Carbon\Carbon::parse($conflict->serviceAppointment->date)->isoFormat('dddd, D MMMM YYYY'),
            $conflict->doctorWorktime->doctorService->doctor_name,
            $conflict->doctorWorktime->time_start . ' - ' . $conflict->doctorWorktime->time_end,
            $conflict->doctorWorktime->quota,
            $conflict->time_start . ' - ' . $conflict->time_end,
            $conflict->quota
          ] as $text)
          <td rowspan="{{ $loop->count }}">{{ $text }}</td>
          @endforeach
          <td>{{ $patientAppointment->patient->name }}</td>

          <td>
            <a
              href="{{ route('admin@patient-reschedule', ['patientAppointment' => $patientAppointment->id]) }}"
              class="btn btn-warning text-white"
            >
              Ubah
            </a>
          </td>

          <td rowspan="{{ $loop->count }}">
            <a
              href="{{ route('admin@conflict-cancel', ['conflict' => $conflict->id]) }}"
              class="btn btn-danger"
            >
              Batalkan perubahan
            </a>
            <a
              href="{{ route('admin@conflict-nextweek', ['conflict' => $conflict->id]) }}"
              class="btn btn-primary my-2"
            >
              Berlakukan mulai minggu selanjutnya
            </a>
            @php
              $url = '';
              if (in_array(0, $conflict->serviceAppointment->quota)) {
                $url = route('admin@conflict-close', ['serviceAppointment' => $conflict->serviceAppointment->id]);
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
          <td>{{ $patientAppointment->patient->name }}</td>
          <td>
            <a
              href="{{ route('admin@patient-reschedule', ['patientAppointment' => $patientAppointment->id]) }}"
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
</x-app>
