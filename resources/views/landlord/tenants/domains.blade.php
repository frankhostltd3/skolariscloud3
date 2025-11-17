@extends('landlord.layouts.app')

@section('content')
<div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-5">
  <div>
    <span class="badge text-bg-primary-subtle text-primary-emphasis px-3 py-2 mb-3">{{ __('Domain management') }}</span>
    <h1 class="h3 fw-semibold mb-2">{{ __('Add and manage custom domains for tenants') }}</h1>
    <p class="text-secondary mb-0">{{ __('Provision branded domains, configure DNS, and submit purchase requests on behalf of tenants.') }}</p>
  </div>
  <div class="text-secondary small">
    <div class="fw-semibold text-body">{{ __('Live support window') }}</div>
    <div>{{ __('Mon–Fri, 8:00–18:00 EAT') }}</div>
  </div>
</div>

<div class="row g-4">
  <div class="col-12 col-xl-8">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-white border-0">
        <div class="domain-wizard-stepper" aria-label="{{ __('Domain purchase steps') }}">
          <div class="domain-wizard-step" data-step="1">
            <span class="domain-wizard-step__index">1</span>
            <div>
              <div class="fw-semibold">{{ __('Domain selection') }}</div>
              <small class="text-secondary">{{ __('Search availability and choose billing cycle') }}</small>
            </div>
          </div>
          <div class="domain-wizard-step" data-step="2">
            <span class="domain-wizard-step__index">2</span>
            <div>
              <div class="fw-semibold">{{ __('Tenant linking & DNS') }}</div>
              <small class="text-secondary">{{ __('Attach tenant, provide contact, preview DNS records') }}</small>
            </div>
          </div>
          <div class="domain-wizard-step" data-step="3">
            <span class="domain-wizard-step__index">3</span>
            <div>
              <div class="fw-semibold">{{ __('Checkout & submit') }}</div>
              <small class="text-secondary">{{ __('Confirm order, select payment, agree to terms') }}</small>
            </div>
          </div>
        </div>
      </div>

      <div class="card-body p-4 p-lg-5">
        <form id="domain-wizard" class="domain-wizard" method="POST" action="#" novalidate>
          @csrf

          <section class="domain-wizard-pane" data-step="1">
            <div class="mb-4">
              <h2 class="h5 fw-semibold mb-2">{{ __('What type of domain would you like to provision?') }}</h2>
              <p class="text-secondary mb-0">{{ __('Use subdomains for quick onboarding or purchase a fully custom domain to match the school brand.') }}</p>
            </div>

            <div class="row g-3 mb-4">
              <div class="col-md-6">
                <label class="domain-option-card">
                  <input type="radio" name="domain_type" value="subdomain" class="form-check-input" checked>
                  <div class="domain-option-card__body">
                    <span class="domain-option-card__icon bi bi-diagram-3"></span>
                    <h3 class="h6 fw-semibold mb-1">{{ __('Managed subdomain') }}</h3>
                    <p class="text-secondary small mb-0">{{ __('Launch instantly with *.skolariscloud.com addresses. SSL, routing, and renewals handled for you.') }}</p>
                    <div class="domain-option-card__price mt-3">
                      <span class="fw-semibold text-body">{{ __('Included') }}</span>
                      <small class="text-secondary">{{ __('with subscription') }}</small>
                    </div>
                  </div>
                </label>
              </div>
              <div class="col-md-6">
                <label class="domain-option-card">
                  <input type="radio" name="domain_type" value="custom" class="form-check-input">
                  <div class="domain-option-card__body">
                    <span class="domain-option-card__icon bi bi-globe"></span>
                    <h3 class="h6 fw-semibold mb-1">{{ __('Custom domain purchase') }}</h3>
                    <p class="text-secondary small mb-0">{{ __('Register or transfer a personalised domain and keep billing consolidated under your landlord account.') }}</p>
                    <div class="domain-option-card__price mt-3">
                      <span class="fw-semibold text-body">$19.00</span>
                      <small class="text-secondary">{{ __('per year (example)') }}</small>
                    </div>
                  </div>
                </label>
              </div>
            </div>

            <div class="row g-3 align-items-end">
              <div class="col-md-7">
                <label for="desired-domain" class="form-label">{{ __('Desired domain name') }}</label>
                <div class="input-group">
                  <input type="text" id="desired-domain" name="desired_domain" class="form-control" placeholder="example" required>
                  <select name="tld" class="form-select" aria-label="{{ __('Top level domain') }}">
                    <option value=".com">.com</option>
                    <option value=".school">.school</option>
                    <option value=".academy">.academy</option>
                    <option value=".org">.org</option>
                    <option value=".co.ke">.co.ke</option>
                  </select>
                </div>
                <small class="text-secondary d-block mt-2">{{ __('Search results update instantly – we’ll reserve the domain for 15 minutes after checkout.') }}</small>
              </div>
              <div class="col-md-5">
                <label class="form-label">{{ __('Billing cycle') }}</label>
                <select name="billing_cycle" class="form-select">
                  <option value="annual" selected>{{ __('Annual (best value)') }}</option>
                  <option value="biennial">{{ __('2 years (save 5%)') }}</option>
                  <option value="triennial">{{ __('3 years (save 8%)') }}</option>
                </select>
              </div>
            </div>

            <div class="alert alert-info mt-4 mb-0" role="status">
              <div class="d-flex align-items-start gap-3">
                <span class="bi bi-lightning-charge-fill fs-4 text-primary"></span>
                <div>
                  <div class="fw-semibold mb-1">{{ __('Automatic SSL & tenancy routing') }}</div>
                  <p class="mb-0 small">{{ __('We will automatically provision certificates, CDN caching, and tenant routing rules once the domain goes live.') }}</p>
                </div>
              </div>
            </div>
          </section>

          <section class="domain-wizard-pane" data-step="2" hidden>
            <div class="mb-4">
              <h2 class="h5 fw-semibold mb-2">{{ __('Link to tenant and capture contact details') }}</h2>
              <p class="text-secondary mb-0">{{ __('Specify which school will use this domain and who will receive DNS verification emails.') }}</p>
            </div>

            <div class="row g-3">
              <div class="col-12">
                <label class="form-label" for="tenant-id">{{ __('Select tenant') }}</label>
                <select id="tenant-id" name="tenant_id" class="form-select" required>
                  <option value="" selected disabled>{{ __('Choose tenant school') }}</option>
                  @forelse ($tenants as $tenant)
                    <option value="{{ $tenant['id'] }}">{{ $tenant['name'] }} @if($tenant['admin_email']) — {{ $tenant['admin_email'] }} @endif</option>
                  @empty
                    <option value="" disabled>{{ __('No tenants available yet') }}</option>
                  @endforelse
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label" for="contact-name">{{ __('Primary contact name') }}</label>
                <input type="text" class="form-control" id="contact-name" name="contact_name" placeholder="{{ __('e.g. Jane Okoth') }}" required>
              </div>
              <div class="col-md-6">
                <label class="form-label" for="contact-email">{{ __('Primary contact email') }}</label>
                <input type="email" class="form-control" id="contact-email" name="contact_email" placeholder="contact@school.com" required>
              </div>
              <div class="col-md-6">
                <label class="form-label" for="contact-phone">{{ __('Phone / WhatsApp number') }}</label>
                <input type="tel" class="form-control" id="contact-phone" name="contact_phone" placeholder="+254 700 000000">
              </div>
              <div class="col-md-6">
                <label class="form-label" for="dns-assignee">{{ __('Who will configure DNS?') }}</label>
                <select id="dns-assignee" name="dns_assignee" class="form-select">
                  <option value="skolaris" selected>{{ __('Skolaris infrastructure team') }}</option>
                  <option value="customer">{{ __('Customer IT team') }}</option>
                  <option value="third-party">{{ __('Third-party provider') }}</option>
                </select>
              </div>
            </div>

            <div class="card border border-dashed rounded-4 mt-4">
              <div class="card-body">
                <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                  <div>
                    <h3 class="h6 fw-semibold mb-1">{{ __('DNS records preview (generated)') }}</h3>
                    <p class="text-secondary small mb-0">{{ __('Share these with the person managing the domain. Records activate once the order is approved.') }}</p>
                  </div>
                  <button type="button" class="btn btn-outline-secondary btn-sm">{{ __('Copy records') }}</button>
                </div>

                <div class="table-responsive mt-3">
                  <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                      <tr>
                        <th scope="col">{{ __('Type') }}</th>
                        <th scope="col">{{ __('Host') }}</th>
                        <th scope="col">{{ __('Value / Target') }}</th>
                        <th scope="col">{{ __('TTL') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td><span class="badge text-bg-primary-subtle text-primary-emphasis">CNAME</span></td>
                        <td>@</td>
                        <td>schools.skolariscloud.com</td>
                        <td>300</td>
                      </tr>
                      <tr>
                                                <td><span class="badge text-bg-primary-subtle text-primary-emphasis">TXT</span></td>
                                                <td>@</td>
                                                <td>tenant-verification={{ \Illuminate\Support\Str::uuid() }}</td>
                        <td>3600</td>
                      </tr>
                      <tr>
                        <td><span class="badge text-bg-primary-subtle text-primary-emphasis">CNAME</span></td>
                        <td>www</td>
                        <td>schools.skolariscloud.com</td>
                        <td>300</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </section>

          <section class="domain-wizard-pane" data-step="3" hidden>
            <div class="mb-4">
              <h2 class="h5 fw-semibold mb-2">{{ __('Review order and submit request') }}</h2>
              <p class="text-secondary mb-0">{{ __('Confirm billing details, add purchase notes, and authorise payment routing.') }}</p>
            </div>

            <div class="row g-3">
              <div class="col-lg-6">
                <div class="order-summary-card">
                  <div class="order-summary-card__header">
                    <span class="bi bi-receipt"></span>
                    <h3 class="h6 fw-semibold mb-0">{{ __('Order summary') }}</h3>
                  </div>
                  <dl class="order-summary-card__list">
                    <div class="order-summary-card__row">
                      <dt>{{ __('Domain') }}</dt>
                      <dd data-order-domain>—</dd>
                    </div>
                    <div class="order-summary-card__row">
                      <dt>{{ __('Term') }}</dt>
                      <dd data-order-term>{{ __('Annual') }}</dd>
                    </div>
                    <div class="order-summary-card__row">
                      <dt>{{ __('Tenant') }}</dt>
                      <dd data-order-tenant>—</dd>
                    </div>
                  </dl>
                  <div class="order-summary-card__total mt-3">
                    <span>{{ __('Estimated total') }}</span>
                    <strong data-order-total>$19.00</strong>
                  </div>
                  <small class="text-secondary d-block mt-2">{{ __('Final pricing confirmed by support before charge is applied.') }}</small>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="row g-3">
                  <div class="col-12">
                    <label class="form-label" for="billing-entity">{{ __('Billing entity / organisation') }}</label>
                    <input type="text" class="form-control" id="billing-entity" name="billing_entity" placeholder="{{ __('Skolaris Cloud Ltd.') }}" required>
                  </div>
                  <div class="col-12">
                    <label class="form-label" for="payment-method">{{ __('Preferred payment channel') }}</label>
                    <select id="payment-method" name="payment_method" class="form-select">
                      <option value="mpesa" selected>{{ __('M-Pesa PayBill') }}</option>
                      <option value="card">{{ __('Card on file') }}</option>
                      <option value="invoice">{{ __('Invoice (Net 15)') }}</option>
                    </select>
                  </div>
                  <div class="col-12">
                    <label class="form-label" for="purchase-notes">{{ __('Purchase notes') }}</label>
                    <textarea id="purchase-notes" name="purchase_notes" class="form-control" rows="3" placeholder="{{ __('E.g. include wildcard SSL, coordinate go-live during school holidays...') }}"></textarea>
                  </div>
                  <div class="col-12">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" value="1" id="terms" required>
                      <label class="form-check-label" for="terms">
                        {!! __('I authorise Skolaris to purchase and renew this domain on behalf of the tenant and agree to the <a href=":url" target="_blank">Domain Services Terms</a>.', ['url' => route('landing', ['locale' => app()->getLocale()]) . '#terms']) !!}
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>

          <div class="domain-wizard-actions">
            <button type="button" class="btn btn-outline-secondary domain-wizard-prev" disabled>
              <span class="bi bi-arrow-left-short"></span>{{ __('Back') }}
            </button>
            <div class="ms-auto d-flex gap-2">
              <button type="button" class="btn btn-outline-primary domain-wizard-save" data-bs-toggle="tooltip" title="{{ __('Save current progress for later') }}">
                <span class="bi bi-save"></span>
              </button>
              <button type="button" class="btn btn-primary domain-wizard-next">
                {{ __('Continue') }}<span class="bi bi-arrow-right-short"></span>
              </button>
              <button type="submit" class="btn btn-success domain-wizard-submit d-none">
                <span class="bi bi-check2"></span>{{ __('Submit request') }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-12 col-xl-4">
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header bg-white border-0 pb-0">
        <h2 class="h6 fw-semibold mb-0">{{ __('What happens after submission?') }}</h2>
      </div>
      <div class="card-body">
        <ol class="domain-timeline">
          <li>
            <span class="domain-timeline__icon bi bi-search"></span>
            <div>
              <strong>{{ __('Order review (within 2 hours)') }}</strong>
              <p class="text-secondary small mb-0">{{ __('We verify availability, confirm total pricing, and reserve the domain immediately.') }}</p>
            </div>
          </li>
          <li>
            <span class="domain-timeline__icon bi bi-link-45deg"></span>
            <div>
              <strong>{{ __('DNS coordination (within 24 hours)') }}</strong>
              <p class="text-secondary small mb-0">{{ __('We guide the nominated contact through DNS changes or handle it entirely if delegated to us.') }}</p>
            </div>
          </li>
          <li>
            <span class="domain-timeline__icon bi bi-shield-check"></span>
            <div>
              <strong>{{ __('Go-live and monitoring') }}</strong>
              <p class="text-secondary small mb-0">{{ __('SSL, routing, uptime checks, and renewal reminders are automated from your landlord console.') }}</p>
            </div>
          </li>
        </ol>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <h2 class="h6 fw-semibold mb-3">{{ __('Need help?') }}</h2>
        <p class="text-secondary small mb-3">{{ __('Speak with our domain onboarding specialists for DNS assistance or bulk domain purchases.') }}</p>
        <div class="d-flex flex-column gap-2">
          <a href="mailto:domains@skolariscloud.com" class="btn btn-outline-primary btn-sm"><span class="bi bi-envelope me-2"></span>{{ __('Email domains team') }}</a>
          <a href="https://cal.com/skolaris/domains" target="_blank" class="btn btn-outline-secondary btn-sm"><span class="bi bi-calendar-event me-2"></span>{{ __('Schedule a call') }}</a>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const wizard = document.querySelector('#domain-wizard');
      if (! wizard) {
        return;
      }

      const panes = [...wizard.querySelectorAll('.domain-wizard-pane')];
      const stepper = document.querySelectorAll('.domain-wizard-step');
      const prevBtn = wizard.querySelector('.domain-wizard-prev');
      const nextBtn = wizard.querySelector('.domain-wizard-next');
      const submitBtn = wizard.querySelector('.domain-wizard-submit');
      const domainInput = wizard.querySelector('#desired-domain');
      const tldSelect = wizard.querySelector('select[name="tld"]');
      const tenantSelect = wizard.querySelector('#tenant-id');
      const orderDomain = wizard.querySelector('[data-order-domain]');
      const orderTenant = wizard.querySelector('[data-order-tenant]');
      const orderTerm = wizard.querySelector('[data-order-term]');
      const billingSelect = wizard.querySelector('select[name="billing_cycle"]');

      let currentStep = 1;

      const updateStepper = () => {
        stepper.forEach((element) => {
          const step = Number(element.dataset.step);
          element.classList.toggle('is-active', step === currentStep);
          element.classList.toggle('is-completed', step < currentStep);
        });

        panes.forEach((pane) => {
          const step = Number(pane.dataset.step);
          pane.hidden = step !== currentStep;
        });

        prevBtn.disabled = currentStep === 1;
        nextBtn.classList.toggle('d-none', currentStep === panes.length);
        submitBtn.classList.toggle('d-none', currentStep !== panes.length);
      };

      const updateSummary = () => {
        if (orderDomain) {
          const domainValue = [domainInput.value.trim(), tldSelect.value].filter(Boolean).join('');
          orderDomain.textContent = domainValue || '—';
        }
        if (orderTenant) {
          orderTenant.textContent = tenantSelect.options[tenantSelect.selectedIndex]?.text ?? '—';
        }
        if (orderTerm) {
          orderTerm.textContent = billingSelect.options[billingSelect.selectedIndex]?.text ?? '—';
        }
      };

      const validateStep = () => {
        const pane = panes.find(p => Number(p.dataset.step) === currentStep);
        if (! pane) return true;

        const inputs = [...pane.querySelectorAll('input, select, textarea')].filter(el => el.hasAttribute('required'));
        let valid = true;

        inputs.forEach((input) => {
          if (! input.checkValidity()) {
            input.classList.add('is-invalid');
            valid = false;
          } else {
            input.classList.remove('is-invalid');
          }
        });

        return valid;
      };

      nextBtn.addEventListener('click', () => {
        if (! validateStep()) {
          return;
        }

        if (currentStep < panes.length) {
          currentStep += 1;
          updateSummary();
          updateStepper();
        }
      });

      prevBtn.addEventListener('click', () => {
        if (currentStep > 1) {
          currentStep -= 1;
          updateStepper();
        }
      });

      wizard.addEventListener('submit', (event) => {
        if (! wizard.checkValidity()) {
          event.preventDefault();
          wizard.classList.add('was-validated');
        }
      });

      billingSelect?.addEventListener('change', updateSummary);
      tenantSelect?.addEventListener('change', updateSummary);
      domainInput?.addEventListener('input', updateSummary);
      tldSelect?.addEventListener('change', updateSummary);

      updateSummary();
      updateStepper();
    });
  </script>
@endpush
@endsection
