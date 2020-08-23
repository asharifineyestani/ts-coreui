<input
    type="url"
    name="{{ $field['name'] }}"
    value="{{ old($field['name']) ?? $field['value'] ?? $field['default'] ?? '' }}"
    class="{{ $class }}"
>
