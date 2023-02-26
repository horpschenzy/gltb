<?php

namespace App\Listeners;

use PDF;
use webpatser;
use Carbon\Carbon;
use App\Jobs\SendEmailJob;
use App\Events\SavemailEvent;
use Illuminate\Support\Facades\Mail;
use App\Jobs\SendEmailWithAttachmentJob;
use Illuminate\Queue\InteractsWithQueue;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendmailListener
{
    public function handle($event)
    {
        $permittedChars     = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $customUuid         = substr(str_shuffle($permittedChars), 0, 50);
        $this->email         = $event->details['email'];
        $this->name          = $event->details['name'];
        $this->subject       = $event->details['subject'];
        $this->content       = $event->details['content'];
        $this->title         = $event->details['title'];
        $this->template      = $event->details['template'];

        $data = [
            'email'         => $event->details['email'],
            'name'          => $event->details['name'],
            'business_name' =>  $event->details['name'],
            'content'       => $event->details['content'],
            'title'         => $event->details['title'],
            'template_used' => $event->details['template'],
            'track_id'      => $customUuid,
            'subject'       => $event->details['subject'],
        ];
        try {
            dispatch(new SendEmailJob($event));
        } catch (JWTException $exception) {
            Log::alert($exception->getMessage());
            $this->serverstatuscode = "0";
            $this->serverstatusdes = $exception->getMessage();
        }
    }
}
