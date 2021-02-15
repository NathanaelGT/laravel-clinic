@php
  use App\Helpers;
  use Carbon\Carbon;
@endphp
<x-app>
  <div class="mx-xl-2 mx-lg-4 mx-md-3 mx-2 mb-3 mt-5 d-flex justify-content-between">
    <h3>Tabel daftar pasien</h3>
  </div>

  <div class="table-responsive mx-xl-2 mx-lg-4 mx-md-3 m-2 d-flex justify-content-center">
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
        @forelse ($patients as $patient)
        @php
          $doctorWorktime = $patient['service_appointment']['doctor_worktime'];
          $doctor = $doctorWorktime['doctor_service'];
          [$timeStart, $timeEnd] = Helpers::getPatientMeetHour($doctorWorktime, $patient);

          $date = Carbon::parse($patient['service_appointment']['date'] . ' ' . $timeEnd);
        @endphp
        <tr @if ($date->isPast()) class="bg-light" @endif>
          <td>{{ $doctor['service']['name'] }}</td>
          <td>{{ $doctor['doctor_name'] }}</td>
          <td>{{ $patient['patient']['name'] }}</td>
          <td>{{ $date->isoFormat('dddd, D MMMM YYYY') }}</td>
          <td>{{ "$timeStart - $timeEnd" }}</td>
          <td>
            <a
              href="{{ route('admin@patient-done', ['patientAppointment' => $patient['id']]) }}"
              class="btn btn-success @if ($date->isFuture()) invisible @endif"
              @if ($date->isFuture()) aria-hidden @endif
            >
              Selesai
            </a>

            <a
              href="{{ route('admin@patient-cancel', ['patientAppointment' => $patient['id']]) }}"
              class="btn btn-danger"
            >
              Batal
            </a>

            <a
              href="{{ route('admin@patient-reschedule', ['patientAppointment' => $patient['id']]) }}"
              class="btn btn-warning text-white"
            >
              Ubah
            </a>
          </td>
        </tr>
        @empty
        <tr>
          <td class="py-3" colspan="6">Data pasien tidak dapat ditemukan</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</x-app>
