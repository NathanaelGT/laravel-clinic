<x-app>
  <div class="mx-xl-2 mx-lg-4 mx-md-3 mx-2 mb-3 mt-5 d-flex justify-content-between">
    <h3>Tabel daftar kunjungan yang bermasalah</h3>
  </div>

  <div class="table-responsive mx-xl-2 mx-lg-4 mx-md-3 m-2 d-flex justify-content-center">
    <table class="table table-bordered">
      <thead class="text-center align-middle">
        <tr>
          <th scope="col" class="col-2" rowspan="3">Dokter</th>
          <th scope="col" class="col-4" colspan="2">Kunjungan</th>
          <th scope="col" class="col-2" rowspan="3">Pasien</th>
          <th scope="col" class="col-2" rowspan="3">No HP</th>
          <th scope="col" class="col-2" rowspan="3">Aksi</th>
        </tr>
        <tr>
          <th scope="col" class="col-2">Tanggal</th>
          <th scope="col" class="col-1">Jam</th>
        </tr>
      </thead>
      <tbody class="text-center align-middle">
        @forelse ($appointmentHistories as $appointment)
          <tr>
            <td>{{ $appointment->doctor }}</td>
            <td>{{ $appointment->date->isoFormat('dddd, D MMMM YYYY') }}</td>
            <td>{{ "{$appointment->time_start} - {$appointment->time_end}" }}</td>
            <td>{{ $appointment->patient_name }}</td>
            <td>{{ $appointment->patient_phone_number }}</td>
            <td>
              <a href="{{ route('admin@patient-cancel', $appointment->id) }}" class="btn btn-danger">
                Batal
              </a>
              <a
                href="{{ route('admin@patient-reschedule', $appointment->id) }}"
                class="btn btn-warning text-white"
              >
                Reschedule
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td class="py-3" colspan="9">Tidak ada jadwal yang bermasalah</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</x-app>
