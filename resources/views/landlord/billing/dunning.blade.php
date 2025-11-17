@extends('landlord.layouts.app')

@section('content')
<div class="card border-0 shadow-sm">
  <div class="card-body p-4 p-lg-5">
    <h1 class="h4 fw-semibold mb-3">{{ __('Dunning & Collections Wizard') }}</h1>
    <p class="text-secondary mb-4">{{ __('Configure reminders, grace periods, enforcement actions, and templates to recover overdue invoices.') }}</p>

    @if (session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
      <div class="alert alert-danger">
        <div class="fw-semibold mb-2">{{ __('Please fix the following:') }}</div>
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('landlord.billing.dunning.save') }}" method="POST" class="needs-validation" novalidate>
      @csrf

      <div class="row g-4">
        <div class="col-lg-6">
          <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-body-tertiary fw-semibold">{{ __('Policy & Timing') }}</div>
            <div class="card-body">
              <div class="mb-3">
                <label class="form-label">{{ __('Policy Name') }}</label>
                <input type="text" class="form-control" name="name" value="{{ old('name', $policy->name ?? 'Default Policy') }}" required>
              </div>

              <div class="row g-3">
                <div class="col-4">
                  <label class="form-label">{{ __('Warn before due (days)') }}</label>
                  <input type="number" min="0" class="form-control" name="warning_threshold_days" value="{{ old('warning_threshold_days', $policy->warning_threshold_days ?? 5) }}" required>
                </div>
                <div class="col-4">
                  <label class="form-label">{{ __('Suspend after (days overdue)') }}</label>
                  <input type="number" min="0" class="form-control" name="suspension_grace_days" value="{{ old('suspension_grace_days', $policy->suspension_grace_days ?? 7) }}" required>
                </div>
                <div class="col-4">
                  <label class="form-label">{{ __('Terminate after (addl. days)') }}</label>
                  <input type="number" min="0" class="form-control" name="termination_grace_days" value="{{ old('termination_grace_days', $policy->termination_grace_days ?? 30) }}" required>
                </div>
              </div>

              <div class="mt-3">
                <label class="form-label">{{ __('Reminder Windows (comma-separated days relative to due date)') }}</label>
                <input type="text" class="form-control" name="reminder_windows" value="{{ old('reminder_windows', implode(',', $policy->reminder_windows ?? [-7,-3,-1,0,3])) }}" placeholder="-7,-3,-1,0,3">
                <small class="text-secondary">{{ __('Negative values are before due date, 0 is on due date, positive are after.') }}</small>
              </div>

              <div class="row g-3 mt-3">
                <div class="col-6">
                  <label class="form-label">{{ __('Late Fee (%)') }}</label>
                  <input type="number" step="0.01" min="0" class="form-control" name="late_fee_percent" value="{{ old('late_fee_percent', $policy->late_fee_percent) }}">
                </div>
                <div class="col-6">
                  <label class="form-label">{{ __('Late Fee (flat)') }}</label>
                  <input type="number" step="0.01" min="0" class="form-control" name="late_fee_flat" value="{{ old('late_fee_flat', $policy->late_fee_flat) }}">
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-body-tertiary fw-semibold">{{ __('Channels & Recipients') }}</div>
            <div class="card-body">
              <div class="mb-3">
                <label class="form-label">{{ __('Warning Channels') }}</label>
                <div class="d-flex flex-wrap gap-3">
                  @php $wc = collect(old('warning_channels', $policy->warning_channels ?? ['mail'])); @endphp
                  <label class="form-check">
                    <input type="checkbox" class="form-check-input" name="warning_channels[]" value="mail" {{ $wc->contains('mail') ? 'checked' : '' }}> Mail
                  </label>
                  <label class="form-check">
                    <input type="checkbox" class="form-check-input" name="warning_channels[]" value="sms" {{ $wc->contains('sms') ? 'checked' : '' }}> SMS
                  </label>
                  <label class="form-check">
                    <input type="checkbox" class="form-check-input" name="warning_channels[]" value="slack" {{ $wc->contains('slack') ? 'checked' : '' }}> Slack
                  </label>
                  <label class="form-check">
                    <input type="checkbox" class="form-check-input" name="warning_channels[]" value="webhook" {{ $wc->contains('webhook') ? 'checked' : '' }}> Webhook
                  </label>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label">{{ __('Warning Recipients (comma-separated emails)') }}</label>
                <input type="text" class="form-control" name="warning_recipients" value="{{ old('warning_recipients', implode(',', $policy->warning_recipients ?? [])) }}">
              </div>

              <div class="mb-3">
                <label class="form-label">{{ __('Warning Phones (comma-separated MSISDN)') }}</label>
                <input type="text" class="form-control" name="warning_phones" value="{{ old('warning_phones', implode(',', $policy->warning_phones ?? [])) }}" placeholder="2547XXXXXXXX, +15551234567">
                <small class="text-secondary">{{ __('Use E.164 format where possible') }}</small>
              </div>

              <div class="mb-3">
                <label class="form-label">{{ __('Suspension Recipients (comma-separated emails)') }}</label>
                <input type="text" class="form-control" name="suspension_recipients" value="{{ old('suspension_recipients', implode(',', $policy->suspension_recipients ?? [])) }}">
              </div>

              <div class="mb-3">
                <label class="form-label">{{ __('Suspension Phones (comma-separated MSISDN)') }}</label>
                <input type="text" class="form-control" name="suspension_phones" value="{{ old('suspension_phones', implode(',', $policy->suspension_phones ?? [])) }}">
              </div>

              <div class="mb-3">
                <label class="form-label">{{ __('Termination Recipients (comma-separated emails)') }}</label>
                <input type="text" class="form-control" name="termination_recipients" value="{{ old('termination_recipients', implode(',', $policy->termination_recipients ?? [])) }}">
              </div>

              <div class="mb-3">
                <label class="form-label">{{ __('Termination Phones (comma-separated MSISDN)') }}</label>
                <input type="text" class="form-control" name="termination_phones" value="{{ old('termination_phones', implode(',', $policy->termination_phones ?? [])) }}">
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-body-tertiary fw-semibold">{{ __('Templates') }}</div>
        <div class="card-body">
          @php $templates = $policy->templates ?? []; @endphp
          <div class="mb-3">
            <label class="form-label">{{ __('Warning Email Subject') }}</label>
            <input type="text" class="form-control" name="templates[warning_subject]" value="{{ old('templates.warning_subject', $templates['warning_subject'] ?? 'Invoice &#123;&#123;invoice_number&#125;&#125; is due on &#123;&#123;due_date&#125;&#125;') }}">
          </div>
          <div class="mb-3">
            <label class="form-label">{{ __('Warning Email Body (Markdown)') }}</label>
            <textarea rows="5" class="form-control" name="templates[warning_body]">{{ old('templates.warning_body', $templates['warning_body'] ?? "Hello &#123;&#123;tenant_name&#125;&#125;,\n\nThis is a friendly reminder that invoice &#123;&#123;invoice_number&#125;&#125; for &#123;&#123;amount&#125;&#125; is due on &#123;&#123;due_date&#125;&#125;.\n\nYou can pay securely here: &#123;&#123;pay_url&#125;&#125;\n\nThank you,") }}</textarea>
          </div>

          <div class="mb-3">
            <label class="form-label">{{ __('Suspension Email Subject') }}</label>
            <input type="text" class="form-control" name="templates[suspension_subject]" value="{{ old('templates.suspension_subject', $templates['suspension_subject'] ?? 'Service suspended due to overdue invoice &#123;&#123;invoice_number&#125;&#125;') }}">
          </div>
          <div class="mb-3">
            <label class="form-label">{{ __('Suspension Email Body (Markdown)') }}</label>
            <textarea rows="5" class="form-control" name="templates[suspension_body]">{{ old('templates.suspension_body', $templates['suspension_body'] ?? "Hello &#123;&#123;tenant_name&#125;&#125;,\n\nYour service has been temporarily suspended due to invoice &#123;&#123;invoice_number&#125;&#125; overdue by &#123;&#123;days_overdue&#125;&#125; days.\nPlease make a payment here: &#123;&#123;pay_url&#125;&#125;\n\nIf you already paid, please ignore this message.\n\nThank you,") }}</textarea>
          </div>

          <div class="mb-3">
            <label class="form-label">{{ __('Termination Email Subject') }}</label>
            <input type="text" class="form-control" name="templates[termination_subject]" value="{{ old('templates.termination_subject', $templates['termination_subject'] ?? 'Service termination due to non-payment') }}">
          </div>
          <div class="mb-0">
            <label class="form-label">{{ __('Termination Email Body (Markdown)') }}</label>
            <textarea rows="5" class="form-control" name="templates[termination_body]">{{ old('templates.termination_body', $templates['termination_body'] ?? "Hello &#123;&#123;tenant_name&#125;&#125;,\n\nWe regret to inform you that your service will be terminated on &#123;&#123;termination_date&#125;&#125; due to non-payment.\n\nIf this is an error or you need help, contact support.\n\nRegards,") }}</textarea>
          </div>
        </div>
      </div>

      <div class="mt-4 d-flex gap-2">
        <button class="btn btn-primary" type="submit">
          <i class="bi bi-save me-2"></i>{{ __('Save Policy') }}
        </button>
        <a class="btn btn-outline-secondary" href="{{ route('landlord.billing') }}">{{ __('Cancel') }}</a>
        <div class="ms-auto d-flex gap-2">
          <a class="btn btn-outline-primary" href="{{ route('landlord.billing.dunning.preview', ['action' => 'warning']) }}" target="_blank">{{ __('Preview warning email') }}</a>
          <a class="btn btn-outline-primary" href="{{ route('landlord.billing.dunning.preview', ['action' => 'suspension']) }}" target="_blank">{{ __('Preview suspension email') }}</a>
          <a class="btn btn-outline-primary" href="{{ route('landlord.billing.dunning.preview', ['action' => 'termination']) }}" target="_blank">{{ __('Preview termination email') }}</a>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
