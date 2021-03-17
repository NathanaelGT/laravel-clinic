<span
  @if ($title) title="{{ $title }}" @endif
  class="one-line {{ $className }}"
>
  @if ($isClose)
    <span>Tutup</span>
  @else
    <span
      @if ($timeClassName) class="{{ $timeClassName }}" @endif
      @if ($timeTitle) title="{{ $timeTitle }}" @endif
      data-type="time" 
      data-id="{{ $id }}"
    >
      {{ $timeText }}</span>
    (per
    <span
      @if ($quotaClassName) class="{{ $quotaClassName }}" @endif
      data-type="per"
      data-id="{{ $id }}"
    >{{ $quotaText }}</span>)@endif</span>
{{-- sengaja ga dirapiin, karna bakal ada whitespace kalo dirapiin --}}
{{-- dan bakal ngefek ke tampilan dan javascriptnya --}}
