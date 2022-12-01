<?php
namespace App\Imports;

use App\Milestone;
use App\Project;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
class ProjectImport implements ToModel
{
    use Importable;
    public function __construct(int $id,$projectEnable)
    {
        $this->id = $id;
        $this->projectEnable = $projectEnable;
    }
    public function model(array $excelData)
    {   
        $count = 0;
        $errors = [];
        $scuess = [];
        dd($this->id,$this->projectEnable);
        if (!empty($excelData) && count($excelData) > 0) {
            $records = $excelData;
            for ($i=1;$i<count($excelData);$i++) {
                $val=$excelData[$i];
                $flag = 0;
            $project = Project::where(['project_label' => $val['project_name'], 'wid' => $id])->first(['id']);
            if (!$project) {
                $project = Project::create([
                    'project_label' => $val['project_name'],
                    'wid' => $id,
                    'user_id' => 0,
                    'color_id' => 1,
                    'display' => $projectEnable,
                ]);
                $flag = 1;
            } else {
                $milestone = Milestone::where(['project_id' => $project->id, 'label' => $val['milestone_name']])->first(['id', 'label']);
                if (!$milestone) {
                    $flag = 1;
                } else {
                    $flag = 2;
                }
            }
            switch ($flag) {
                case 1:
                    $milestone = Milestone::create([
                        'project_id' => $project->id,
                        'label' => $val['milestone_name'],
                        'user_id' => 0,
                        'color_id' => 1,
                        'start_date' => (isset($val['milestone_start_date']) && !empty($val['milestone_start_date'])) ? Carbon\Carbon::parse($val['milestone_start_date'])->format('Y-m-d') : Carbon::now()->format('Y-m-d'),
                        'end_date' => Carbon\Carbon::parse($val['milestone_end_date'])->format('Y-m-d'),
                    ]);
                case 2:
                    $scuess[$count] = Task::create([
                        'workshop_id' => $id,
                        'task_created_by_id' => 0,
                        'task_text' => $val['task_name'],
                        'milestone_id' => $milestone->id,
                        'start_date' => Carbon\Carbon::parse($val['task_start_date'])->format('Y-m-d'),
                        'end_date' => Carbon\Carbon::parse($val['task_end_date'])->format('Y-m-d'),
                        'assign_for' => 1,
                        'activity_type_id' => 1,
                        'status' => 1,
                        'task_color_id' => 1,
                    ]);
                    break;
            }
        }
            Storage::disk('localDocPdf')->delete('project.xls');
            
        }
    }
}
?>