<?php

namespace App\Imports;


use App\Entity;
use App\Industry;
use App\Rules\Phone;
use App\Services\ImportServices;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException as IlluminateValidationException;
use Illuminate\Support\Collection;
use Batch;
use  DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;

/**
 * Class ContactsImport
 * @package App\Imports
 */
class EntityImport implements ToCollection, WithValidation, WithStartRow, WithMultipleSheets
{
    use Importable;

    protected $created = 0;
    protected $updated = 0;
    /**
     * @var array
     */
    protected $ruleArray = [];
    /**
     * @var array
     */
    protected $finalData = [];
    /**
     * @var array
     */
    protected $allRules = [];
    /**
     * @var
     */
    protected $data;
    /**
     * @var int
     */
    protected $step = 2;
    protected $entityType = 2;
    protected $entityTypeStr = 'company';

    /**
     * @param $ruleArray
     */
    public function setRuleArray($ruleArray)
    {
        $this->ruleArray = $ruleArray;
    }


    /**
     * @param $key
     * @param $rules
     */
    public function setAllRules($key, $rules)
    {
        $this->allRules[$key] = $rules;
    }

    /**
     * @param $unique
     */
    public function setFinalData($finalData)
    {
        $this->finalData = $finalData;
    }

    /**
     * @param $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param $step
     */
    public function setStep($step)
    {
        $this->step = $step;
    }

    public function setEntityType($entityType)
    {
        $this->entityType = $entityType;
    }

    public function setEntityTypeStr($entityTypeStr)
    {
        $this->entityTypeStr = $entityTypeStr;
    }

    /**
     * @return int
     */
    public function getStep()
    {
        return $this->step;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
//        ^[a-zA-Z0-9\déèêîïôàç,.#]+$'regex:/^[a-zA-Z\déèêîïôàç,.#]+$/'
        $star = '*.';
        $this->allRules[$star . $this->ruleArray['long_name']] =  ['required', 'regex:/^[A-Za-z. -]+$/', Rule::unique('tenant.entities','long_name')->where(function ($query)  {
            return $query->where('entity_type_id', $this->entityType);
        })];
        
        isset($this->ruleArray['email']) ? $this->allRules[$star . $this->ruleArray['email']] = 'email' : '';
        isset($this->ruleArray['phone']) ? $this->allRules[$star . $this->ruleArray['phone']] = [
            'nullable',
            new Phone
        ] : '';
        isset($this->ruleArray['zip_code']) ? $this->allRules[$star . $this->ruleArray['zip_code']] = 'nullable|numeric' : '';
        isset($this->ruleArray['fax']) ? $this->allRules[$star . $this->ruleArray['fax']] = 'nullable|numeric' : '';
        isset($this->ruleArray['entity_website']) ? $this->allRules[$star . $this->ruleArray['entity_website']] = 'nullable|url' : '';
        isset($this->ruleArray['entity_sub_type']) ? $this->allRules[$star . $this->ruleArray['entity_sub_type']] = 'required|between:1,2' : '';
        ksort($this->allRules);
        return $this->allRules;
    }

    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [];

    }

    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }

    /**
     * @return array
     */
    public function customValidationAttributes()
    {
        $star = '*.';
        foreach ($this->ruleArray as $k => $item) {
            $cust[$star . $item] = $k;
        }

        return $cust;

    }

    public function sheets(): array
    {
        return [
            0 => $this,
        ];
    }

    /**
     * @param Collection $rows
     * @throws IlluminateValidationException
     */
    public function collection(Collection $rows)
    {
        $star = '*.';
        $messages = $this->customValidationMessages();
        $attributes = $this->customValidationAttributes();
        $rules = $this->rules();

        try {
            //iterating and making entries in lower case
            $rows = $rows->map(function ($name) {
                if (isset($name[$this->ruleArray['long_name']])) {
                    $name[$this->ruleArray['long_name']] = strtolower($name[$this->ruleArray['long_name']]);
                    return $name;
                }
            });
            //validator checks the validation
            $validator = Validator::make($rows->toArray(), $rules, $messages, $attributes);
            //checking industries exist or not
            $indExist = collect(array_column($this->finalData, 'map'))->search(function ($item, $key) {
                return collect($item)->contains('industry_id');
            });
            $industries = [];
            if ($indExist !== false) {
                $indKey = array_keys($this->finalData[$indExist]['map']);
                $rows = $rows->map(function ($name) use ($indKey) {
                    $name[$indKey[0]] = strtolower($name[$indKey[0]]);
                    return $name;
                });
                $industries = Industry::whereIn(DB::raw("LOWER(name)"), $rows->pluck($indKey[0])->filter()->toArray())->where('parent', '!=', 0)->get(['id', 'name']);
                $industries = $industries->map(function ($name) {
                    $name->name = strtolower($name->name);
                    return $name;
                });
                if (count($industries) == 0) {
                    $validator->after(function ($validator) use ($rows, $indKey) {
                        foreach ($rows as $k => $item) {
                            $validator->errors()->add($k, 'Industry ' . $item[$indKey[0]] . " not Exist in System.");
                        }
                    });
                } else {
                    $contains = $rows->map(function ($value, $key) use ($industries, $indKey, $validator) {
                        if ((!$industries->pluck('name')->contains(($value[$indKey[0]]))) && (!empty($value[$indKey[0]]))) {
                            $validator->after(function ($validator) use ($key, $value, $indKey) {
                                $validator->errors()->add($key, 'Industry ' . $value[$indKey[0]] . " not Exist in System.");
                            });
                        }
                    });

                }
            }
            //validation run
            $validator->validate();
            $custom = $default = $entity = $entityPos = $custom = [];
           /*
            * declaring variable where ins denote the insertion
            * contains denote that already contain
            * */
            $ins = $cusIns = $eniIns = $entityPosIns = $containsIns = $cusContainsIns = $eniContainsIns = $entityPosContainsIns = [];
            //here we are adding condition where 2 means it show only validation errors
            //and 3 will enter it in db
            if ($this->step == 2) {
                //here we are adding all errors array
                foreach ($this->finalData as $item) {
                    if (isset($item['map']) && isset($item['is_custom'])) {
                        $arrKey = array_keys($item['map']);
                        if ($item['tab'] == 'personal_tab') {
                            $default[__('message.' . $item['map'][$arrKey[0]])] = $arrKey[0];
                        }
                        if ($item['tab'] == 'professional_tab') {

                            if ($item['map'][$arrKey[0]] == 'company_position' || $item['map'][$arrKey[0]] == 'instance_position' || $item['map'][$arrKey[0]] == 'union_position' || $item['map'][$arrKey[0]] == 'press_position') {
                                $entityPos[__('message.' . $item['map'][$arrKey[0]])] = $arrKey[0];
                            } else {
                                $entity[__('message.' . $item['map'][$arrKey[0]])] = $arrKey[0];
                            }
                        }
                        if ($item['is_custom']) {
                            $custom[isset($item['map'][$arrKey[0]]['label']) ? $item['map'][$arrKey[0]]['label'] : 0] = $arrKey[0];
                        }
                    }
                }
                //adding row wise errors
                foreach ($rows as $key => $row) {
                    if ($key <= 2) {
                        foreach ($default as $k => $item) {
                            $show[$key][$k] = strtolower($row[$item]);
                        }
                        foreach ($custom as $k => $item1) {
                            $show[$key][$k] = $row[$item1];
                        }
                        foreach ($entity as $k => $item2) {
                            $show[$key][$k] = $row[$item2];
                        }

                        foreach ($entityPos as $k => $item3) {
                            $show[$key][$k] = $row[$item3];
                        }
                    }

                }

                $this->setData($show);
            }
            if ($this->step == 3) {
                $entity = [];
                //adjusting tab wise data and creating arrays
                foreach ($this->finalData as $item) {

                    if (isset($item['map']) && isset($item['is_custom'])) {
                        $arrKey = array_keys($item['map']);
                        if ($item['tab'] == 'personal_tab') {
                            $default[$item['map'][$arrKey[0]]] = $arrKey[0];
                        }

                        if ($item['is_custom']) {

                            $custom[$arrKey[0]] = [isset($item['map'][$arrKey[0]]['value']) ? $item['map'][$arrKey[0]]['value'] : 0, isset($item['map'][$arrKey[0]]['skill_format_id']) ? $item['map'][$arrKey[0]]['skill_format_id'] : 0];
                        }
                    }
                }
                //filling value as per rows
                foreach ($rows as $key => $row) {
                    foreach ($default as $k => $item) {
                        if ($k == 'industry_id') {
                            if (!empty($row[$item])) {
                                $industry = $industries->search(function ($item1, $key) use ($row, $item) {
                                    return $item1->name == strtolower($row[$item]);
                                });
                                $ins[$key][$k] = $industries[$industry]->id;
                            } else {
                                $ins[$key][$k] = null;
                            }
                        } else {
                            if ($k == 'long_name') {
                                $ins[$key]['entity_type_id'] = $this->entityType;
                            }
                            $ins[$key][$k] = $row[$item];
                        }
                    }

                    foreach ($custom as $k => $item1) {
                        $cusIns[$key][$item1[0]] = ['value' => $row[$k], 'skill_format_id' => $item1[1]];
                    }
                }
    
                $ins = collect($ins)->unique(function ($item) {
                    return $item['entity_type_id'].$item['long_name'];
                });
                $ins= $ins->values()->all();
                //init the import service instance
                $importService = ImportServices::getInstance();
                //init the db transaction
                DB::transaction(function () use ($ins, $cusIns, $importService, $rows) {
                    $className = new Entity();
                    $lastIdBeforeInsertion = $importService->getLastId($className);
                    // 2- insert your data
                    Entity::insert($ins);
                    // 3- Getting the last inserted ids
                    $insertedIds = [];
                    for ($i = 1; $i <= count($ins); $i++) {
                        array_push($insertedIds, $lastIdBeforeInsertion + $i);
                    }
                    //adding custom skills data
                    if (count($cusIns) > 0) {
                        $importService->addUserSkill($insertedIds, $cusIns, $this->entityTypeStr, []);
                    }

                    $this->created = count($insertedIds);
                    $this->updated = 0;
                });

            }

        } catch (IlluminateValidationException $e) {

            throw new IlluminateValidationException(
                $e->errors()
            );
        }

    }
}
