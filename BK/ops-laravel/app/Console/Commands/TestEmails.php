<?php
    
    namespace App\Console\Commands;
    
    use Illuminate\Console\Command;
    use Illuminate\Support\Facades\Mail;
    
    class TestEmails extends Command
    {
        /**
         * The name and signature of the console command.
         *
         * @var string
         */
        protected $signature = 'email:send';
        
        /**
         * The console command description.
         *
         * @var string
         */
        protected $description = 'Command description';
        
        /**
         * Create a new command instance.
         *
         * @return void
         */
        public function __construct()
        {
            parent::__construct();
        }
        
        /**
         * Execute the console command.
         *
         * @return mixed
         */
        public function handle()
        {
            $qual = app(\Modules\Qualification\Http\Controllers\VoteController::class);
            if (!$qual->testEmail(true)) {
                throw new \Exception('Email Server Not Working');
            }
            //
//            $core = app(\App\Http\Controllers\CoreController::class);
//            $mailData['mail']['email'] = 'opbissa@sharabh.com';
//            $mailData['mail']['msg'] = 'Testing From OOi';
//            $mailData['mail']['subject'] = 'Testing From OOi';
//            if (!$core->SendEmail($mailData)) {
//                throw new \Exception('Email Server Not Working');
//            }
        }
    }
