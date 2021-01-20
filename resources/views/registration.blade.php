<x-app>
  <div class="row">
    <div class="col-lg-6 offset-lg-3 col-md-8 offset-md-2 col-sm-10 offset-sm-1 full-height">
      <noscript>
        <div class="d-flex justify-content-center align-items-center h-100">
          Izinkan javascript untuk melihat konten pada halaman ini
        </div>
      </noscript>

      <form action="{{ $formAction }}" method="post" class="d-none">
        @method($formMethod)
        @csrf
        @if (isset($id))
        <input type="hidden" name="id" value="{{ $id }}" />
        @endif

        @foreach ([
          [
            'placeholder' => 'Layanan',
            'name' => 'service',
            'value' => $service
          ],
          [
            'placeholder' => 'Nama',
            'name' => 'name',
            'value' => $patient->name ?? ''
          ],
          [
            'placeholder' => 'NIK',
            'name' => 'nik',
            'value' => $patient->nik ?? '',
            'type' => 'number'
          ],
          [
            'placeholder' => 'No. HP',
            'name' => 'phone-number',
            'value' => $patient->phone_number ?? '',
            'type' => 'number'
          ],
          [
            'placeholder' => 'Alamat',
            'name' => 'address',
            'value' => $patient->address ?? ''
          ]
        ] as $data)
        <x-input :data="$data" />
        @endforeach

        @foreach ([
          [
            'placeholder' => 'Nama Dokter',
            'name' => 'doctor',
            'options' => $doctors,
            'selected' => $patient->doctor ?? ''
          ],
          [
            'placeholder' => 'Hari Praktek',
            'name' => 'day',
            'options' => isset($patient->doctor) ? $availableDays : (sizeof($doctors) > 1
              ? 'Harap pilih doktek terlebih dahulu'
              : array_keys($workingSchedules[0])),
            'selected' => isset($patient->doctor) ? $patient->time_start->isoFormat('dddd') : ''
          ],
          [
            'placeholder' => 'Jam Praktek',
            'name' => 'time',
            'options' => 'Harap pilih hari praktek terlebih dahulu',
            'selected' => ''
          ],
        ] as $data)
        <x-input-choice :data="$data" />
        @endforeach

        <div class="d-grid mb-5">
          <button type="submit" class="btn btn-primary">
            {{ isset($patient->doctor) ? 'Ubah' : 'Daftar' }}
          </button>
        </div>
      </form>
    </div>
  </div>

  <x-slot name="script">
    <script>
      window.workingSchedules = {!! json_encode($workingSchedules) !!}
    </script>
    <script src="{{ mix('js/patientAppointment.js') }}"></script>
  </x-slot>
</x-app>