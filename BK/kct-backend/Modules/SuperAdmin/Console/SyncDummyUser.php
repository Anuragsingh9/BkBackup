<?php

namespace Modules\SuperAdmin\Console;

use Carbon\Carbon;
use Exception;
use Hyn\Tenancy\Models\Website;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Modules\KctAdmin\Entities\Event;
use Modules\UserManagement\Entities\DummyUser;
use Modules\UserManagement\Imports\DummyUserSync;
use Modules\UserManagement\Traits\ServicesAndRepo;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class SyncDummyUser extends Command {
    use ServicesAndRepo;
    use \Modules\SuperAdmin\Traits\ServicesAndRepo;


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dummy-user:sync {--website_id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To sync the dummy users from the provided env path';

    /**
     * @var mixed
     */
    private $extractedPath;

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
     * @return void
     */
    public function handle() {
        try {
            $website = $this->option('website_id');
            if ($website) {
                $webSites = Website::whereIn('id', [$website])->get();
            } else {
                $webSites = Website::all();
            }
            foreach ($webSites as $website) {
                $this->suServices()->tenantService->setTenantByWebsite($website);
                DB::connection('tenant')->beginTransaction();
                $this->sync();
                DB::connection('tenant')->commit();
            }
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    private function sync() {
        $usersFromExcel = $this->getDummyUsersFromExcel();
        $dummyUsers = $this->syncDummyUsersByName($usersFromExcel);
        $this->addNewUsersFromList($dummyUsers);
    }

    /**
     * @return Collection
     * @throws Exception
     */
    private function getDummyUsersFromExcel(): Collection {
        $dummyUserPath = env('DUMMY_USER_IMPORT_PATH');
        $this->extractedPath = $dummyUserPath;
        // Getting the file names present in the extracted folders
        $files = $this->scanFilesForImport($dummyUserPath);

        $excelFileName = $this->getExcelFileName($files);
        $this->validateExcelFile($excelFileName);
        // Preparing path to read the Excel file
        $fileToRead = "$dummyUserPath/$excelFileName";
        // returning first to access the first sheet directly
        return Excel::toCollection(new DummyUserSync, $fileToRead)->first()->forget(0);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the excel file name
     * @warn this will return the only first excel file present in path
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $files
     * @return string|null
     */
    public function getExcelFileName($files): ?string {
        $allowedExtensions = ['xls', 'xlsx'];
        // matching the files with extension and return if found
        foreach ($files as $file) {
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            if (in_array($ext, $allowedExtensions)) {
                return $file;
            }
        }
        return null;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To validate if the extracted path location contains the excel files or not
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string $filteredFiles
     * @return void
     * @throws Exception
     */
    public function validateExcelFile(string $filteredFiles): void {
        if (!$filteredFiles) {
            throw new Exception('No file found to process');
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To scan the files to extract
     * this will return the files present in the directory
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $extractPath
     * @return array|false
     */
    public function scanFilesForImport($extractPath) {
        return scandir($extractPath);
    }


    /**
     * @throws Exception
     */
    private function syncDummyUsersByName($users): Collection {
        DB::connection('tenant')->statement('SET FOREIGN_KEY_CHECKS=0');
        DummyUser::truncate();
        DB::connection('tenant')->statement('SET FOREIGN_KEY_CHECKS=1');
        $first = DummyUser::first();
        if (!$first) {
            // adding first user to change the index of primary key to following
            DummyUser::insert(['id' => 5000000,]);
            DummyUser::where('id', 5000000)->delete();
        }
        $result = new Collection();
        foreach ($users as $user) {

            $userData = [
                'fname'            => $user[0] ?? null,
                'lname'            => $user[1] ?? null,
                'company'          => $user[2] ?? null,
                'company_position' => $user[3] ?? null,
                'union'            => $user[4] ?? null,
                'union_position'   => $user[5] ?? null,
                'video_url'        => isset($user[6]) ? "$user[6].mp4" : '',
                'avatar'           => $this->validateMediaExists($user[7] ?? null),
                'type'             => 1, // regular
                'updated_at'       => Carbon::now(),
                'created_at'       => Carbon::now(),
            ];
            $result->push(DummyUser::create($userData));
        }
        return $result;
    }

    /**
     * @throws Exception
     */
    private function addNewUsersFromList($dummyUsers) {
        $events = Event::with('spaces')->get();
        $events = $events->filter(function ($event) {
            return $event->event_settings['is_dummy_event'] == 1;
        });

        foreach ($events as $event) {
            if ($event->event_settings['is_dummy_event'] == 1) {
                $event->dummyRelations()->delete();
                foreach ($dummyUsers as $dummyUser) {
                    $event->dummyRelations()->create([
                        'space_uuid'    => $event->spaces->first()->space_uuid,
                        'dummy_user_id' => $dummyUser->id,
                    ]);
                }
            }
        }
    }

    /**
     * @throws Exception
     */
    public function validateMediaExists($fileName): string {
        $imagePath = "$this->extractedPath/images/$fileName.jpg";
        if (!file_exists($imagePath)) {
            throw new Exception("File $fileName not present in images folder after extract");
        }
        return "assets/dummy_users/$fileName.jpg";
    }


    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments(): array {
        return [
            ['example', InputArgument::REQUIRED, 'An example argument.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array {
        return [
            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
