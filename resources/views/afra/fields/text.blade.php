<input
    type="text"
    name="{{ $field['name'] }}"
    placeholder="{{ $field['placeholder'] ?? $field['label'] ?? '' }}"
    value="{{ old($field['name']) ?? $field['value'] ?? $field['default'] ?? '' }}"
    class="{{ $class }}"
>
