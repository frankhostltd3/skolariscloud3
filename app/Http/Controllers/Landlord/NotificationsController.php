<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\LandlordNotification;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use App\Notifications\Channels\SmsChannel; // registered in AppServiceProvider
use App\Notifications\Channels\WebhookChannel; // ditto
use Illuminate\Support\Str;

class NotificationsController extends Controller
{
    public function index(Request $request): View
    {
        $query = LandlordNotification::query();
        if ($channel = trim((string) $request->get('channel'))) {
            $query->where('channel', $channel);
        }
        if ($status = trim((string) $request->get('status'))) {
            if ($status === 'scheduled') { $query->whereNull('sent_at')->whereNotNull('scheduled_at'); }
            elseif ($status === 'sent') { $query->whereNotNull('sent_at'); }
            elseif ($status === 'draft') { $query->whereNull('sent_at')->whereNull('scheduled_at'); }
        }
        $notifications = $query->with('creator')->latest()->paginate(15)->withQueryString();

        return view('landlord.notifications.index', [
            'notifications' => $notifications,
        ]);
    }

    public function create(): View
    {
        return view('landlord.notifications.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required','string','max:255'],
            'message' => ['required','string'],
            'channel' => ['required','in:system,email,sms,slack,webhook'],
            'audience' => ['nullable','array'],
            'audience.plans' => ['nullable','array'],
            'audience.countries' => ['nullable','array'],
            'audience.landlord_roles' => ['nullable','array'],
            'scheduled_at' => ['nullable','date'],
        ]);

        $notification = new LandlordNotification($data);
        $notification->created_by = auth('landlord')->id();
        $notification->save();

        return redirect()->route('landlord.notifications.edit', $notification)->with('success', __('Notification created'));
    }

    public function edit(LandlordNotification $notification): View
    {
        return view('landlord.notifications.edit', [ 'notification' => $notification ]);
    }

    public function update(Request $request, LandlordNotification $notification): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required','string','max:255'],
            'message' => ['required','string'],
            'channel' => ['required','in:system,email,sms,slack,webhook'],
            'audience' => ['nullable','array'],
            'audience.plans' => ['nullable','array'],
            'audience.countries' => ['nullable','array'],
            'audience.landlord_roles' => ['nullable','array'],
            'scheduled_at' => ['nullable','date'],
        ]);

        $notification->fill($data)->save();

        return redirect()->route('landlord.notifications.edit', $notification)->with('success', __('Notification updated'));
    }

    public function destroy(LandlordNotification $notification): RedirectResponse
    {
        $notification->delete();
        return redirect()->route('landlord.notifications.index')->with('success', __('Notification deleted'));
    }

    public function dispatchNow(Request $request, LandlordNotification $notification): RedirectResponse
    {
        // Resolve audience of recipients
        $audience = $notification->audience ?? [];
        $plans = (array) ($audience['plans'] ?? []);
        $countries = (array) ($audience['countries'] ?? []);
        $landlordRoles = (array) ($audience['landlord_roles'] ?? []);

        $tenantsQuery = \Stancl\Tenancy\Database\Models\Tenant::query();
        if (!empty($plans)) {
            $tenantsQuery->whereRaw("JSON_EXTRACT(data, '$.plan') IN (" . implode(',', array_fill(0, count($plans), '?')) . ")", $plans);
        }
        if (!empty($countries)) {
            $tenantsQuery->whereRaw("JSON_EXTRACT(data, '$.country') IN (" . implode(',', array_fill(0, count($countries), '?')) . ")", $countries);
        }
        $tenants = $tenantsQuery->get();

        $channel = $notification->channel;
        $payload = [ 'title' => $notification->title, 'message' => $notification->message ];

        // Dispatch to audience
        $sentCount = 0;
        if ($channel === 'email') {
            foreach ($tenants as $tenant) {
                $email = $tenant->data['contact_email'] ?? $tenant->data['admin_email'] ?? null;
                if ($email) {
                    NotificationFacade::route('mail', $email)->notify(new \App\Notifications\GenericLandlordMessage($payload));
                    $sentCount++;
                }
            }
        } elseif ($channel === 'sms') {
            foreach ($tenants as $tenant) {
                $phones = (array) ($tenant->data['phones'] ?? []);
                foreach ($phones as $phone) {
                    NotificationFacade::route(SmsChannel::class, $phone)->notify(new \App\Notifications\GenericLandlordMessage($payload));
                    $sentCount++;
                }
            }
        } elseif ($channel === 'slack') {
            $webhook = config('services.slack.webhook');
            if ($webhook) {
                NotificationFacade::route('slack', $webhook)->notify(new \App\Notifications\GenericLandlordMessage($payload));
                $sentCount++;
            }
        } elseif ($channel === 'webhook') {
            $url = config('services.notifications.webhook');
            if ($url) {
                NotificationFacade::route(WebhookChannel::class, $url)->notify(new \App\Notifications\GenericLandlordMessage($payload));
                $sentCount++;
            }
        } else {
            // system: log to audit only
        }

        $notification->forceFill([
            'sent_at' => now(),
            'meta' => array_merge($notification->meta ?? [], ['sent_count' => $sentCount]),
        ])->save();

        return redirect()->route('landlord.notifications.index')->with('success', __('Notification dispatched')); 
    }
}
