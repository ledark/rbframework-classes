@php
    $id = $id ?? uniqid('input_date_');
    $name = $name ?? $id;
    $label = $label ?? $id;
    $options = $options ?? [];
    $selected = $selected ?? array_keys($options)[0];
    $class = $class ?? 'form-group';
@endphp

<div class="{{$class}}">
    @if($class == 'form-group')
    <label for="{{$id}}">{{$label}}</label>
    @endif
    <select class="form-control" id="{{$id}}" name="{{$name}}">
        @foreach($options as $key => $value)
            <option value="{{$key}}" {{$selected == $key ? 'selected' : ''}}>{{$value}}</option>
        @endforeach
    </select>
    @if(strpos($class, 'form-floating') !== false)
    <label for="{{$id}}">{{$label}}</label>
    @endif
</div>