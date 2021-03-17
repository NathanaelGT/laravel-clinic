@php
  $kebab = Str::kebab($data['name']);
@endphp
<div class="py-1">
  <label for="{{ $kebab }}">{{ $data['placeholder'] }}</label>
  <input
    id="{{ $kebab }}"
    name="{{ $kebab }}"
    @isset($data['type']) type="{{ $data['type'] }}" @endisset
    @isset($data['max']) maxlength="{{ $data['max'] }}" @endisset
    @isset($data['data-type']) data-type="{{ $data['data-type'] }}" @endisset
    placeholder="Masukkan {{ Str::lower($data['placeholder']) }}"
    class="form-control @if (Session::has($kebab)) is-invalid @enderror"
    autocomplete="off"
    @isset($data['pattern']) pattern="{{ $data['pattern'] }}" @endisset
    @if (isset($data['value']) && $data['value'])
    value="{{ $data['value'] }}"
    disabled
    @endif
    required
  />
  <span class="form-text text-danger">
    @if (Session::has($kebab))
    <strong>{{ Session::pull($kebab) }}</strong>
    @endif
    &nbsp;
  </span>
</div>
