@if (is_array($data['options']) && sizeof($data['options']) === 1)
  @php $data['value'] = $data['options'][0] @endphp
  <x-input :data="$data" />
@else
  @php $kebab = Str::kebab($data['name']) @endphp

  <div class="py-1">
    <label for="{{ $kebab }}">{{ $data['placeholder'] }}</label>
    <select
      class="form-select @if (Session::has($kebab)) is-invalid @enderror"
      {{-- biar gambar error dikanan bawahnya engga terlalu mepet --}}
      @if (Session::has($kebab)) style="background-position-x: calc(100% - 10.5px), calc(100% - 30px)" @enderror
      id="{{ $kebab }}"
      name="{{ $kebab }}"
    >
      <option @if (!$data['selected']) selected @endif hidden disabled aria-hidden>
        Pilih {{ Str::lower($data['placeholder']) }}
      </option>

      @if (is_array($data['options']))
        @foreach ($data['options'] as $option)
        <option @if ($data['selected'] === $option) selected @endif value="{{ $option }}">
          {{ $option }}
        </option>
        @endforeach
      @else
        <option disabled aria-hidden>{{ $data['options'] }}</option>
      @endif
    </select>

    <span class="form-text text-danger">
      @if (Session::has($kebab))
      <strong>{{ Session::pull($kebab) }}</strong>
      @endif
      &nbsp;
    </span>
  </div>
@endif
