<div class="modal fade" id="dropHintModal" tabindex="-1" aria-labelledby="dropHintModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content luxury-modal border-0">
            <div class="modal-header">
                <h5 class="modal-title luxury-headline" id="dropHintModalLabel">{{ __('Drop a hint') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="{{ route('hint.send') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="item_id" value="{{ $item->id }}">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Their name') }}</label>
                        <input type="text" name="recipient_name" class="form-control" required maxlength="100">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Their email') }}</label>
                        <input type="email" name="recipient_email" class="form-control" required maxlength="255">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Occasion') }}</label>
                        <select name="occasion" class="form-control" required>
                            <option value="Engagement">{{ __('Engagement') }}</option>
                            <option value="Anniversary">{{ __('Anniversary') }}</option>
                            <option value="Wedding">{{ __('Wedding') }}</option>
                            <option value="Birthday">{{ __('Birthday') }}</option>
                            <option value="Holiday">{{ __('Holiday') }}</option>
                            <option value="Just Because">{{ __('Just Because') }}</option>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">{{ __('Short message (optional)') }}</label>
                        <textarea name="message" class="form-control" rows="3" maxlength="500"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn-luxury">{{ __('Send hint') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
