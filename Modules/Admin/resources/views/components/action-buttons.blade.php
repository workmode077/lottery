<div class="col-md-12">
    <div class="fixed-action-card">
        <div class="action-buttons">
            <button type="submit" class="btn btn-primary">
                <i class="bx bx-save"></i>
                <span>{{ $saveLabel }}</span>
            </button>

            @if ($cancelUrl)
                <a href="{{ $cancelUrl }}" class="btn btn-secondary">
                    <i class="bx bx-x-circle"></i>
                    <span>Cancel</span>
                </a>
            @endif
        </div>
    </div>
</div>
