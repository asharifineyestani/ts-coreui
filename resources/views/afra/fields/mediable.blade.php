    @csrf
    <div class="form-group">
        <div class="needsclick dropzone" id="mediable-dropzone">
        </div>
    </div>
    <div>
        <input class="btn btn-danger" type="submit">
    </div>



@push('fields_scripts')

    <script src="/js/dropzone.min.js"></script>

    <script>
        let uploadedMediableMap = {}
        Dropzone.options.mediableDropzone = {
            url: '{{ route('crud.storeMedia') }}',
            maxFilesize: 2, // MB
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            success: function (file, response) {
                $('form').append('<input type="hidden" name="mediable[]" value="' + response.name + '">')
                uploadedMediableMap[file.name] = response.name
            },
            removedfile: function (file) {
                file.previewElement.remove()
                var name = ''
                if (typeof file.file_name !== 'undefined') {
                    name = file.file_name
                } else {
                    name = uploadedMediableMap[file.name]
                }
                $('form').find('input[name="mediable[]"][value="' + name + '"]').remove()
            },
            init: function () {
                    @if(isset($project) && $project->mediable)
                var files =
                {!! json_encode($project->mediable) !!}
                    for (var i in files) {
                    var file = files[i]
                    this.options.addedfile.call(this, file)
                    file.previewElement.classList.add('dz-complete')
                    $('form').append('<input type="hidden" name="mediable[]" value="' + file.file_name + '">')
                }
                @endif
            }
        }
    </script>

@endpush



@push('fields_css')
    <link href="/js/dropzone.min.css" rel="stylesheet">
@endpush



