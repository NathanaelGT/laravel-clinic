@php
  $kebab = Str::kebab($data['name']);
@endphp
<div class="py-1">
  <label for="{{ $kebab }}">{{ $data['placeholder'] }}</label>
  <input
    id="{{ $kebab }}"
    name="{{ $kebab }}"
    type="date"
    placeholder="Masukkan {{ Str::lower($data['placeholder']) }}"
    class="form-control"
    autocomplete="off"
    min="{{ $data['min'] ?? '' }}"
    max="{{ $data['max'] ?? '' }}"
    required
  />
  <div class="form-text text-danger">&nbsp;</div>
</div>