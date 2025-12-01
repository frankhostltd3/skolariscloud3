@php
    $invoice = $invoice ?? null;
    $policy = $policy ?? null;
    $action = $action ?? 'warning';
    $templates = (array) ($policy->templates ?? []);

    $subjectKey = $action . '_subject';
    $bodyKey = $action . '_body';

    $defaults = [
        'warning_subject' => __('Invoice :number is due on :date', [
            'number' => $invoice?->invoice_number,
            'date' => optional($invoice?->due_at)->toDateString(),
        ]),
        'warning_body' =>
            "Hello {{ tenant_name }},\n\nThis is a reminder that invoice {{ invoice_number }} for {{ amount }} is due on {{ due_date }}.\n\nYou can pay securely here: {{ pay_url }}\n\nThank you,",
        'suspension_subject' => __('Service suspended due to overdue invoice :number', [
            'number' => $invoice?->invoice_number,
        ]),
        'suspension_body' =>
            "Hello {{ tenant_name }},\n\nYour service has been temporarily suspended due to invoice {{ invoice_number }} overdue by {{ days_overdue }} days.\nPlease make a payment here: {{ pay_url }}\n\nIf you already paid, please ignore this message.\n\nThank you,",
        'termination_subject' => __('Service termination due to non-payment'),
        'termination_body' =>
            "Hello {{ tenant_name }},\n\nWe regret to inform you that your service will be terminated on {{ termination_date }} due to non-payment.\n\nIf this is an error or you need help, contact support.\n\nRegards,",
    ];

    $subjectTpl = $templates[$subjectKey] ?? $defaults[$subjectKey];
    $bodyTpl = $templates[$bodyKey] ?? $defaults[$bodyKey];

    $vars = [
        '{{ tenant_name }}' => e($invoice?->tenant_name_snapshot ?? ($invoice?->tenant_id ?? __('Tenant'))),
        '{{ invoice_number }}' => e($invoice?->invoice_number ?? ''),
        '{{ amount }}' => number_format((float) ($invoice?->total ?? 0), 2),
        '{{ due_date }}' => e(optional($invoice?->due_at)->toDateString() ?? ''),
        '{{ days_overdue }}' => max(
            0,
            now()
                ->startOfDay()
                ->diffInDays(optional($invoice?->due_at)?->startOfDay() ?? now(), false) * -1,
        ),
        '{{ pay_url }}' => route('landlord.billing.invoices.show', $invoice?->getKey() ?? 0),
        '{{ termination_date }}' => e(
            now()
                ->addDays($policy?->termination_grace_days ?? 30)
                ->toDateString(),
        ),
    ];

    $subject = strtr($subjectTpl, $vars);
    $body = strtr($bodyTpl, $vars);
@endphp

@component('mail::message')
    # {{ $subject }}

    {!! nl2br(e($body)) !!}
@endcomponent
