@php
    $current_value = old($field['name']) ?? $field['value'] ?? $field['default'] ?? '';
    if (is_object($current_value) && is_subclass_of(get_class($current_value), 'Illuminate\Database\Eloquent\Model') ) {
        $current_value = $current_value->getKey();
    }
    if (!isset($field['options'])) {
        $options = $field['model']::all();
    } else {
        $options = call_user_func($field['options'], $field['model']::query());
    }
@endphp


<select
    name="{{ $field['name'] }}"
    class="{{$class}} select2-basic-single"
>

    @if (count($options))
        @foreach ($options as $option)
            @if($current_value == $option->getKey())
                <option value="{{ $option->getKey() }}" selected>{{ $option->{$field['attribute']} }}</option>
            @else
                <option value="{{ $option->getKey() }}">{{ $option->{$field['attribute']} }}</option>
            @endif
        @endforeach
    @endif
</select>

@push('fields_scripts')
    <script>
        $(document).ready(function () {
            $('.select2-basic-single').select2();
        });
    </script>
@endpush



