<?php

namespace App\Console\Commands;

use App\Exceptions\CustomException;
use App\Exceptions\CustomValidationException;
use App\Services\OrganisationService;
use Illuminate\Console\Command;

class CreateAccount extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'account:create
                            {--fname= : First name of the Org Admin}
                            {--lname= : Last Name of the Org Admin}
                            {--email= : Email Of the ORG Admin}
                            {--name= : Name of the Organisation}
                            {--accName= : Name of the Account}';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will be responsible for creating a new Organisation Account.';
    
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
     * @throws CustomException
     * @throws CustomValidationException
     */
    public function handle() {
        return OrganisationService::getInstance()->createAccount(
            $this->option('fname'),
            $this->option('lname'),
            $this->option('email'),
            $this->option('name'),
            $this->option('accName')
        );
    }
}
