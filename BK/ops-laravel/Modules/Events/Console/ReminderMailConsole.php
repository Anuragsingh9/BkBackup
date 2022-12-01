<?php

namespace Modules\Events\Console;

use Hyn\Tenancy\Models\Hostname;
use Illuminate\Console\Command;
use Modules\Events\Service\EmailService;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ReminderMailConsole extends Command {
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'mail:event-reminders';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'For sending reminder emails to event users';
    
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        EmailService::getInstance()->sendReminderEmails();
    }
    
}
