<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $event;
    public function __construct($event)
    {
        $this->event = $event;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $permittedChars     = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $customUuid         = substr(str_shuffle($permittedChars), 0, 50);
        $this->email         = $this->event->details['email'];
        $this->name          = $this->event->details['name'];
        $this->subject       = $this->event->details['subject'];
        $this->content       = $this->event->details['content'];
        $this->title         = $this->event->details['title'];
        $this->template      = $this->event->details['template'];

        $data = [
            'email'         => $this->event->details['email'],
            'name'          => $this->event->details['name'],
            'content'       => $this->event->details['content'],
            'title'         => $this->event->details['title'],
            'template_used' => $this->event->details['template'],
            'track_id'      => $customUuid,
            'subject'       => $this->event->details['subject'],
        ];
        Mail::send($this->template, $data, function ($message) {
            $message->to($this->email, $this->name);
            $message->subject($this->subject);
        });
    }
}
