<x-app>
  <div class="mx-xl-2 mx-lg-4 mx-md-3 mx-2 mb-3 mt-5 d-flex justify-content-between">
    <h3>Tabel daftar kunjungan</h3>
  </div>

  <div class="table-responsive mx-xl-2 mx-lg-4 mx-md-3 m-2 d-flex justify-content-center">
    <table class="table table-bordered table-patient-list">
      <thead>
        <tr class="text-center">
          <th scope="col" class="col-1">Layanan</th>
          <th scope="col" class="col-2">Dokter</th>
          <th scope="col" class="col-2">Pasien</th>
          <th scope="col" class="col-2">Tanggal</th>
          <th scope="col" class="col-2">Jam</th>
          <th scope="col" class="col-2">Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($appointments as [
          'id'            => $id,
          'date'          => $date,
          'doctorService' => $doctorService,
          'patient'       => $patient,
          'time_start'    => $timeStart,
          'time_end'      => $timeEnd,
          'status'        => $status
        ])
          <tr>
            <td>{{ $doctorService->service->name }}</td>
            <td>{{ $doctorService->doctor_name }}</td>
            <td>{{ $patient->name }}</td>
            <td>{{ $date->isoFormat('dddd, D MMMM YYYY') }}</td>
            <td>{{ "$timeStart - $timeEnd" }}</td>
            <td>{{ $status }}</td>
          </tr>
        @empty
          <tr>
            <td class="py-3" colspan="6">Data pasien tidak dapat ditemukan</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="d-flex justify-content-center">
    {{ $appointments->links() }}
  </div>
</x-app>
