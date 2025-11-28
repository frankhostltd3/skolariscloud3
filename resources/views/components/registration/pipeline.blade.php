@props([
    'context' => 'default',
    'stages' => [],
    'summary' => [],
    'mode' => null,
    'nextAction' => null,
    'title' => null,
    'subtitle' => null,
])

@php
    $resolvedTitle =
        $title ??
        ([
            'admin' => __('Enrollment & approval pipeline'),
            'teacher' => __('New students headed to your classes'),
            'student' => __('Your registration progress'),
            'parent' => __('Family registration status'),
            'landing' => __('Enrollment pipeline, live'),
        ][$context] ??
            __('Registration pipeline'));

    $resolvedSubtitle =
        $subtitle ??
        ([
            'admin' => __('Track every step from application to class placement.'),
            'teacher' => __('See which learners need attention before you welcome them.'),
            'student' => __('Four quick steps before you are fully onboarded.'),
            'parent' => __('Where each child is in the school admissions flow.'),
            'landing' => __('Families apply, admins approve, and placements happen fast.'),
        ][$context] ??
            __('Monitor application review, verification, and placement.'));

    $normalizedStages = collect($stages)->values();
    $summary = collect($summary)->values();
@endphp

@once
    <style>
        .registration-pipeline-card .registration-node {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            border: 2px solid var(--bs-border-color, #dee2e6);
            display: grid;
            place-items: center;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .registration-node.is-complete {
            border-color: var(--bs-success, #198754);
            background-color: rgba(25, 135, 84, 0.15);
            color: var(--bs-success, #198754);
        }

        .registration-node.is-current {
            border-color: var(--bs-primary, #0d6efd);
            background-color: rgba(13, 110, 253, 0.1);
            color: var(--bs-primary, #0d6efd);
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
        }

        .registration-node.is-upcoming {
            background-color: var(--bs-body-bg, #fff);
            color: var(--bs-secondary-color, #6c757d);
        }

        .registration-node.is-blocked {
            border-color: var(--bs-danger, #dc3545);
            color: var(--bs-danger, #dc3545);
        }

        .registration-pipeline-connector {
            flex: 1;
            height: 2px;
            background: linear-gradient(90deg, rgba(108, 117, 125, 0.25) 0%, rgba(108, 117, 125, 0.1) 100%);
            align-self: center;
        }
    </style>
@endonce

<div class="card registration-pipeline-card mb-4 border-0 shadow-sm">
    <div class="card-header bg-white border-0 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h5 class="mb-1">{{ $resolvedTitle }}</h5>
            <p class="mb-0 text-muted small">{{ $resolvedSubtitle }}</p>
        </div>
        @if ($mode)
            <span
                class="badge text-bg-dark-subtle text-dark">{{ __('Approval mode: :mode', ['mode' => str_replace('_', ' ', ucfirst($mode))]) }}</span>
        @endif
    </div>
    <div class="card-body">
        <div class="d-flex flex-column flex-lg-row align-items-stretch gap-3">
            @foreach ($normalizedStages as $stage)
                <div class="d-flex flex-column align-items-center text-center flex-fill px-2">
                    <div class="registration-node is-{{ $stage['status'] ?? 'upcoming' }}">
                        {{ $loop->iteration }}
                    </div>
                    <div class="fw-semibold mt-2">{{ $stage['label'] ?? __('Stage') }}</div>
                    @if (!empty($stage['primary']))
                        <div class="fs-5 fw-bold">{{ $stage['primary'] }}</div>
                    @endif
                    @if (!empty($stage['description']))
                        <small class="text-muted">{{ $stage['description'] }}</small>
                    @endif
                </div>

                @if (!$loop->last)
                    <div class="registration-pipeline-connector d-none d-lg-block"></div>
                @endif
            @endforeach
        </div>

        @if ($summary->isNotEmpty())
            <div class="row g-3 mt-4">
                @foreach ($summary as $item)
                    <div class="col-12 col-md-4">
                        <div class="border rounded-4 p-3 h-100">
                            <div class="text-muted small">{{ $item['label'] ?? '' }}</div>
                            <div class="fs-4 fw-semibold">{{ $item['value'] ?? '—' }}</div>
                            @if (!empty($item['hint']))
                                <small class="text-muted">{{ $item['hint'] }}</small>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        @if ($nextAction)
            <div class="alert alert-info mt-4 mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-lightbulb"></i>
                <div>
                    <strong>{{ __('Next action') }}:</strong> {{ $nextAction }}
                </div>
            </div>
        @endif
    </div>
</div>
