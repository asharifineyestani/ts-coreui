@php
    $current_value = old($field['name']) ?? $field['value'] ?? $field['default'] ?? null;
@endphp

<input type="text" class="date-persian form-control pull-right">
<input type="text" id="{{$field['name']}}" name="{{$field['name']}}" hidden>

@push('fields_scripts')
    <script src="{{ asset('js/persian-date.js') }}"></script>
    <script src="{{ asset('js/persian-datepicker.js') }}"></script>

    <script type="text/javascript">
        let dp = $('.date-persian').persianDatepicker({
            format: 'YYYY/MM/DD',
            altField: "#{{$field['name']}}"
        });
        dp.setDate({{$current_value}});

    </script>
@endpush



