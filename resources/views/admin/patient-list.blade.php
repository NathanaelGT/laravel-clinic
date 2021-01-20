<x-app>
  <div class="d-flex justify-content-center">
    <div class="table-responsive my-xl-5 m-lg-4 m-md-3 m-2">
      <table class="table table-bordered table-patient-list">
        <thead>
          <tr class="text-center">
            <th scope="col" class="col-1">Nama Layanan</th>
            <th scope="col" class="col-2">Nama Dokter</th>
            <th scope="col" class="col-2">Nama Pasien</th>
            <th scope="col" class="col-2">Tanggal</th>
            <th scope="col" class="col-1">Jam</th>
            <th scope="col" class="col-2">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($patients as $patient)
          @php $time_end = (clone $patient->time_start)->addMinutes($patient->service_per) @endphp
          <tr @if ($time_end->isPast()) class="bg-light" @endif>
            <td>{{ $patient->service }}</td>
            <td>{{ $patient->doctor }}</td>
            <td>{{ $patient->name }}</td>
            <td>{{ $patient->time_start->isoFormat('dddd, D MMMM YYYY') }}</td>
            <td>{{ $patient->time_start->isoFormat('HH:mm') }} - {{ $time_end->isoFormat('HH:mm') }}</td>
            <td>
              <a
                href="#"
                class="btn btn-success @if ($time_end->isFuture()) invisible @endif"
                @if ($time_end->isFuture()) aria-hidden @endif
              >
                Selesai
              </a>
              <a href="#" class="btn btn-danger">Batal</a>
              <a
                href="{{ route('admin@patient-reschedule', ['id' => $patient->id]) }}"
                class="btn btn-warning text-white"
              >
                Ubah
              </a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</x-app>