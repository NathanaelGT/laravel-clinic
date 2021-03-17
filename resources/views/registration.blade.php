<x-app>
  <div class="row">
    <div class="col-lg-6 offset-lg-3 col-md-8 offset-md-2 col-sm-10 offset-sm-1 full-height">
      <noscript>
        <div class="d-flex justify-content-center align-items-center h-100">
          Izinkan javascript untuk melihat konten pada halaman ini
        </div>
      </noscript>

      <form action="{{ $formAction }}?layanan={{ $service }}" method="post" class="d-none">
        @method($formMethod)
        @csrf

        @foreach ([
          [
            'placeholder' => 'Layanan',
            'name' => 'service',
            'value' => $service
          ],
          [
            'placeholder' => 'Nama',
            'name' => 'name',
            'value' => $patient['name'] ?? '',
            'max' => 255
          ],
          [
            'placeholder' => 'NIK',
            'name' => 'nik',
            'value' => $patient['nik'] ?? '',
            'data-type' => 'number',
            'max' => 15
          ],
          [
            'placeholder' => 'No. HP',
            'name' => 'phone-number',
            'value' => $patient['phone_number'] ?? '',
            'type' => 'tel',
            'data-type' => 'phone-number',
            'pattern' => '[0-9]{3,5}[-\s\.]?[0-9]{3,5}[-\s\.]?[0-9]{3,5}'
          ],
          [
            'placeholder' => 'Alamat',
            'name' => 'address',
            'value' => $patient['address'] ?? '',
            'max' => 255
          ]
        ] as $data)
        <x-input :data="$data" />
        @endforeach

        @foreach ([
          [
            'placeholder' => 'Nama Dokter',
            'name' => 'doctor',
            'options' => $doctors,
            'selected' => $patient['doctor'] ?? ''
          ],
          [
            'placeholder' => 'Tanggal Praktek',
            'name' => 'date',
            'options' => 'Harap pilih doktek terlebih dahulu',
            'selected' => ''
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
            {{ isset($patient['doctor']) ? 'Ubah' : 'Daftar' }}
          </button>
        </div>
      </form>
    </div>
  </div>

  <x-slot name="script">
    <script>
      window.schedules = @json($schedules);
      @isset($selected)
      window.selected = @json($selected);
      @endisset
    </script>
    <script src="{{ mix('js/patientAppointment.js') }}"></script>
  </x-slot>
</x-app>
