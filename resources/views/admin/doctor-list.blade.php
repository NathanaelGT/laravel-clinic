<x-app>
  <div class="mx-xl-2 mx-lg-4 mx-md-3 mx-2 mb-3 mt-5 d-flex justify-content-between">
    <h3>Tabel daftar dokter</h3>
    <div>
      <a class="btn btn-primary" href="{{ route('admin@new-service') }}">Tambahkan layanan baru</a>
      <a
        id="conflict-button"
        class="btn btn-primary @if (!$hasConflict) d-none @endif"
        href="{{ route('admin@conflict') }}"
      >
        Lihat jadwal layanan yang "bermasalah"
      </a>
    </div>
  </div>

  <div class="table-responsive mx-xl-2 mx-lg-4 mx-md-3 m-2 d-flex justify-content-center">
    <table class="table table-bordered table-doctor-list">
      <thead>
        <tr class="text-center">
          <th scope="col" class="col-1">Nama Layanan</th>
          <th scope="col" class="col-2">Nama Dokter</th>
          <th scope="col" class="col-lg-7 col-6">Jadwal</th>
          <th scope="col" class="col-lg-1 col-2">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($data as $service => $doctors)
          @foreach ($doctors as $doctor => $schedules)
            @if ($loop->first && sizeof($doctors) > 1)
              <td rowspan="{{ sizeof($doctors) + 1 }}" data-drag="{{ $ids[$service] }}">{{ $service }}</td>
            @endif
            <tr @if (sizeof($doctors) > 1) data-drag-target="{{ $ids[$service] }}" @endif>
              @if ($loop->first && sizeof($doctors) === 1)
                <td data-drag="{{ $ids[$service] }}">{{ $service }}</td>
              @endif
              <td class="editable p-0" data-type="name" data-id="{{ $ids["$service.$doctor"] }}">{{ $doctor }}</td>
              <td class="doctor-schedule">
                <div class="row">
                  @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $day)
                    <x-schedule :schedules="$schedules" :day="$day" />
                  @endforeach
                </div>
              </td>
              <td>
                <form
                  class="delete-service"
                  action="{{ route('admin@delete-service', $ids["$service.$doctor"]) }}"
                  method="POST"
                >
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-danger w-100">Hapus</button>
                </form>
              </td>
            </tr>
          @endforeach
        @empty
          <tr>
            <td class="py-3" colspan="4">Tidak dapat menemukan data</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if ($data)
  <x-slot name="script">
    <script src="{{ mix('js/admin/doctorList.js') }}"></script>
  </x-slot>
  @endif
</x-app>
