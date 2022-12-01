<?php

namespace App\Http\Controllers\Import;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Industry;
use App\Issuer;

use App\Union;

use App\Workshop;
use App\WorkshopCode;
use App\WorkshopMeta;
use App\Organisation;
use App\Model\WorkshopMetaTemp;
use Auth;
use Carbon;
use DB;
use Excel;

class ExcelTemplateController extends Controller
{
    /**
     * Generate excel template of industery.
     *
     * @return \Illuminate\Http\Response
     */
    public function excelIndustrySample()
    {

        $families = Industry::where('parent', null)->get(['id', 'name'])->pluck('name');
        $data = [];
        $validate = [];
        // $data[0] = [ 'Industry','Family'];
        foreach ($families as $key => $value) {
            $data[] = ['Family' => $value, 'Industry' => ''];
            $validate[] = $value;
        }

        Excel::create('SampleIndustary', function ($excel) use ($data, $validate) {
            $excel->sheet('data', function ($sheet2) use ($data, $validate) {
                $sheet2->row(1, array(
                    'Family', 'Industry',
                ));

                /**
                 * Validate and display gathered data in other cell
                 * @var  $objValidation */
                $objValidation = $sheet2->getCell('A2')->getDataValidation();
                $objValidation->setType(\PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objValidation->setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
                $objValidation->setAllowBlank(false);
                $objValidation->setShowInputMessage(true);
                $objValidation->setShowErrorMessage(true);
                $objValidation->setShowDropDown(true);
                $objValidation->setErrorTitle('Input error');
                $objValidation->setError('Value is not in list.');
                $objValidation->setPromptTitle('Pick from list');
                $objValidation->setPrompt('Please pick a value from the drop-down list.');
                $objValidation->setFormula1('upload!$A$2:$A$' . count($validate)); //note this!
            });
            $excel->sheet('upload', function ($sheet) use ($data, $validate) {
                $sheet->row(1, array(
                    'Family',

                ));
                foreach ($validate as $k => $v) {
                    $sheet->SetCellValue("A" . ($k + 1), $v);
                }
                //Gather data from these cells
                $sheet->_parent->addNamedRange(
                    new \PHPExcel_NamedRange(
                        'php', $sheet, 'A2:A4'
                    )
                );

            });

        })->store('xlsx')->export('xlsx');
        return $redirect->back();
    }

    /**
     * Generate union controller.
     *
     * @return \Illuminate\Http\Response
     */
    public function unionExcel($header)
    {
        $data = [];
        $validate = [];
        $Ind = Industry::get(['id', 'name', 'parent']);
        foreach ($Ind as $key => $value) {
            if ($value->parent == null) {
                $validate['family'][] = $value->name;
            } else {
                $validate['industry'][] = $value->name;
            }
        }

        Excel::create('Sample', function ($excel) use ($header, $validate) {
            $excel->sheet('data', function ($sheet2) use ($header, $validate) {
                $sheet2->row(1, $header);

                /**
                 * Validate and display gathered data in other cell
                 * @var  $objValidation */
                $objValidation = $sheet2->getCell('A2')->getDataValidation();
                // $objValidation = $sheet2->getCell('B2')->getDataValidation();
                $objValidation->setType(\PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objValidation->setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
                $objValidation->setAllowBlank(false);
                $objValidation->setShowInputMessage(true);
                $objValidation->setShowErrorMessage(true);
                $objValidation->setShowDropDown(true);
                $objValidation->setErrorTitle('Input error');
                $objValidation->setError('Value is not in list.');
                $objValidation->setPromptTitle('Pick from list');
                $objValidation->setPrompt('Please pick a value from the drop-down list.');
                $objValidation->setFormula1('upload!$A$2:$A$' . count($validate['family'])); //note this!
                // $objValidation->setFormula2('upload!$B$1:$B$'.count($validate['industry'])); //note this!

                /**
                 * Validate and display gathered data in other cell
                 * @var  $objValidation */
                // $objValidation = $sheet2->getCell('A2')->getDataValidation();
                $objValidation = $sheet2->getCell('B2')->getDataValidation();
                $objValidation->setType(\PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objValidation->setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
                $objValidation->setAllowBlank(false);
                $objValidation->setShowInputMessage(true);
                $objValidation->setShowErrorMessage(true);
                $objValidation->setShowDropDown(true);
                $objValidation->setErrorTitle('Input error');
                $objValidation->setError('Value is not in list.');
                $objValidation->setPromptTitle('Pick from list');
                $objValidation->setPrompt('Please pick a value from the drop-down list.');
                // $objValidation->setFormula1('upload!$A$2:$A$'.count($validate['family'])); //note this!
                $objValidation->setFormula1('upload!$B$2:$B$' . count($validate['industry'])); //note this!
            });

            $excel->sheet('upload', function ($sheet) use ($validate) {
                $sheet->row(1, [
                    'Family', 'Industry']);
                foreach ($validate['family'] as $k => $v) {
                    $sheet->SetCellValue("A" . ($k + 2), $v);
                }
                foreach ($validate['industry'] as $k => $v) {
                    $sheet->SetCellValue("B" . ($k + 2), $v);
                }
                //Gather data from these cells
                $sheet->_parent->addNamedRange(
                    new \PHPExcel_NamedRange(
                        'php', $sheet, 'A2:A' . count($validate['family'])
                    ),
                    new \PHPExcel_NamedRange(
                        'php', $sheet, 'B2:B' . count($validate['industry'])
                    )
                );

            });

        })->store('xlsx')->export('xlsx');
        return $redirect->back();
    }

    /**
     * Generate varios excel sample.
     *
        */
    public function generateExcelSample($type)
    {

        $data = [];
        $header = [];

        switch ($type) {
            case 'union':
                $header = ['family', 'industry', 'union_name', 'union_code', 'union_address1','union_zipcode' ,'union_city', 'union_country','union_phone','union_fax', 'contact_email', 'url', 'text_contact_button','visible_in_directory','logo_file_name','description','is_internal'];
                break;
            case 'user':
                $header = ['user_firstname', 'user_lastname', 'user_email','union_id','position_in_union','company','position_in_company'];
                break;
            case 'family':
                $header = ['family'];
                break;
            case 'workshop':
                $header = ['workshopname', 'code1', 'code2', 'workshop_description', 'is_visible',  'secretaryemail',  'deputyemail'];
                break;
            case 'member':
                $header = ['user_email', 'user_first_name', 'user_last_name'];
                break;
            case 'past_meeting':
                $org = Organisation::first();
                $address1 = (isset($org->address1)) ? $org->address1 : '';
                $city = (isset($org->city)) ? $org->city : '';
                $post = (isset($org->postal_code)) ? $org->postal_code : '';
                $country = (isset($org->country)) ? $org->country : '';
                $address = $address1 . ' ' . $city . ' ' . $post . ' ' . $country;
                $header = ['name_of_meeting', 'description_of_meeting', 'address', 'date', 'start_time', 'end_time'];
                $data[] = ['name_of_meeting' => 'demo meeting', 'description_of_meeting' => 'demo meeting description', 'address' => $address, 'date' => '2018-12-20', 'start_time' => '16:30', 'end_time' => '17:30'];
                break;
            case 'project':
                $header = ['project_name', 'milestone_name', 'milestone_start_date', 'milestone_end_date', 'task_name', 'task_start_date', 'task_end_date'];
                $data[] = ['project_name' => 'test ravindra 1',
                    'milestone_name' => 'milestone_name 1',
                    'milestone_start_date' => date('Y-m-d'),
                    'milestone_end_date' => date('Y-m-d', strtotime('+1 Year')),
                    'task_name' => 'Test 1',
                    'task_start_date' => date('Y-m-d'),
                    'task_end_date' => date('Y-m-d', strtotime('+1 Year')),
                ];
                break;
            case 'union_ids':
                $header =['union_id','union_short_name','union_name'];
                $unionData=Union::get(['id','union_code','union_name']);
                $data=[];
                foreach ($unionData as $key => $value) {
                    $data[]=['unionID'=>$value->id,'union_short_name'=>$value->union_code,'unionName'=>$value->union_name];
                }
                
        }
        if ($type == 'union') {
            $this->unionExcel($header);
        } else {
            Excel::create('Sample file of ' . $type, function ($excel) use ($data, $header) {
                $excel->sheet('data', function ($sheet) use ($data, $header) {
                    $sheet->row(1, $header);
                    $sheet->fromArray($data);
                });
            })->store('xls')->export('xls');
        }
        return $redirect->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
