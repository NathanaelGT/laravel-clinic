@php
  $kebab = Str::kebab($data['name']);
@endphp
<div class="py-1">
  <label for="{{ $kebab }}">{{ $data['placeholder'] }}</label>
  <input
    id="{{ $kebab }}"
    name="{{ $kebab }}"
    placeholder="Masukkan {{ Str::lower($data['placeholder']) }}"
    class="form-control"
    autocomplete="off"
    @if (isset($data['value']))
    value="{{ $data['value'] }}"
    disabled
    @endif
    required
  />
  <div class="form-text text-danger">&nbsp;</div>
</div>