<x-app>
  <div class="table-responsive m-5">
    <table class="table table-bordered table-patient-list">
      <thead>
        <tr class="text-center">
          <th scope="col" class="col-1">Nama Layanan</th>
          <th scope="col" class="col-3">Nama Dokter</th>
          <th scope="col" class="col-3">Nama Pasien</th>
          <th scope="col" class="col-1">Hari</th>
          <th scope="col" class="col-1">Jam</th>
          <th scope="col" class="col-2">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach ([
          [
            'service' => 'Gigi',
            'doctor' => 'Budi',
            'patient' => 'Otto',
            'day' => 'Senin',
            'time' => '15:00 - 16:00'
          ],
          [
            'service' => 'Gigi',
            'doctor' => 'Budi',
            'patient' => 'Thornton',
            'day' => 'Rabu',
            'time' => '10:00 - 11:00'
          ],
          [
            'service' => 'Mata',
            'doctor' => 'John',
            'patient' => 'Ray',
            'day' => 'Sabtu',
            'time' => '16:00 - 17:00'
          ],
        ] as ['service' => $service, 'doctor' => $doctor, 'patient' => $patient, 'day' => $day, 'time' => $time])
        <tr>
          <td>{{ $service }}</td>
          <td>{{ $doctor }}</td>
          <td>{{ $patient }}</td>
          <td>{{ $day }}</td>
          <td>{{ $time }}</td>
          <td>
            <a href="#" type="button" class="btn btn-danger">Batalkan</a>
            <a href="#" type="button" class="btn btn-warning text-white">Reschedule</a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</x-app>