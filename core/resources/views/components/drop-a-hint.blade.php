<button type="button" class="btn-luxury hint-trigger"
        data-bs-toggle="modal" data-bs-target="#hintModal">
    Drop a Hint &hearts;
</button>

<div class="modal fade" id="hintModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content luxury-modal">
            <div class="modal-header">
                <h5 class="modal-title luxury-headline">Drop a Hint</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="/hint/send" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div class="mb-3">
                        <label>Their Name</label>
                        <input type="text" name="recipient_name" class="luxury-input" required>
                    </div>
                    <div class="mb-3">
                        <label>Their Email</label>
                        <input type="email" name="recipient_email" class="luxury-input" required>
                    </div>
                    <div class="mb-3">
                        <label>Occasion</label>
                        <select name="occasion" class="luxury-input">
                            <option>Engagement</option>
                            <option>Birthday</option>
                            <option>Anniversary</option>
                            <option>Just Because</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Personal Message (optional)</label>
                        <textarea name="message" class="luxury-input" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn-luxury w-100">Send the Hint</button>
                </form>
            </div>
        </div>
    </div>
</div>
