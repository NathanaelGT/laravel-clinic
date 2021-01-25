<x-app>
  <div class="row new-service">
    <div class="col-lg-6 offset-lg-3 col-md-8 offset-md-2 col-sm-10 offset-sm-1 pt-4" id="form-container">
      <noscript>
        <div class="d-flex justify-content-center align-items-center h-100">
          Izinkan javascript untuk melihat konten pada halaman ini
        </div>
      </noscript>
    </div>
  </div>

  <x-slot name="script">
    <script>
      window.services = {!! json_encode($services) !!}
    </script>
    <script src="{{ mix('js/admin/newService.js') }}"></script>
  </x-slot>
</x-app>