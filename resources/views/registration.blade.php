<x-app>
  <div class="row">
    <div class="col-lg-6 offset-lg-3 col-md-8 offset-md-2 col-sm-10 offset-sm-1">
      <form action="" method="post">
        @foreach ([
          ['placeholder' => 'Layanan', 'name' => 'service', 'value' => $service],
          ['placeholder' => 'Nama',    'name' => 'name'],
          ['placeholder' => 'NIK',     'name' => 'nik'],
          ['placeholder' => 'No. HP',  'name' => 'phone-number'],
          ['placeholder' => 'Alamat',  'name' => 'address']
        ] as $data)
        <x-input :data="$data" />
        @endforeach

        @foreach ([
          [
            'placeholder' => 'Nama Dokter',
            'name' => 'doctor',
            'options' => $doctors
          ],
          [
            'placeholder' => 'Hari Praktek',
            'name' => 'day',
            'options' => sizeof($doctors) > 1
              ? 'Harap pilih doktek terlebih dahulu'
              : array_keys($workingSchedules[0])
          ],
          [
            'placeholder' => 'Jam Praktek',
            'name' => 'time',
            'options' => 'Harap pilih hari praktek terlebih dahulu'
          ],
        ] as $data)
        <x-input-choice :data="$data" />
        @endforeach

        <div class="d-grid mb-5">
          <button type="submit" class="btn btn-primary">Daftar</button>
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