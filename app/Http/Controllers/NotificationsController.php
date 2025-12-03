<?php

namespace App\Http\Controllers;

use App\Enums\NotificationStatus;
use App\Enums\PriorityStates;
use App\Mail\BaseMailable;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class NotificationsController extends Controller
{
    public function notificationSend(Request $request)
    {
        $valid = $request->validate([
            'email' => 'required|email',
            'subject' => 'required|string',
        ]);

        $blocks = [
            [
                'type' => 'text',
                'content' => 'This is a notification email sent from the system.'
            ]
        ];

        try {
            $mailable = new BaseMailable(
                to: $valid['email'],
                options: [
                    'subject' => $valid['subject'],
                    'template_name' => 'default',
                ],
                blocks: $blocks
            );
            
            $notification = new Notification();

            $notification->recipients = $mailable->extractEmailFromAddress('to');
            $notification->from = $mailable->extractEmailFromAddress('from') ?? config('mail.from.address');
            $notification->is_debug = $mailable->getOption('is_debug');
            $notification->subject = $mailable->getOption('subject');
            $notification->type = $valid['type'];
            $notification->max_attempts = 3;
            $notification->status = NotificationStatus::PENDING;
            $notification->priority = PriorityStates::NORMAL;
            $notification->template_name = $mailable->getOption('template_name');
            $notification->template_data = $mailable->getOption('template_data');
            $notification->metadata = $mailable->getOption('metadata');
            $notification->message = $mailable->render();
            $notification->save();

            // Mail::to($mailable->extractEmailFromAddress('to'))->send($mailable);
            
        } catch (\Throwable $th) {
            logger()->error('Failed to send notification', ['error' => $th->getMessage()]);
            return redirect()->route('home')->with('error', 'Failed to send notification!');
        }

        return redirect()->route('home')->with('success', 'Notification sent correctly!');
    }
}
