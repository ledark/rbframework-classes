@php
    $name = $name ?? uniqid('input_text_');
    $value = $value ?? '';
    $label = $label ?? $name;
    $addon = $addon ?? '';
    $middle = $middle ?? '';
    $placeholder = $placeholder ?? '';
@endphp

<div id="div{{$name}}" class="form-floating mb-3">
    <div class="input-group mb-3">
    @if(!empty($addon))
        <span id="addon-{{$name}}" class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
    @endif
        <div class="form-floating flex-grow-1">
            <input type="text" class="form-control" id="{{$name}}" name="{{$name}}" value="{{$value}}" {{$middle}} placeholder="{{$placeholder}}"/>
            <label for="{{$name}}">{{$label}}</label>
        </div>
    </div>
</div>