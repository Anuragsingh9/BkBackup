<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Auth;
use Mail;
use App\Mail\SendMailable;

class ReminderEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $doodle_workshop;
    protected $task;
    protected $docs;
    protected $user;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($doodle_workshop, $task, $docs,$user)
    {
        $this->doodle_workshop = $doodle_workshop;
        $this->task = $task;
        $this->docs = $docs;
        $this->user = $user;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

         $message = (new SendMailable($this->doodle_workshop, $this->task, $this->docs, $this->user));
        return Mail::to($this->user->email)
            ->queue($message);

    }
}
