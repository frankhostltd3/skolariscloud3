@extends('landlord.layouts.app')

@section('content')
  <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
    <div>
      <span class="badge text-bg-primary-subtle text-primary-emphasis px-3 py-2 mb-2">{{ __('Invoice creation') }}</span>
      <h1 class="h3 fw-semibold mb-1">{{ __('Generate invoices across your school network') }}</h1>
      <p class="text-secondary mb-0">{{ __('Build multi-school invoices, add services, and confirm totals before issuing drafts to finance teams.') }}</p>
    </div>
    <div class="d-flex align-items-center gap-2 text-secondary small">
      <span class="bi bi-lock-fill text-warning"></span>
      <span>{{ __('Invoices are saved as drafts. Sending requires payment gateway setup.') }}</span>
    </div>
  </div>

  @if($invoiceTableMissing ?? false)
    <div class="alert alert-warning d-flex align-items-center gap-3" role="alert">
      <span class="bi bi-database-exclamation fs-4"></span>
      <div>
        <p class="fw-semibold mb-1">{{ __('Landlord invoices table missing') }}</p>
        <p class="mb-0 small">{{ __('Run the landlord migrations to enable invoice history. Example: :command', ['command' => 'php artisan migrate']) }}</p>
      </div>
    </div>
  @endif

  <div class="row g-4" data-billing-wizard data-store-url="{{ route('landlord.billing.invoices.store') }}">
    <div class="col-12 col-xl-8">
      <div class="card border-0 shadow-sm">
        <div class="card-body d-flex flex-column gap-4">
          <div data-wizard-alerts class="d-grid gap-2"></div>

          <div class="billing-wizard-stepper" data-wizard-progress>
            <div class="billing-wizard-step is-active" data-step="1">
              <span class="billing-wizard-step__index">1</span>
              <div>
                <p class="fw-semibold mb-0">{{ __('Recipients & schedule') }}</p>
                <p class="text-secondary small mb-0">{{ __('Pick schools and configure period and due date.') }}</p>
              </div>
            </div>
            <div class="billing-wizard-step" data-step="2">
              <span class="billing-wizard-step__index">2</span>
              <div>
                <p class="fw-semibold mb-0">{{ __('Line items') }}</p>
                <p class="text-secondary small mb-0">{{ __('Add subscription fees, services, or one-off charges.') }}</p>
              </div>
            </div>
            <div class="billing-wizard-step" data-step="3">
              <span class="billing-wizard-step__index">3</span>
              <div>
                <p class="fw-semibold mb-0">{{ __('Review & confirm') }}</p>
                <p class="text-secondary small mb-0">{{ __('Double-check totals, taxes, and notes before saving.') }}</p>
              </div>
            </div>
          </div>

          <div class="billing-wizard-pane" data-wizard-pane="1">
            <div class="d-flex flex-column gap-4">
              <section>
                <h2 class="h5 fw-semibold mb-3">{{ __('Select tenant recipients') }}</h2>
                <p class="text-secondary small mb-3">{{ __('Choose one or more schools to include in this invoice batch. Recently onboarded schools appear first.') }}</p>
                <div class="row g-3">
                  @forelse($tenantOptions as $tenant)
                    <div class="col-md-6 mb-2">
                      <label class="border rounded-3 d-flex gap-3 align-items-center p-3 h-100 w-100">
                        <input class="form-check-input mt-0" type="checkbox" name="tenants[]" value="{{ $tenant['id'] }}" data-tenant
                          data-tenant-name="{{ $tenant['name'] }}" data-tenant-plan="{{ $tenant['plan'] }}" />
                        <div class="d-flex flex-column">
                          <span class="fw-semibold">{{ $tenant['name'] }}</span>
                          <span class="text-secondary small">{{ __('Plan: :plan', ['plan' => $tenant['plan']]) }} · {{ $tenant['country'] ?? __('Region N/A') }}</span>
                        </div>
                      </label>
                    </div>
                  @empty
                    <div class="col-12">
                      <div class="alert alert-warning mb-0">{{ __('No tenants were found. Add schools before generating invoices.') }}</div>
                    </div>
                  @endforelse
                </div>
              </section>

              <section>
                <h2 class="h5 fw-semibold mb-3">{{ __('Invoice schedule') }}</h2>
                <div class="row g-3">
                  <div class="col-md-6">
                    <label for="invoicePeriod" class="form-label">{{ __('Billing month') }}</label>
                    <input type="month" id="invoicePeriod" class="form-control" data-invoice-period />
                    <div class="form-text">{{ __('Typically the month services were delivered.') }}</div>
                  </div>
                  <div class="col-md-6">
                    <label for="invoiceDueDate" class="form-label">{{ __('Due date') }}</label>
                    <input type="date" id="invoiceDueDate" class="form-control" data-invoice-due />
                    <div class="form-text">{{ __('Defaults to 14 days after issue if left blank.') }}</div>
                  </div>
                </div>
              </section>
            </div>
          </div>

          <div class="billing-wizard-pane d-none" data-wizard-pane="2">
            <div class="d-flex flex-column gap-4">
              <section>
                <div class="d-flex align-items-center justify-content-between">
                  <h2 class="h5 fw-semibold mb-0">{{ __('Invoice line items') }}</h2>
                  <button type="button" class="btn btn-outline-primary btn-sm" data-add-line-item>
                    <span class="bi bi-plus-circle me-1"></span>{{ __('Add custom line item') }}
                  </button>
                </div>
                <p class="text-secondary small mb-3">{{ __('Enter descriptions, quantities, and unit prices. Totals are calculated automatically.') }}</p>

                <div class="table-responsive border rounded-3">
                  <table class="table align-middle mb-0" data-line-items-table>
                    <thead class="table-light">
                      <tr>
                        <th scope="col" style="width: 35%">{{ __('Description') }}</th>
                        <th scope="col" style="width: 20%">{{ __('Category') }}</th>
                        <th scope="col" style="width: 15%" class="text-end">{{ __('Qty') }}</th>
                        <th scope="col" style="width: 15%" class="text-end">{{ __('Unit price') }}</th>
                        <th scope="col" style="width: 15%" class="text-end">{{ __('Line total') }}</th>
                        <th scope="col" class="text-end"></th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
                </div>
              </section>

              <section>
                <h3 class="h6 fw-semibold mb-2">{{ __('Quick add suggestions') }}</h3>
                <div class="d-flex flex-wrap gap-2">
                  @foreach($suggestedLineItems as $item)
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-suggested-item
                      data-label="{{ $item['label'] }}" data-description="{{ $item['description'] }}" data-amount="{{ $item['amount'] }}" data-category="{{ __('Services') }}">
                      <span class="bi bi-lightning me-1"></span>{{ $item['label'] }} · {{ __('${amount}', ['amount' => number_format($item['amount'], 0)]) }}
                    </button>
                  @endforeach
                </div>
              </section>
            </div>
          </div>

          <div class="billing-wizard-pane d-none" data-wizard-pane="3">
            <div class="d-flex flex-column gap-4">
              <section>
                <h2 class="h5 fw-semibold mb-3">{{ __('Review draft details') }}</h2>
                <div class="order-summary-card">
                  <div class="order-summary-card__header">
                    <span class="bi bi-receipt-cutoff"></span>
                    <div>
                      <p class="fw-semibold mb-0">{{ __('Draft summary') }}</p>
                      <p class="text-secondary small mb-0">{{ __('Totals update as you make changes.') }}</p>
                    </div>
                  </div>
                  <dl class="order-summary-card__list">
                    <div class="order-summary-card__row">
                      <dt>{{ __('Recipients selected') }}</dt>
                      <dd data-summary-recipient-count>0</dd>
                    </div>
                    <div class="order-summary-card__row">
                      <dt>{{ __('Invoice window') }}</dt>
                      <dd data-summary-period>—</dd>
                    </div>
                    <div class="order-summary-card__row">
                      <dt>{{ __('Due date') }}</dt>
                      <dd data-summary-due>—</dd>
                    </div>
                    <div class="order-summary-card__row">
                      <dt>{{ __('Line items') }}</dt>
                      <dd data-summary-line-count>0</dd>
                    </div>
                  </dl>
                  <div class="order-summary-card__total mt-3">
                    <span>{{ __('Grand total') }}</span>
                    <span class="fw-semibold fs-5" data-summary-total>$0.00</span>
                  </div>
                </div>
              </section>

              <section>
                <h3 class="h6 fw-semibold mb-2">{{ __('Invoice notes & payment instructions') }}</h3>
                <textarea class="form-control" rows="4" placeholder="{{ __('Share M-Pesa, bank, or card payment instructions that appear on the PDF.') }}" data-invoice-notes></textarea>
              </section>

              <section>
                <h3 class="h6 fw-semibold mb-2">{{ __('Next steps after saving') }}</h3>
                <ul class="domain-timeline">
                  <li>
                    <span class="domain-timeline__icon bi bi-upload"></span>
                    <div>
                      <p class="fw-semibold mb-0">{{ __('Generate PDF drafts') }}</p>
                      <p class="text-secondary small mb-0">{{ __('Preview the invoice for each school and adjust branding before distribution.') }}</p>
                    </div>
                  </li>
                  <li>
                    <span class="domain-timeline__icon bi bi-envelope-paper"></span>
                    <div>
                      <p class="fw-semibold mb-0">{{ __('Email finance leads') }}</p>
                      <p class="text-secondary small mb-0">{{ __('Send secure links or attach PDFs with payment instructions.') }}</p>
                    </div>
                  </li>
                  <li>
                    <span class="domain-timeline__icon bi bi-check2-circle"></span>
                    <div>
                      <p class="fw-semibold mb-0">{{ __('Reconcile payments') }}</p>
                      <p class="text-secondary small mb-0">{{ __('Record payment confirmations and trigger automatic reminders when late.') }}</p>
                    </div>
                  </li>
                </ul>
              </section>
            </div>
          </div>

          <div class="billing-wizard-actions">
            <div class="d-flex gap-2">
              <button type="button" class="btn btn-outline-secondary" data-wizard-prev>
                <span class="bi bi-arrow-left me-1"></span>{{ __('Back') }}
              </button>
              <button type="button" class="btn btn-primary" data-wizard-next>
                {{ __('Next step') }}<span class="bi bi-arrow-right ms-1"></span>
              </button>
            </div>
            <button type="button" class="btn btn-success d-none" data-wizard-complete>
              <span class="bi bi-file-earmark-plus me-1"></span>{{ __('Save draft invoices') }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-xl-4">
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body d-grid gap-3" data-summary-panel>
          <h2 class="h6 fw-semibold mb-0">{{ __('Wizard progress') }}</h2>
          <div class="d-flex align-items-center gap-3">
            <div class="flex-fill">
              <div class="progress" role="progressbar" aria-label="{{ __('Wizard progress') }}">
                <div class="progress-bar" style="width: 33%" data-progress-bar></div>
              </div>
            </div>
            <span class="small text-secondary" data-progress-copy>{{ __('Step 1 of 3') }}</span>
          </div>
          <div class="bg-body-secondary rounded-3 p-3">
            <p class="fw-semibold small mb-1">{{ __('Draft recipients') }}</p>
            <ul class="list-unstyled mb-0 small d-grid gap-2" data-summary-recipient-list>
              <li class="text-secondary">{{ __('No schools selected yet.') }}</li>
            </ul>
          </div>
          <div class="bg-body-secondary rounded-3 p-3">
            <p class="fw-semibold small mb-1">{{ __('Line items preview') }}</p>
            <ul class="list-unstyled mb-0 small d-grid gap-2" data-summary-line-items>
              <li class="text-secondary">{{ __('Add line items in step 2 to see totals here.') }}</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="card border-0 shadow-sm">
        <div class="card-body d-grid gap-3">
          <h2 class="h6 fw-semibold mb-0">{{ __('Need a hand?') }}</h2>
          <p class="text-secondary small mb-0">{{ __('Our finance success team can double-check taxes, currencies, and payment reminders before you send invoices.') }}</p>
          <a href="mailto:finance@skolariscloud.com" class="btn btn-outline-primary btn-sm">
            <span class="bi bi-envelope-at me-1"></span>{{ __('Email finance success') }}
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="card border-0 shadow-sm mt-4">
    <div class="card-body">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
          <h2 class="h5 fw-semibold mb-0">{{ __('Recent invoices') }}</h2>
          <p class="text-secondary small mb-0">{{ __('Track recent activity to understand who has paid and who needs a reminder.') }}</p>
        </div>
        <button type="button" class="btn btn-outline-secondary btn-sm" disabled>
          <span class="bi bi-download me-1"></span>{{ __('Export CSV') }}
        </button>
      </div>

      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead class="table-light">
            <tr class="text-secondary text-uppercase small">
              <th scope="col">{{ __('Invoice #') }}</th>
              <th scope="col">{{ __('Tenant') }}</th>
              <th scope="col" class="text-end">{{ __('Amount') }}</th>
              <th scope="col">{{ __('Issued') }}</th>
              <th scope="col">{{ __('Status') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach($recentInvoices as $invoice)
              <tr>
                <td class="fw-semibold">{{ $invoice['number'] }}</td>
                <td>{{ $invoice['tenant'] }}</td>
                <td class="text-end fw-semibold">${{ number_format($invoice['amount'], 2) }}</td>
                <td class="text-secondary small">{{ $invoice['issued']->diffForHumans() }}</td>
                <td>
                  @php
                    $status = $invoice['status'];
                    $badgeClass = match ($status) {
                      'Paid' => 'text-bg-success-subtle text-success-emphasis',
                      'Sent' => 'text-bg-primary-subtle text-primary-emphasis',
                      'Viewed' => 'text-bg-info-subtle text-info-emphasis',
                      default => 'text-bg-secondary-subtle text-secondary-emphasis',
                    };
                  @endphp
                  <span class="badge {{ $badgeClass }}">{{ __($status) }}</span>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const wizard = document.querySelector('[data-billing-wizard]');
        if (!wizard) {
          return;
        }

        const storeUrl = wizard.dataset.storeUrl || '';
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const uuid = () => {
          if (window.crypto?.randomUUID) {
            return crypto.randomUUID();
          }
          return `item-${Date.now()}-${Math.random().toString(16).slice(2)}`;
        };

        const escapeHtml = (value = '') => (
          value
            .toString()
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;')
        );

        const state = {
          step: 1,
          tenants: new Map(),
          period: '',
          dueDate: '',
          items: [],
          notes: '',
        };

        const toIsoDate = (date) => date.toISOString().slice(0, 10);

        const computePeriodRange = () => {
          if (!state.period) {
            return { start: null, end: null };
          }

          const [year, month] = state.period.split('-').map(Number);
          if (!year || !month) {
            return { start: null, end: null };
          }

          const start = new Date(Date.UTC(year, month - 1, 1));
          const end = new Date(Date.UTC(year, month, 0));

          return {
            start: toIsoDate(start),
            end: toIsoDate(end),
          };
        };

        const formatPeriodLabel = () => {
          const { start } = computePeriodRange();
          if (!start) {
            return '—';
          }

          const formatter = new Intl.DateTimeFormat(undefined, { month: 'long', year: 'numeric' });
          return formatter.format(new Date(`${start}T00:00:00Z`));
        };

        const formatDateDisplay = (value) => {
          if (!value) {
            return '—';
          }

          const formatter = new Intl.DateTimeFormat(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
          return formatter.format(new Date(`${value}T00:00:00Z`));
        };

    const defaultLineDescription = @json(__('Invoice line item'));
    const missingItemsMessage = @json(__('Add at least one line item before saving the invoices.'));
    const missingRecipientsMessage = @json(__('Select at least one tenant to continue.'));
    const missingLineItemsForReviewMessage = @json(__('Add at least one line item before reviewing the draft.'));
    const endpointMissingMessage = @json(__('Unable to submit invoices because the endpoint is missing. Please refresh the page.'));
    const genericErrorMessage = @json(__('We could not save the invoices. Please review the inputs and try again.'));
  const successTemplate = @json(__('Created :count invoice(s): :numbers'));
  const invoicesSavedMessage = @json(__('Invoices were saved.'));

        const stepper = wizard.querySelector('[data-wizard-progress]');
        const panes = wizard.querySelectorAll('[data-wizard-pane]');
        const btnNext = wizard.querySelector('[data-wizard-next]');
        const btnPrev = wizard.querySelector('[data-wizard-prev]');
        const btnComplete = wizard.querySelector('[data-wizard-complete]');
        const progressBar = wizard.querySelector('[data-progress-bar]');
        const progressCopy = wizard.querySelector('[data-progress-copy]');
        const recipientList = wizard.querySelector('[data-summary-recipient-list]');
        const summaryRecipientCount = wizard.querySelector('[data-summary-recipient-count]');
        const summaryLineCount = wizard.querySelector('[data-summary-line-count]');
        const summaryPeriod = wizard.querySelector('[data-summary-period]');
        const summaryDue = wizard.querySelector('[data-summary-due]');
        const summaryTotal = wizard.querySelector('[data-summary-total]');
        const summaryLineItems = wizard.querySelector('[data-summary-line-items]');
        const tenantCheckboxes = wizard.querySelectorAll('input[data-tenant]');
        const invoicePeriod = wizard.querySelector('[data-invoice-period]');
        const invoiceDue = wizard.querySelector('[data-invoice-due]');
        const addItemButton = wizard.querySelector('[data-add-line-item]');
        const suggestedButtons = wizard.querySelectorAll('[data-suggested-item]');
  const lineItemsTable = wizard.querySelector('[data-line-items-table] tbody');
  const notesField = wizard.querySelector('[data-invoice-notes]');
        const alertsHost = wizard.querySelector('[data-wizard-alerts]');

        const showWizardAlert = (message, variant = 'warning') => {
          if (!alertsHost) {
            window.alert?.(message);
            return;
          }

          const id = `alert-${uuid()}`;
          const wrapper = document.createElement('div');
          wrapper.className = `alert alert-${variant} alert-dismissible fade show mb-0`;
          wrapper.role = 'alert';
          wrapper.id = id;
          wrapper.innerHTML = `
            <div>${escapeHtml(message)}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
          `;

          alertsHost.appendChild(wrapper);

          window.setTimeout(() => {
            if (wrapper && wrapper.parentElement) {
              wrapper.classList.remove('show');
              wrapper.addEventListener('transitionend', () => wrapper.remove(), { once: true });
            }
          }, 6000);
        };

        const canProceedFromCurrentStep = () => {
          switch (state.step) {
            case 1:
              return state.tenants.size > 0;
            case 2:
              return state.items.length > 0;
            default:
              return true;
          }
        };

        const updateActionStates = () => {
          if (!btnNext) {
            return;
          }
          const canProceed = canProceedFromCurrentStep();
          btnNext.dataset.wizardReady = canProceed ? 'true' : 'false';
          btnNext.classList.toggle('opacity-50', !canProceed);
          btnNext.setAttribute('aria-disabled', String(!canProceed));
        };

        if (notesField) {
          state.notes = notesField.value || '';
        }

        if (invoicePeriod?.value) {
          state.period = invoicePeriod.value;
        }

        if (invoiceDue?.value) {
          state.dueDate = invoiceDue.value;
        }

        const formatCurrency = (value) => {
          return new Intl.NumberFormat(undefined, { style: 'currency', currency: 'USD' }).format(value || 0);
        };

        const updateSummary = () => {
          // Recipients
          summaryRecipientCount.textContent = state.tenants.size.toString();
          recipientList.innerHTML = '';
          if (state.tenants.size === 0) {
            const li = document.createElement('li');
            li.className = 'text-secondary';
            li.textContent = @json(__('No schools selected yet.'));
            recipientList.appendChild(li);
          } else {
            state.tenants.forEach((meta) => {
              const li = document.createElement('li');
              const safeName = escapeHtml(meta.name ?? '');
              const safePlan = escapeHtml(meta.plan ?? '');
              li.innerHTML = `<span class="fw-semibold">${safeName}</span><br><span class="text-secondary">${safePlan}</span>`;
              recipientList.appendChild(li);
            });
          }

          // Period & due date
          summaryPeriod.textContent = formatPeriodLabel();
          summaryDue.textContent = formatDateDisplay(state.dueDate);

          // Line items
          summaryLineCount.textContent = state.items.length.toString();
          summaryLineItems.innerHTML = '';
          if (state.items.length === 0) {
            const li = document.createElement('li');
            li.className = 'text-secondary';
            li.textContent = @json(__('No line items yet. Add items in step 2.'));
            summaryLineItems.appendChild(li);
          } else {
            state.items.forEach((item) => {
              const li = document.createElement('li');
              const safeLabel = escapeHtml(item.label ?? '');
              li.innerHTML = `<span class="fw-semibold">${safeLabel}</span> · ${formatCurrency(item.total)}`;
              summaryLineItems.appendChild(li);
            });
          }

          const grandTotal = state.items.reduce((sum, item) => sum + item.total, 0);
          summaryTotal.textContent = formatCurrency(grandTotal);

          updateActionStates();
        };

        const renderLineItems = () => {
          lineItemsTable.innerHTML = '';
          if (state.items.length === 0) {
            const emptyRow = document.createElement('tr');
            const cell = document.createElement('td');
            cell.colSpan = 6;
            cell.className = 'text-center text-secondary py-4';
            cell.textContent = @json(__('No line items have been added yet.'));
            emptyRow.appendChild(cell);
            lineItemsTable.appendChild(emptyRow);
            return;
          }

          state.items.forEach((item) => {
            const row = document.createElement('tr');
            row.dataset.itemId = item.id;

            const safeLabel = escapeHtml(item.label ?? '');
            const safeCategory = escapeHtml(item.category ?? '');
            const safeDescription = escapeHtml(item.description ?? '');
            const quantity = Number.isFinite(Number(item.quantity)) ? Number(item.quantity) : 1;
            const unitPrice = Number.isFinite(Number(item.unitPrice)) ? Number(item.unitPrice) : 0;
            item.total = quantity * unitPrice;

            row.innerHTML = `
              <td>
                <input type="text" class="form-control form-control-sm" value="${safeLabel}" data-item-field="label" />
                <div class="form-text">${safeDescription}</div>
              </td>
              <td><input type="text" class="form-control form-control-sm" value="${safeCategory}" data-item-field="category" /></td>
              <td class="text-end"><input type="number" min="1" step="1" class="form-control form-control-sm text-end" value="${quantity}" data-item-field="quantity" /></td>
              <td class="text-end"><input type="number" min="0" step="0.01" class="form-control form-control-sm text-end" value="${unitPrice}" data-item-field="unitPrice" /></td>
              <td class="text-end fw-semibold" data-item-total>${formatCurrency(item.total)}</td>
              <td class="text-end">
                <button type="button" class="btn btn-outline-danger btn-sm" data-remove-item>
                  <span class="bi bi-trash"></span>
                </button>
              </td>
            `;
            lineItemsTable.appendChild(row);
          });
        };

        const updateStepVisibility = () => {
          panes.forEach((pane) => {
            pane.classList.toggle('d-none', Number(pane.dataset.wizardPane) !== state.step);
          });

          if (stepper) {
            stepper.querySelectorAll('.billing-wizard-step').forEach((stepEl) => {
              const stepIndex = Number(stepEl.dataset.step);
              stepEl.classList.toggle('is-active', stepIndex === state.step);
              stepEl.classList.toggle('is-completed', stepIndex < state.step);
            });
          }

          btnPrev.classList.toggle('disabled', state.step === 1);
          btnPrev.disabled = state.step === 1;

          if (state.step === 3) {
            btnNext.classList.add('d-none');
            btnComplete.classList.remove('d-none');
          } else {
            btnNext.classList.remove('d-none');
            btnComplete.classList.add('d-none');
          }

          const progressPercent = (state.step / 3) * 100;
          if (progressBar) {
            progressBar.style.width = `${progressPercent}%`;
          }
          if (progressCopy) {
            progressCopy.textContent = `Step ${state.step} of 3`;
          }

          updateActionStates();
        };

        tenantCheckboxes.forEach((checkbox) => {
          checkbox.addEventListener('change', (event) => {
            const target = event.currentTarget;
            const tenantId = target.value;
            if (target.checked) {
              state.tenants.set(tenantId, {
                id: tenantId,
                name: target.dataset.tenantName,
                plan: target.dataset.tenantPlan,
              });
            } else {
              state.tenants.delete(tenantId);
            }
            updateSummary();
          });
        });

        invoicePeriod?.addEventListener('change', (event) => {
          state.period = event.target.value;
          updateSummary();
        });

        invoiceDue?.addEventListener('change', (event) => {
          state.dueDate = event.target.value;
          updateSummary();
        });

        notesField?.addEventListener('input', (event) => {
          state.notes = event.target.value;
        });

        addItemButton?.addEventListener('click', () => {
          const id = uuid();
          state.items.push({
            id,
            label: '',
            description: '',
            category: '',
            quantity: 1,
            unitPrice: 0,
            total: 0,
          });
          renderLineItems();
          updateSummary();
        });

        suggestedButtons.forEach((button) => {
          button.addEventListener('click', () => {
            const id = uuid();
            const label = button.dataset.label || '';
            const description = button.dataset.description || '';
            const amount = Number(button.dataset.amount) || 0;
            const category = button.dataset.category || '';
            state.items.push({
              id,
              label,
              description,
              category,
              quantity: 1,
              unitPrice: amount,
              total: amount,
            });
            renderLineItems();
            updateSummary();
          });
        });

        lineItemsTable?.addEventListener('input', (event) => {
          const field = event.target.dataset.itemField;
          if (!field) {
            return;
          }
          const row = event.target.closest('tr');
          const id = row?.dataset.itemId;
          if (!id) {
            return;
          }
          const item = state.items.find((entry) => entry.id === id);
          if (!item) {
            return;
          }

          const value = event.target.value;
          if (field === 'quantity' || field === 'unitPrice') {
            item[field] = Number(value);
            item.total = (Number(item.quantity) || 0) * (Number(item.unitPrice) || 0);
            row.querySelector('[data-item-total]').textContent = formatCurrency(item.total);
          } else if (field === 'label') {
            item.label = value;
          } else if (field === 'category') {
            item.category = value;
          }

          updateSummary();
        });

        lineItemsTable?.addEventListener('click', (event) => {
          if (!event.target.closest('[data-remove-item]')) {
            return;
          }
          const row = event.target.closest('tr');
          const id = row?.dataset.itemId;
          if (!id) {
            return;
          }
          state.items = state.items.filter((item) => item.id !== id);
          renderLineItems();
          updateSummary();
        });

        const submitInvoice = async () => {
          if (!storeUrl) {
            showWizardAlert(endpointMissingMessage, 'danger');
            return;
          }

          if (state.items.length === 0) {
            showWizardAlert(missingItemsMessage, 'warning');
            return;
          }

          const tenants = Array.from(state.tenants.keys());
          const { start, end } = computePeriodRange();
          const payload = {
            tenant_ids: tenants.filter((id) => typeof id === 'string' && id.length > 0),
            period: {
              start: start ?? null,
              end: end ?? null,
              due: state.dueDate || null,
            },
            notes: state.notes || null,
            items: state.items.map((item) => ({
              description: item.label || item.description || defaultLineDescription,
              category: item.category || null,
              quantity: Number(item.quantity) || 1,
              unit_price: Number(item.unitPrice) || 0,
            })),
          };

          btnComplete?.classList.add('disabled');
          btnComplete && (btnComplete.disabled = true);

          try {
            const response = await fetch(storeUrl, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
              },
              body: JSON.stringify(payload),
            });

            if (!response.ok) {
              const errorPayload = await response.json().catch(() => null);
              let message = genericErrorMessage;
              if (errorPayload) {
                if (errorPayload.message) {
                  message = errorPayload.message;
                }
                if (errorPayload.errors) {
                  const firstError = Object.values(errorPayload.errors).flat().find(Boolean);
                  if (firstError) {
                    message = firstError;
                  }
                }
              }
              showWizardAlert(message, 'danger');
              return;
            }

            const data = await response.json();
            const createdList = Array.isArray(data.created) ? data.created.join(', ') : '';
            const successMessage = data.count
              ? successTemplate.replace(':count', data.count).replace(':numbers', createdList || '—')
              : invoicesSavedMessage;
            showWizardAlert(successMessage, 'success');
            window.location.reload();
          } catch (error) {
            console.error('Invoice submission failed', error);
            showWizardAlert(genericErrorMessage, 'danger');
          } finally {
            btnComplete?.classList.remove('disabled');
            btnComplete && (btnComplete.disabled = false);
          }
        };

        btnNext?.addEventListener('click', () => {
          if (!canProceedFromCurrentStep()) {
            const message = state.step === 1 ? missingRecipientsMessage : missingLineItemsForReviewMessage;
            showWizardAlert(message, 'warning');
            return;
          }

          if (state.step < 3) {
            state.step += 1;
            updateStepVisibility();
          }
        });

        btnPrev?.addEventListener('click', () => {
          if (state.step > 1) {
            state.step -= 1;
            updateStepVisibility();
          }
        });

        btnComplete?.addEventListener('click', () => {
          submitInvoice();
        });

        updateSummary();
        renderLineItems();
        updateStepVisibility();
      });
    </script>
  @endpush
@endsection
