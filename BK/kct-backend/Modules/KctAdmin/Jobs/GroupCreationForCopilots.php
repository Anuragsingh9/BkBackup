<?php

namespace Modules\KctAdmin\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class GroupCreationForCopilots implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $data = [];
    public $subject;
    public $view;
    public $users;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($users,$data,$subject,$view)
    {
        $this->users = $users;
        $this->data =  $data;
        $this->subject = $subject;
        $this->view = $view;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->users as $user){
            Mail::send("$this->view", $this->data, function ($message) use ($user) {
                $message->to($user)->subject($this->subject);
                $message->from(env('MAIL_FROM_ADDRESS'), 'Test Mail');
            });
        }
    }
}
