@php
    $result = [];
      if($crud->row) {
         $medias = $crud->row->getMedia('*');
          foreach ($medias as $media)
              $result[] = ([
                  "name" => $media->name,
                  "size" => $media->size,
                  "url" => $media->getUrl(),
              ]);
  }

@endphp



<div class="form-group">
    <div class="needsclick dropzone" id="mediable-dropzone">
    </div>
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


                $.ajax('/crud/deleteMedia/'+file.name, {
                    type: 'GET',
                    // data: {name: file.name},
                    // success: function (data, status, xhr) {
                    //     console.log('status: ' + status + ', data: ' + data);
                    // },
                    // error: function (jqXhr, textStatus, errorMessage) {
                    //     console.log('Error' + errorMessage);
                    // }
                });


                console.log(file)
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
                let mockFile = '';

                @foreach($result as $media)

                    mockFile = {!! json_encode($media) !!};
                this.emit("addedfile", mockFile);
                this.emit("thumbnail", mockFile, mockFile.url);
                this.emit("complete", mockFile);
                @endforeach
            }
        }
    </script>

@endpush



@push('fields_css')
    <link href="/js/dropzone.min.css" rel="stylesheet">
@endpush



