<div class="modal fade" id="approveModal{{ $plan->id }}" tabindex="-1"
    aria-labelledby="approveModalLabel{{ $plan->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveModalLabel{{ $plan->id }}">{{ __('Approve Lesson Plan') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="{{ __('Close') }}"></button>
            </div>
            <form action="{{ route('tenant.admin.lesson-plans.approve', $plan) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small">{{ __('Optional: add a quick note to the teacher.') }}</p>
                    <textarea name="feedback" class="form-control" rows="3"
                        placeholder="{{ __('Great outline. Approved for delivery...') }}">{{ old('feedback') }}</textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary"
                        data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-success">{{ __('Approve') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="revisionModal{{ $plan->id }}" tabindex="-1"
    aria-labelledby="revisionModalLabel{{ $plan->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="revisionModalLabel{{ $plan->id }}">{{ __('Request Revision') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="{{ __('Close') }}"></button>
            </div>
            <form action="{{ route('tenant.admin.lesson-plans.request-revision', $plan) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small">
                        {{ __('Tell the teacher what needs attention. This field is required.') }}</p>
                    <textarea name="feedback" class="form-control" rows="4" required
                        placeholder="{{ __('Please expand assessment criteria...') }}">{{ old('feedback') }}</textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary"
                        data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-info">{{ __('Send Revision Request') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="rejectModal{{ $plan->id }}" tabindex="-1"
    aria-labelledby="rejectModalLabel{{ $plan->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel{{ $plan->id }}">{{ __('Reject Lesson Plan') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="{{ __('Close') }}"></button>
            </div>
            <form action="{{ route('tenant.admin.lesson-plans.reject', $plan) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small">
                        {{ __('Explain why this plan cannot move forward. Teachers will see this note.') }}</p>
                    <textarea name="feedback" class="form-control" rows="4" required
                        placeholder="{{ __('The resources listed are not available...') }}">{{ old('feedback') }}</textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary"
                        data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('Reject Plan') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
