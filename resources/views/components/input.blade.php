@php
  $kebab = Str::kebab($data['name']);
@endphp
<div class="py-1">
  <label for="{{ $kebab }}">{{ $data['placeholder'] }}</label>
  <input
    id="{{ $kebab }}"
    name="{{ $kebab }}"
    @if (isset($data['type'])) data-type="{{ $data['type'] }}" @endif
    @if (isset($data['max'])) maxlength="{{ $data['max'] }}" @endif
    placeholder="Masukkan {{ Str::lower($data['placeholder']) }}"
    class="form-control @error($kebab) is-invalid @enderror"
    autocomplete="off"
    @if (isset($data['value']) && $data['value'])
    value="{{ $data['value'] }}"
    disabled
    @endif
    required
  />
  <span class="form-text text-danger">
    <strong>
      {!! Session::get($kebab) ?? '&nbsp;' !!}
    </strong>
  </span>
</div>