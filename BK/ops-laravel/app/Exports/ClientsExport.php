<?php

    namespace App\Exports;

    use App\Workshop;
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Auth;
    use Maatwebsite\Excel\Concerns\Exportable;
    use Maatwebsite\Excel\Concerns\FromQuery;
    use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
    use Maatwebsite\Excel\Concerns\WithHeadings;
    use Maatwebsite\Excel\Concerns\WithMapping;
    use Modules\Qualification\Entities\QualificationClients;
    use Maatwebsite\Excel\Concerns\FromCollection;
    use PhpOffice\PhpSpreadsheet\Cell\Cell;
    use PhpOffice\PhpSpreadsheet\Cell\DataType;
    use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
    use PhpOffice\PhpSpreadsheet\Shared\Date;

    class ClientsExport extends DefaultValueBinder implements WithCustomValueBinder, FromCollection, WithHeadings
    {
        use Exportable;

        public function bindValue(Cell $cell, $value)
        {
            if (is_numeric($value)) {
                $cell->setValueExplicit($value, DataType::TYPE_STRING);

                return TRUE;
            }

            // else return default behavior
            return parent::bindValue($cell, $value);
        }

        public function forWid(int $wid)
        {
            $this->wid = $wid;

            return $this;
        }

        public function headings(): array
        {
            if (Auth::user()->role == 'M1') {
                if (userLang() == 'FR') {
                    return [
                        'Comité',
                        'Code',
                        'Nom',
                        'Téléphone',
                        'Email',
                        'Société',
                        'SIRET',
                        'Code postal',
                        'Mobile',
                        'Date de demande',
                    ];
                } else {
                    return [
                        'Workshop Name',
                        'Workshop Code',
                        'Name',
                        'Phone',
                        'Email',
                        'Company',
                        'SIRET',
                        'Zip Code',
                        'Mobile',
                        'Date',
                    ];
                }
            } else {
                if (userLang() == 'FR') {
                    return [
                        'Nom',
                        'Téléphone',
                        'Email',
                        'Société',
                        'SIRET',
                        'Code postal',
                        'Mobile',
                        'Date de demande',
                    ];
                } else {
                    return [
                        'Name',
                        'Phone',
                        'Email',
                        'Company',
                        'SIRET',
                        'Zip Code',
                        'Mobile',
                        'Date',
                    ];
                }
            }
        }

        public function collection()
        {
            if (Auth::user()->role == 'M1') {
                if ($this->wid == 0) {
                    $data['clients'] = QualificationClients::query()->with(['workshop:id,workshop_name,code1,is_qualification_workshop'])->get();
                    $data['candidates'] = Workshop::withoutGlobalScopes()->where('is_qualification_workshop', 1)->with(['meta' => function ($a) {
                        $a->select('id', 'workshop_id', 'user_id', 'role')->where('role', 4);
                    }, 'meta.user.userSkillCompany:id,skill_id,type,field_id,text_input', 'meta.user.userSkillSiret:id,skill_id,type,field_id,numerical_input', 'meta.user:id,fname,lname,phone,email,postal,created_at,mobile'])->get(['id', 'workshop_name', 'code1', 'is_qualification_workshop']);
                } else {
                    $data = [];
                    $workshop = Workshop::withoutGlobalScopes()->find($this->wid, ['id', 'code1']);

                    if (isset($workshop->code1)) {
                        $data['clients'] = QualificationClients::query()->where('workshop_code', $workshop->code1)->with(['workshop:id,workshop_name,code1,is_qualification_workshop'])->get();
                        $data['candidates'] = Workshop::withoutGlobalScopes()->where('id', $workshop->id)->where('is_qualification_workshop', 1)->with(['meta' => function ($a) {
                            $a->select('id', 'workshop_id', 'user_id', 'role')->where('role', 4);
                        }, 'meta.user.userSkillCompany:id,skill_id,type,field_id,text_input', 'meta.user.userSkillSiret:id,skill_id,type,field_id,numerical_input', 'meta.user:id,fname,lname,phone,email,postal,created_at,mobile'])->get(['id', 'workshop_name', 'code1', 'is_qualification_workshop']);
                    }
                }

                return $this->customMap($data);
                // return $data;
            } else {
                $data = [];
                $workshop = Workshop::withoutGlobalScopes()->find($this->wid, ['id', 'code1']);

                if (isset($workshop->code1)) {
                    $data['clients'] = QualificationClients::query()->where('workshop_code', $workshop->code1)->get();
                    $data['candidates'] = Workshop::withoutGlobalScopes()->where('id', $workshop->id)->where('is_qualification_workshop', 1)->with(['meta' => function ($a) {
                        $a->select('id', 'workshop_id', 'user_id', 'role')->where('role', 4);
                    }, 'meta.user.userSkillCompany:id,skill_id,type,field_id,text_input', 'meta.user.userSkillSiret:id,skill_id,type,field_id,numerical_input', 'meta.user:id,fname,lname,phone,email,postal,created_at,mobile'])->get(['id', 'workshop_name', 'code1', 'is_qualification_workshop']);
                }
                return $this->customMap($data);
            }
        }


        public function customMap($data)
        {
            $result = [];
            if (isset($data['candidates']))
                $this->getCandidates($data['candidates'], $result);
            if (isset($data['clients']))
                $this->getClients($data['clients'], $result);

            return collect($result);
        }

        public function getClients($clients, &$result)
        {
            if (!empty($clients)) {
                foreach ($clients as $invoice) {
                    if (Auth::user()->role == 'M1') {
                        $result[] = [
                            isset($invoice->workshop->workshop_name) ? $invoice->workshop->workshop_name : 'N/A',
                            isset($invoice->workshop->code1) ? $invoice->workshop->code1 : 'N/A',
                            ($invoice->fname . ' ' . $invoice->lname),
                            $invoice->tel,
                            $invoice->email,
                            $invoice->company,
                            $invoice->reg_no,
                            $invoice->zip_code,
                            isset($invoice->mobile)?$invoice->mobile:'',
                            Carbon::parse($invoice->created_at)->format('d-m-Y'),
                        ];
                    } else {
                        $result[] = [
                            ($invoice->fname . ' ' . $invoice->lname),
                            $invoice->tel,
                            $invoice->email,
                            $invoice->company,
                            $invoice->reg_no,
                            $invoice->zip_code,
                            isset($invoice->mobile)?$invoice->mobile:'',
                            Carbon::parse($invoice->created_at)->format('d-m-Y'),
                        ];
                    }
                }
            }

        }

        public function getCandidates($workshops, &$result)
        {
            if (!empty($workshops)) {
                $workshops = collect($workshops)->filter(function ($value, $key) {
                    return count($value->meta) > 0;
                })->values();
                foreach ($workshops as $workshop) {
                    foreach ($workshop->meta as $item) {
                        if (!empty($item->user)) {
                            if (Auth::user()->role == 'M1') {
                                $result[] = [
                                    isset($workshop->workshop_name) ? $workshop->workshop_name : 'N/A',
                                    isset($workshop->code1) ? $workshop->code1 : 'N/A',
                                    ($item->user->fname . ' ' . $item->user->lname),
                                    $item->user->phone,
                                    $item->user->email,
                                    isset($item->user->userSkillCompany->text_input) ? $item->user->userSkillCompany->text_input : 'N/A',
                                    isset($item->user->userSkillSiret->numerical_input) ? $item->user->userSkillSiret->numerical_input : 'N/A',
                                    $item->user->postal,
                                    $item->user->mobile,
                                    Carbon::parse($item->user->getOriginal('created_at'))->format('d-m-Y'),
                                ];
                            } else {
                                $result[] = [
                                    ($item->user->fname . ' ' . $item->user->lname),
                                    $item->user->phone,
                                    $item->user->email,
                                    isset($item->user->userSkillCompany->text_input) ? $item->user->userSkillCompany->text_input : 'N/A',
                                    isset($item->user->userSkillSiret->numerical_input) ? $item->user->userSkillSiret->numerical_input : 'N/A',
                                    $item->user->postal,
                                    $item->user->mobile,
                                    Carbon::parse($item->user->getOriginal('created_at'))->format('d-m-Y'),
                                ];
                            }
                        }
                    }
                }
            }
        }
    }
