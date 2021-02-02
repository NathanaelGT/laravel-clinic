@php
  use App\Helpers;
  use Carbon\Carbon;
@endphp
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
                href="#"
                class="btn btn-success @if ($date->isFuture()) invisible @endif"
                @if ($date->isFuture()) aria-hidden @endif
              >
                Selesai
              </a>
              <a href="#" class="btn btn-danger">Batal</a>
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
  </div>
</x-app>