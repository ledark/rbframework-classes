@php
    $id = $id ?? uniqid('input_date_');
    $value = $value ?? '1';
    $label = $label ?? $id;
    if(isset($checked) and is_array($checked)) {
      $checked = in_array($value, $checked) ? 'checked' : '';
    } else
    if(isset($checked) and is_string($checked)) {
      $checked = $checked == $value ? 'checked' : '';
    } else {
      $checked = $checked ?? false;
      $checked = $checked ? 'checked' : '';
    }
    $class = $class ?? 'form-check';
@endphp

<div class="form-check">
  <input class="form-check-input" type="checkbox" value="{{$value}}" id="{{$id}}" {{$checked}}/>
  <label class="form-check-label" for="{{$id}}">{{$label}}</label>
</div>