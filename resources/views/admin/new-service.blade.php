<x-app>
  <div class="row new-service">
    <div class="col-lg-6 offset-lg-3 col-md-8 offset-md-2 col-sm-10 offset-sm-1 pt-4" id="form-container">
    </div>
  </div>

  <x-slot name="script">
    <script>
      window.services = {!! json_encode(config('global.services')) !!}
    </script>
    <script src="{{ mix('js/admin/newService.js') }}"></script>
  </x-slot>
</x-app>