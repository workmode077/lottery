<div class="modal fade" id="cropModal" tabindex="-1" aria-labelledby="cropModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cropModalLabel">Adjust Image Crop</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8">
                        <img id="cropped-image" src="" alt="Image to crop">
                    </div>
                    <div class="col-md-4 d-flex align-items-center">
                        <div class="preview_wrapper">
                            <div class="preview"></div>
                            <div id="crop-dimensions" class="text-muted mt-2"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <div>
                    <div class="btn-group">
                        <button type="button"
                            class="btn btn-outline-primary aspect-ratio-radio crop-mode-free active btn-primary"
                            data-aspectratio="NaN">
                            Free Crop
                        </button>
                        <button type="button" class="btn btn-outline-primary aspect-ratio-radio crop-mode-fixed">
                        </button>
                        @foreach (['1' => '1:1', '0.6666666666666667' => '2:3', '1.333333333333333' => '4:3', '1.777777777777778' => '16:9'] as $ratio => $label)
                            <button type="button" class="btn btn-outline-primary aspect-ratio-radio crop-mode-ratio"
                                data-aspectratio="{{ $ratio }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
                <div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="crop-image">Crop</button>
                </div>
            </div>
        </div>
    </div>
</div>
