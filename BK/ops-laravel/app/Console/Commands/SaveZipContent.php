<?php

namespace App\Console\Commands;

use App\Imports\DummyUserImport;
use App\Services\DummyUsersService;
use Hyn\Tenancy\Models\Hostname;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class SaveZipContent extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dummy-user:import
                            {filepath : Path of the folder without file name}
                            {filename : File name present in the filepath}
                            {hostname_id : Hostname ID account to import}
                            ';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To import the dummy users to the provided account id';
    
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * -------------------------------------------------------------------------------------------------------------
     * @description Importing zip file content into databases.
     * @algo
     * 1. Prepare path for file
     * 2. Check existence of file if not exist throw error
     * 3. To find extension to file
     * 4. Prepare extraction path
     * 5. Extract the file from zip to path
     * 6. Fetch the file from extract directory
     * 7. Validate each file extension if match not found throw error
     * 8. Validate images folder in zip file
     * 9. Fetch data from file and insert into Database
     * 10. Remove the extraction folder from directory
     * -------------------------------------------------------------------------------------------------------------
     *
     * @return void
     */
    public function handle() {
        try {
            $hostname = Hostname::with('website')->find($this->argument('hostname_id'));
            DB::connection('tenant')->beginTransaction();
            if (!$hostname) {
                $this->error("Invalid hostname id");
                return;
            }
            $tenancy = app(\Hyn\Tenancy\Environment::class);
            $tenancy->hostname($hostname);
            $filepath = $this->argument('filepath');
            $filename = $this->argument('filename');
            
            $this->info("Provided path: $filepath");
            $this->info("Provided file name: $filename");
            
            $path = DummyUsersService::getInstance()->prepareDummyImportPath($filepath, $filename);
            
            $this->comment("Using the path: $path");
            
            $extractPath = DummyUsersService::getInstance()->prepareDummyImportExtractPath($filepath);
            
            $this->info("Extracting all the files to: $extractPath");
            
            $this->comment("Validating the file extension to extract");
            DummyUsersService::getInstance()->validateFileExtensionForDummyImport($filename);
            $this->info("Import file extension is validated");
            
            $this->info("Extracting zip file content to $extractPath");
            DummyUsersService::getInstance()->extractZipFile($path, $extractPath);
            $this->info("All the files inside the compressed file is now extracted to: $extractPath");
            
            $this->info("Getting the file names present in the extracted folders");
            $files = DummyUsersService::getInstance()->scanFilesForImport($extractPath);
            
            $this->info("Fetched all the file names");
            
            $this->comment("Checking if image folder present or not");
            DummyUsersService::getInstance()->validateImageFolder($extractPath);
            $this->info("Image folder present in the extracted path");
            
            $this->comment("Getting the first excel file present in the extracted folder");
            $excelFileName = DummyUsersService::getInstance()->getExcelFileName($files);
            $this->info("File found successfully");
            $this->alert("Name of the target excel file is: $excelFileName");
            
            $this->comment("Validating the excel file");
            DummyUsersService::getInstance()->validateExcelFile($excelFileName);
            $this->info("Excel file validated successfully");
            
            $this->info("Preparing path to read the excel file");
            $fileToRead = "$extractPath/$excelFileName";
            $this->info("Excel file path prepared: $fileToRead");
            
            $this->comment("Starting to import the dummy users, Please wait !...");
            Excel::import(new DummyUserImport($extractPath), $fileToRead);
            $this->alert("Dummy Users Imported Successfully");
            DB::connection('tenant')->commit();
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            $this->error($e->getMessage());
        }
    }
}
