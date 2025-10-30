@push('css')
    <link href="{{ asset('backend/libs/cropper/cropper.bundle.css') }}" rel="stylesheet" type="text/css" />
    <style>
        #cropped-image {
            max-width: 100%;
            max-height: 100%;
            display: block;
            object-fit: contain;
        }

        .preview_wrapper {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .preview {
            width: 150px;
            height: 150px;
            overflow: hidden;
            border: 1px solid #ddd;
        }
    </style>
@endpush

@push('js')
    <script src="{{ asset('backend/libs/cropper/cropper.bundle.js') }}"></script>
    <script>
        let cropper = null;
        let originalInput = null;
        let isCroppedFile = false;

        $(document).on('shown.bs.modal', '#cropModal', function() {
            initializeCropper();
        });

        $(document).on('hidden.bs.modal', '#cropModal', function() {
            resetCropper();
        });

        $(document).on('click', '.aspect-ratio-radio', function() {
            updateAspectRatio($(this));
        });

        function initializeCropper() {
            const image = document.getElementById('cropped-image');
            if (cropper) cropper.destroy();

            cropper = new Cropper(image, {
                aspectRatio: NaN,
                viewMode: 1,
                modal: true,
                preview: '.preview_wrapper .preview',
                ready: updateCropDimensions,
                cropmove: updateCropDimensions,
                zoom: updateCropDimensions
            });

            $('#crop-image').on('click', cropImage);
        }

        function updateCropDimensions() {
            if (!cropper) return;
            const cropData = cropper.getData();
            $('#crop-dimensions').text(`${Math.round(cropData.width)} × ${Math.round(cropData.height)} px`);
        }

        function cropImage() {
            var canvas = cropper.getCroppedCanvas();
            canvas.toBlob(function(blob) {
                isCroppedFile = true;

                const imageElement = document.getElementById('cropped-image');
                const itemId = imageElement.dataset.filePondItem;
                const filename = imageElement.dataset.filename;
                const extension = imageElement.dataset.extension;

                const croppedFile = new File([blob], filename, {
                    type: 'image/' + extension,
                    lastModified: new Date().getTime()
                });

                const pond = FilePond.find(document.getElementById(originalInput));
                pond.addFile(croppedFile).then(item => {
                    const originalItem = pond.getFile(itemId);
                    if (originalItem) pond.removeFile(itemId);
                });

                $('#cropModal').modal('hide');
            }, 'image/webp');
        }

        function resetCropper() {
            $('#cropped-image').attr('src', '');
            if (cropper) cropper.destroy();
            isCroppedFile = false;
        }

        function updateAspectRatio(button) {
            let $button = $(button);
            let width = $button.data('width');
            let height = $button.data('height');

            if (!cropper) return; // Ensure cropper exists before proceeding

            let aspectRatio = width && height ? width / height : $button.data('aspectratio');

            cropper.setAspectRatio(isNaN(aspectRatio) ? NaN : parseFloat(aspectRatio));

            if (width && height) {
                let imageData = cropper.getImageData(); // Get original image dimensions
                let scaleX = imageData.naturalWidth / imageData.width;
                let scaleY = imageData.naturalHeight / imageData.height;

                cropper.setCropBoxData({
                    width: width / scaleX,
                    height: height / scaleY
                });

                cropper.setDragMode('crop'); // Allow resizing
                cropper.enable(); // Ensure cropper is active
            }

            $('.aspect-ratio-radio').removeClass('active btn-primary').addClass('btn-outline-primary');
            $button.removeClass('btn-outline-primary').addClass('active btn-primary');

            setTimeout(updateCropDimensions, 50);
        }
    </script>
@endpush
