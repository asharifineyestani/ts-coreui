
<textarea
    name="{{ $field['name'] }}"
    class="{{ $class }}"
>{{ old($field['name']) ?? $field['value'] ?? $field['default'] ?? '' }}</textarea>
