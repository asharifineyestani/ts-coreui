@php
    $result = [];
      if($crud->row) {
         $medias = $crud->row->media;
          foreach ($medias as $media)
              $result[] = ([
                  "name" => $media->name,
                  "size" => $media->size,
                  "url" => $media->getUrl(),
                  "type" => $media->type,
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

                $.ajax('/crud/deleteMedia/' + file.name, {
                    type: 'GET',
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

                if (mockFile.type == "video") {
                    video(mockFile)
                } else {
                    this.emit("addedfile", mockFile);
                    this.emit("thumbnail", mockFile, mockFile.url);
                    this.emit("complete", mockFile);
                }


                @endforeach
            }
        }


        function video(file) {
            var src = file.url; ///video url not youtube or vimeo,just video on server
            var video = document.createElement('video');
            video.src = src;

            video.width = 120;
            video.height = 106;

            var canvas = document.createElement('canvas');
            canvas.width = 360;
            canvas.height = 240;
            var context = canvas.getContext('2d');

            video.addEventListener('loadeddata', function () {
                context.drawImage(video, 0, 0, canvas.width, canvas.height);
                var dataURI = canvas.toDataURL('image/jpeg');
                html += '<figure>';
                html += '<img src="' + dataURI + '' + '" alt="' + item.description + '" />';
                html += '<figurecaption>' + item.description + '</figurecaption>'
                html += '</figure>';
            });

            let icon = document.createElement('span');
            icon.setAttribute('class', 'video-icon');
            icon.innerHTML = "video"
            let div = document.createElement('div');
            div.setAttribute('class', 'wrap-video');
            div.appendChild(video);
            div.appendChild(icon);


            console.log(div)
            console.log(div)

            $('#mediable-dropzone').append(div);
        }
    </script>

@endpush



@push('fields_css')
    <link href="/js/dropzone.min.css" rel="stylesheet">


    <style>

        .wrap-video {
            display: inline-block;
            position: relative;
            padding: 0px !important;
            margin: 0px !important;
        }


        .video-icon {
            background: red;
            padding: 2px 4px;
            border-radius: 4px;
            text-align: center;
            color: white;
            background: #333;
            opacity: .4;
            position: absolute;
            left: 38px;
            top: 45px;
            font-size: 12px;
        }
    </style>
@endpush



