<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMailable extends Mailable
{
    use Queueable, SerializesModels;

    protected $doodle_workshop;
    protected $task;
    protected $docs;
    protected $user;
    protected $project;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($doodle_workshop, $task, $docs, $user,$project)
    {
        $this->doodle_workshop = $doodle_workshop;
        $this->task = $task;
        $this->docs = $docs;
        $this->user = $user;
        $this->project = $project;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this->subject('Weekly Reminder')->view('email_template.weekly_reminder')->with([
            'doodle_workshop' => $this->doodle_workshop,
            'task' => $this->task,
            'docs' => $this->docs,
            'user' => $this->user,
            'project' => $this->project,
        ]);
    }
}
