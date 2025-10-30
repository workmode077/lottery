<!-- Template Preview Modal -->
<div class="modal fade bs-example-modal-sm" id="preview" tabindex="-1" role="dialog" aria-labelledby="previewTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <img src="" class="img-fluid previewImage" style="width: 100%;">
            </div>
        </div>
    </div>
</div>
@push('js')
    <script>
        //preview section button click
        $(document).on('click', '.section-preview', function(e) {
            $('#preview').find('img').attr('src', $(this).attr('data-img'));
            $('#preview').modal('show');
        });
    </script>
@endpush
