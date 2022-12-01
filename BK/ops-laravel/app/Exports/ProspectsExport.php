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
    use Modules\Qualification\Entities\Prospect;
    use Modules\Qualification\Entities\QualificationClients;
    use Maatwebsite\Excel\Concerns\FromCollection;
    use PhpOffice\PhpSpreadsheet\Cell\Cell;
    use PhpOffice\PhpSpreadsheet\Cell\DataType;
    use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
    use PhpOffice\PhpSpreadsheet\Shared\Date;

    class ProspectsExport extends DefaultValueBinder implements WithCustomValueBinder, FromQuery, WithHeadings, WithMapping
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

        public function forWid(int $wid, int $case)
        {
            $this->wid = $wid;
            $this->case = $case;

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

        /**
         * @var Invoice $invoice
         */
        public function map($invoice): array
        {
            if (Auth::user()->role == 'M1') {
                return [
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
                return [
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

        public function query()
        {
            if (Auth::user()->role == 'M1') {
                if ($this->wid == 0) {
                    return Prospect::query()->with('workshop:id,workshop_name,code1,is_qualification_workshop')/*->where('case', $this->case)*/ ->with('workshop');
                } else {

                    $workshop = Workshop::withoutGlobalScopes()->find($this->wid, ['id', 'code1']);
                    if (isset($workshop->code1)) {
                        return Prospect::query()->with('workshop:id,workshop_name,code1,is_qualification_workshop')->where('workshop_code', $workshop->code1)->with('workshop');
                    }
                }
            } else {
                $workshop = Workshop::withoutGlobalScopes()->find($this->wid, ['id', 'code1']);
                if (isset($workshop->code1)) {
                    return Prospect::query()/*->where('case', $this->case)*/ ->where('workshop_code'
                        , $workshop->code1);
                }
            }
        }
//'zip_code', $this->wid

    }
