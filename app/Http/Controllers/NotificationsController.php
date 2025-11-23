<?php

namespace App\Http\Controllers;

use App\Enums\NotificationStatus;
use App\Mail\BaseMailable;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    public function notificationSend(Request $request)
    {
        $valid = $request->validate([
            'email' => 'required|email',
            'subject' => 'required|string',
            'bcc' => 'nullable|string',
            'cc' => 'nullable|string',
            'reply_to' => 'nullable|string',
            'type' => 'required|string'
        ]);

        $blocks = [
            'type' => 'text',
            'content' => 'Esto es un asalto. Manos arribaaaaaaaaaaaa!'
        ];

        $mailable = new BaseMailable(
            to:$valid['email'],
            subject:$valid['subject'],
            bcc:$valid['bcc'] ?? [],
            cc:$valid['cc'] ?? [],
            replyTo:$valid['reply_to'] ?? null,
            blocks:$blocks
        );

        $notification = new Notification();
        $notification->recipients = $mailable->getEnvelopeOptions('to');
        $notification->is_debug = $mailable->getEnvelopeOptions('is_debug');
        $notification->subject = $mailable->getEnvelopeOptions('subject');
        $notification->type = $valid['type'];
        $notification->status = NotificationStatus::PENDING->value;
        $notification->template_name = $mailable->getEnvelopeOptions('template_name');
        $notification->template_data = $mailable->getEnvelopeOptions('template_data');
        $notification->metadata = $mailable->getEnvelopeOptions('metadata');

        $notification->save();
    }
}
