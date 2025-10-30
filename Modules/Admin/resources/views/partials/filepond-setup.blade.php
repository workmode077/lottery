@push('css')
    <!-- FilePond CSS: Core styling for FilePond file upload plugin, providing a modern and accessible file input -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/filepond/4.31.1/filepond.min.css"
        integrity="sha512-TtQdiqlFBF4xOf9GCawalT4FQ7qihYm+EMYxpor3WzndeGC+NflmNd/P5AN8vvRH4XqTjoNrIeJRbZcifEMbWA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/filepond-plugin-image-preview@4.6.12/dist/filepond-plugin-image-preview.min.css">
    <!-- FilePond Image Preview Plugin CSS: Styling for displaying image previews within the FilePond file upload interface -->
@endpush
@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/filepond/4.31.1/filepond.min.js"
        integrity="sha512-UlakzTkpbSDfqJ7iKnPpXZ3HwcCnFtxYo1g95pxZxQXrcCLB0OP9+uUaFEj5vpX7WwexnUqYXIzplbxq9KSatw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- include FilePond jQuery adapter -->
    <script src="https://cdn.jsdelivr.net/npm/jquery-filepond@1.0.0/filepond.jquery.min.js"></script>
    <!-- include FilePond plugins -->
    <script
        src="https://cdn.jsdelivr.net/npm/filepond-plugin-image-preview@4.6.12/dist/filepond-plugin-image-preview.min.js">
    </script>
    <script
        src="https://cdn.jsdelivr.net/npm/filepond-plugin-file-validate-type@1.2.9/dist/filepond-plugin-file-validate-type.min.js">
    </script>
    <script
        src="https://cdn.jsdelivr.net/npm/filepond-plugin-file-validate-size@2.2.8/dist/filepond-plugin-file-validate-size.min.js">
    </script>
    <!-- Register FilePond Plugins: Registering necessary plugins for image preview, file size validation, and file type validation -->
    <script>
        $.fn.filepond.registerPlugin(
            FilePondPluginImagePreview, // Plugin for image previews
            FilePondPluginFileValidateSize, // Plugin for validating file size
            FilePondPluginFileValidateType // Plugin for validating fileD types
        );

        var mediaUrls = JSON.parse(@json(optional($mediaSource)->getModelMediaUrls() ?? '{}'));

        //filepond init
        $(function() {
            regularInput();
            regularObjectInput();
            croppedInput();
        });

        function regularInput() {
            $('.filepond-input').each(function() {
                var inputElement = $(this);
                var inputName = inputElement.attr('data-in-name') ?? inputElement.attr('name');
                var size = inputElement.attr('data-size');
                var accept = inputElement.attr('data-accept');
                let imagePreviewHeight = null;
                if ($(inputElement).attr('multiple') !== undefined && $(inputElement).hasClass(
                        'filepond-multiple-grid')) {
                    imagePreviewHeight = 160;
                }
                let allowMultiple = $(inputElement).attr('multiple') !== undefined ? true : false;
                var pondOptions = {
                    allowMultiple: allowMultiple,
                    storeAsFile: true,
                    acceptedFileTypes: accept || 'image/*',
                    maxFileSize: size || '600KB',
                    credits: false,
                    imagePreviewHeight: imagePreviewHeight,
                };
               if(inputName)
                    inputName = inputName.replace('[]', '');

                if (typeof mediaUrls !== 'undefined' && mediaUrls[inputName]) {
                    if (typeof mediaUrls[inputName] === 'object') {
                        var files = $.each(mediaUrls[inputName], function(indexInArray, valueOfElement) {
                            return {
                                source: valueOfElement
                            }
                        });
                        pondOptions.files = files;
                    } else {
                        pondOptions.files = [{
                            source: mediaUrls[inputName],
                        }];
                    }
                }
                const pond = FilePond.create(inputElement[0], pondOptions);
            });
        }

        function regularObjectInput() {
            $('.filepond-input-object').each(function() {
                var inputElement = $(this);
                var inputName = inputElement.attr('data-in-name') ?? inputElement.attr('name');
                var size = inputElement.attr('data-size');
                var accept = inputElement.attr('data-accept');
                var src = inputElement.attr('data-src');
                let imagePreviewHeight = null;
                if ($(inputElement).attr('multiple') !== undefined && $(inputElement).hasClass(
                        'filepond-multiple-grid')) {
                    imagePreviewHeight = 160;
                }
                let allowMultiple = $(inputElement).attr('multiple') !== undefined ? true : false;
                var pondOptions = {
                    allowMultiple: allowMultiple,
                    storeAsFile: true,
                    acceptedFileTypes: accept || 'image/*',
                    maxFileSize: size || '600KB',
                    credits: false,
                    imagePreviewHeight: imagePreviewHeight,
                };
                if (src) {
                    pondOptions.files = [{
                        source: src,
                    }];
                }
                const pond = FilePond.create(inputElement[0], pondOptions);
            });
        }

        function croppedInput() {
            let processedPromises = [];
            isCroppedFile = true; //for not show crop for already exist images
            $('.filepond-input-crop').each(function() {
                var inputElement = $(this);
                var inputName = inputElement.attr('name');
                var size = inputElement.attr('data-size');
                var accept = inputElement.attr('data-accept');
                var width = inputElement.attr('data-width');
                var height = inputElement.attr('data-height');
                let allowMultiple = $(inputElement).attr('multiple') !== undefined ? true : false;
                var pondOptions = {
                    allowMultiple: allowMultiple,
                    storeAsFile: true,
                    acceptedFileTypes: accept || 'image/*',
                    maxFileSize: size || '600KB',
                    credits: false,
                    beforeAddFile: (item) => {
                        //ckeck the image is svg or not
                        if (item.fileExtension === 'svg') {
                            return true;
                        }
                        if (isCroppedFile) {
                            return true;
                        }
                        return new Promise((resolve) => {
                            const file = item.file;
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                // Set image in cropper modal
                                const imageElement = document.getElementById(
                                    'cropped-image');
                                imageElement.src = e.target.result;
                                isFromFilepond = true;
                                originalInput =
                                    inputName; // HERE ID PASSING AS IT IS SAME

                                $('.aspect-ratio-radio').removeClass('active btn-primary')
                                    .addClass('btn-outline-primary');
                                $('.crop-mode-free').removeClass('btn-outline-primary')
                                    .addClass('active btn-primary');

                                let cropModeFixedButton = $('.crop-mode-fixed');
                                let cropModeRatioButton = $('.crop-mode-ratio');
                                // Only update the button if width and height exist
                                if (width && height) {
                                    cropModeRatioButton.hide();
                                    cropModeFixedButton.attr('data-width', width);
                                    cropModeFixedButton.attr('data-height', height);
                                    cropModeFixedButton.text(`${width} x ${height} px`);
                                    cropModeFixedButton.show();
                                } else {
                                    cropModeFixedButton.hide();
                                    cropModeRatioButton.show();
                                }
                                // Show modal
                                $('#cropModal').modal('show');
                                // Store the FilePond item for later use
                                imageElement.dataset.filePondItem = item.id;
                                imageElement.dataset.filename = item.filename;
                                imageElement.dataset.extension = item.fileExtension;
                            };
                            reader.readAsDataURL(file);
                            // Prevent FilePond from adding the file immediately
                            resolve(false);
                        });
                    },
                };
                inputName = inputName.replace('[]', '').trim();
                if (typeof mediaUrls !== 'undefined' && mediaUrls[inputName]) {
                    if (typeof mediaUrls[inputName] === 'object') {
                        var files = $.each(mediaUrls[inputName], function(indexInArray, valueOfElement) {
                            return {
                                source: valueOfElement
                            }
                        });
                        pondOptions.files = files;
                    } else {
                        pondOptions.files = [{
                            source: mediaUrls[inputName],
                        }];
                    }
                }
                const pond = FilePond.create(inputElement[0], pondOptions);
                processedPromises.push(pond);
            });
            // Wait for all FilePond instances to process their files
            Promise.all(processedPromises.map(pond => {
                return new Promise((resolve) => {
                    //check if pond has files
                    if (!pond.getFiles().length) {
                        resolve();
                    }
                    //to do when all files added
                    pond.on('addfile', (error, file) => {
                        resolve();
                    });
                });
            })).then(() => {
                //all FilePond instances have processed their files
                isCroppedFile = false;
            });
        }



        function regularObjectInput() {
            $('.filepond-input-object').each(function() {
                var inputElement = $(this);
                var inputName = inputElement.attr('data-in-name') ?? inputElement.attr('name');
                var size = inputElement.attr('data-size');
                var accept = inputElement.attr('data-accept');
                var src = inputElement.attr('data-src');
                let imagePreviewHeight = null;
                if ($(inputElement).attr('multiple') !== undefined && $(inputElement).hasClass(
                        'filepond-multiple-grid')) {
                    imagePreviewHeight = 160;
                }
                let allowMultiple = $(inputElement).attr('multiple') !== undefined ? true : false;
                var pondOptions = {
                    allowMultiple: allowMultiple,
                    storeAsFile: true,
                    acceptedFileTypes: accept || 'image/*',
                    maxFileSize: size || '600KB',
                    credits: false,
                    imagePreviewHeight: imagePreviewHeight,
                };
                if (src) {
                    pondOptions.files = [{
                        source: src,
                    }];
                }
                const pond = FilePond.create(inputElement[0], pondOptions);
            });
        }
    </script>
@endpush
