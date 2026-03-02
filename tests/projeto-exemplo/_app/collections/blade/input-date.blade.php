@php
    $id = $id ?? uniqid('input_date_');
    $middle = $middle ?? '';
    $value = $value ?? '';
    $label = $label ?? $id;
@endphp

<div id="div{{$id}}" class="form-floating mb-3">
    <div class="input-group mb-3">
        <span id="addon-{{$id}}" class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
        <div class="form-floating flex-grow-1">
            <input type="text" class="form-control" id="{{$id}}" pattern="\d{1,2}/\d{1,2}/\d{4}" name="{{$id}}" value="{{$value}}" {{$middle}} placeholder="dd/mm/aaaa"/>
            <label for="{{$id}}">{{$label}}</label>
        </div>
    </div>
</div>

@footer
<script defer>
    window.addEventListener('DOMContentLoaded', event => {
        //AutoFocus
        document.getElementById('addon-{{$id}}').addEventListener('click', function(event) {
            let input = document.getElementById('{{$id}}');
            input.focus();
        });

        new Datepicker(document.getElementById('{{$id}}'), {
            language: 'pt-BR'
        });

        document.getElementById('{{$id}}').addEventListener('changeDate', function(event) {
            if(trigger_{{$id}} !== undefined) {
                trigger_{{$id}}(event);
            }
        });

        document.getElementById('{{$id}}').addEventListener('input', function(event) {
            let inputDateMasked = "";
            let inputDate = document.getElementById('{{$id}}').value;
            inputDate = inputDate.replace(/\D/g, "");
            if(inputDate.length > 0) {
                inputDateMasked = inputDate;
            }
            if(inputDate.length > 2) {
                inputDateMasked = inputDate.substring(0,2) + '/' + inputDate.substring(2);
            }
            if(inputDate.length > 4) {
                inputDateMasked = inputDate.substring(0,2) + '/' + inputDate.substring(2,4) + '/' + inputDate.substring(4);
            }
            document.getElementById('{{$id}}').value = inputDateMasked;
        });
    });
</script>
@endfooter



@php
    unset($id);
    unset($middle);
    unset($value);
    unset($label);
@endphp