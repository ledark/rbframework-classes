@php
    $id = $id ?? uniqid('input_date_');
    $middle = $middle ?? '';
    $value_de = $value_de ?? '';
    $value_ate = $value_ate ?? '';
    $label = $label ?? $id;
@endphp

<div class="input-group input-daterange">
    <div class="input-group-text text-small col-3">
        {{$label}}
    </div>
    <div class="form-floating">
        <input type="text" class="form-control" id="{{$id}}_de"  pattern="\d{1,2}/\d{1,2}/\d{4}" name="{{$id}}_de" value="{{$value_de}}" {{$middle}} placeholder="dd/mm/aaaa"/>
        <label for="{{$id}}_de">{{$label}} (Início)</label>
    </div>
    <span class="input-group-text">até</span>
    <div class="form-floating">
        <input type="text" class="form-control" id="{{$id}}_ate" pattern="\d{1,2}/\d{1,2}/\d{4}" name="{{$id}}_ate" value="{{$value_ate}}" {{$middle}} placeholder="dd/mm/aaaa" />
        <label for="{{$id}}_ate">{{$label}} (Fim)</label>
    </div>
</div>

@footer
<script defer>
    window.addEventListener('DOMContentLoaded', event => {
        /*
        //AutoFocus
        document.getElementById('addon-{{$id}}').addEventListener('click', function(event) {
            let input = document.getElementById('{{$id}}');
            input.focus();
        });
        */

        new Datepicker(document.getElementById('{{$id}}_de'), {
            language: 'pt-BR'
        });

        new Datepicker(document.getElementById('{{$id}}_ate'), {
            language: 'pt-BR'
        });

/*
        document.getElementById('{{$id}}_de').addEventListener('changeDate', function(event) {
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
        */

        $('.input-daterange input').each(function() {
            $(this).datepicker('clearDates');
        });

    });
</script>
@endfooter



@php
    unset($id);
    unset($middle);
    unset($value_de);
    unset($value_ate);
    unset($label);
@endphp