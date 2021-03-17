<x-app>
  <div class="mx-xl-2 mx-lg-4 mx-md-3 mx-2 mb-3 mt-5 d-flex justify-content-between">
    <h3>Tabel daftar jadwal yang bermasalah</h3>
  </div>

  <div class="table-responsive mx-xl-2 mx-lg-4 mx-md-3 m-2 d-flex justify-content-center">
    <table class="table table-bordered">
      <thead class="text-center align-middle">
        <tr>
          <th scope="col" class="col-1" rowspan="3">Nama Dokter</th>
          <th scope="col" class="col-7" colspan="5">Data</th>
          <th scope="col" class="col-2" rowspan="3">Nama Pasien</th>
          <th scope="col" class="col-3" rowspan="3" colspan="2">Aksi</th>
        </tr>
        <tr>
          <th scope="col" class="col-2" rowspan="2">Tanggal</th>
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
        @forelse ($doctorWorktimes as $index => $doctorWorktime)
          @foreach ($doctorWorktime->appointmentHistory as $appointment)
            <tr>
              @if ($loop->first)
                @foreach ([
                  $doctorWorktime->doctorService->doctor_name,
                  $appointment->date->isoFormat('dddd, D MMMM YYYY'),
                  $doctorWorktime->time_start . ' - ' . $doctorWorktime->time_end,
                  $doctorWorktime->quota
                ] as $text)
                  <td rowspan="{{ $loop->parent->count }}">{{ $text }}</td>
                @endforeach

                @if ($doctorWorktime->replacedWith->quota)
                  @foreach ([
                    $doctorWorktime->replacedWith->time_start . ' - ' . $doctorWorktime->replacedWith->time_end,
                    $doctorWorktime->replacedWith->quota
                  ] as $text)
                  <td rowspan="{{ $loop->parent->count }}">{{ $text }}</td>
                  @endforeach
                @else
                  <td rowspan="{{ $loop->count }}" colspan="2">Tutup</td>
                @endif
              @endif

              <td>{{ $appointment->patient_name }}</td>
              <td>
                <a
                  href="{{ route('admin@patient-reschedule', ['appointmentHistory' => $appointment->id]) }}"
                  class="btn btn-warning text-white"
                >
                  Reschedule
                </a>
              </td>

              @if ($loop->first)
                <td rowspan="{{ $loop->count }}">
                  <a
                    href="{{ route('admin@conflict-cancel', ['doctorWorktime' => $doctorWorktime->id]) }}"
                    class="btn btn-danger"
                  >
                    Batalkan perubahan
                  </a>
                  <a
                    href="{{ route('admin@conflict-nextweek', ['doctorWorktime' => $doctorWorktime->id]) }}"
                    class="btn btn-primary my-2"
                  >
                    Berlakukan mulai minggu selanjutnya
                  </a>
                </td>
              @endif
            </tr>
          @endforeach
        @empty
          <tr>
            <td class="py-3" colspan="9">Tidak ada jadwal yang bermasalah</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</x-app>
