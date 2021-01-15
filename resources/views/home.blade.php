<x-app>
  <div class="row">
    <div class="col-md-10 offset-md-1 my-5">
      <h1>Klinik Foo Bar</h1>
      <h3>Lorem ipsum dolor sit amet consectetur adipisicing elit. Vero quae sint laudantium ducimus maiores facere possimus perspiciatis odit aliquid qui. Sapiente voluptatibus vitae facere delectus nihil odit necessitatibus praesentium fugiat.</h3>
    </div>
    <div class="col-md-1"></div>

    <div class="col-md-10 offset-md-1 px-0">
      <h4 class="ps-3">Layanan yang tersedia:</h4>
      <div class="row">

        @php $index = 0 @endphp
        @foreach ($services as $servicesSection)
        <div class="col-xl-4 offset-xl-0 col-md-10 offset-md-1">
          @foreach ($servicesSection as $service)
          <x-service :service="$service" :doctors="$doctors[$index]" />
          @php $index++ @endphp
          @endforeach
        </div>
        @endforeach

      </div>
    </div>

  </div>

  <x-slot name="script">
    <script>
      window.doctors = {!! json_encode($doctors) !!}
      window.workingSchedules = {!! json_encode($workingSchedules) !!}
    </script>
    <script src="{{ mix('js/home.js') }}"></script>
  </x-slot>
</x-app>