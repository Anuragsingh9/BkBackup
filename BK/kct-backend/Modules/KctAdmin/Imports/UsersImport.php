<?php

namespace Modules\KctAdmin\Imports;

use Exception;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Modules\KctAdmin\Rules\GenderRule;
use Modules\KctAdmin\Rules\GradeRule;
use Modules\KctAdmin\Traits\ServicesAndRepo;
use Modules\UserManagement\Rules\UserRule;

class UsersImport implements ToCollection, WithStartRow, WithValidation {
    private array $headings;
    private array $aliases;
    private array $data;

    use ServicesAndRepo;

    /**
     * @throws Exception
     */
    public function __construct(array $headings, array $aliases) {
        $this->headings = $headings;
        $this->aliases = $aliases;
        $this->data = [];
        $this->aliases = array_map('strtolower', $this->aliases);
        $this->findAliasesRowNumber();
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will put the Excel sheet column number along with aliases
     * by searching the alias name in headings
     * -----------------------------------------------------------------------------------------------------------------
     * @throws Exception
     */
    private function findAliasesRowNumber() {
        foreach ($this->aliases as $column => $excelAlias) {
            $columnNumber = array_search($excelAlias, $this->headings);
            if ($columnNumber === false) {
                throw new Exception("Column name not found $excelAlias");
            }
            $this->aliases[$column] = [
                'header_name'   => $excelAlias,
                'column_number' => $columnNumber,
            ];
        }
    }

    private function resolveGender($gender): ?string {
        if($gender) {
            if (in_array(strtolower($gender), ['m', 'male'])) {
                return 'Male';
            } else if (in_array(strtolower($gender), ['f', 'female'])) {
                return 'Female';
            } else {
                return 'Other';
            }
        }
        return null;
    }

    /**
     * @throws Exception
     */
    public function collection(Collection $collection) {
        foreach ($collection as $row) {
            $userData = [
                'fname'            => $row[$this->aliases['fname']['column_number']],
                'lname'            => $row[$this->aliases['lname']['column_number']],
                'email'            => $row[$this->aliases['email']['column_number']],
                // optional fields
                'city'             => $row[$this->aliases['city']['column_number'] ?? null] ?? null,
                'country'          => $row[$this->aliases['country']['column_number'] ?? null] ?? null,
                'address'          => $row[$this->aliases['address']['column_number'] ?? null] ?? null,
                'postal'           => $row[$this->aliases['postal']['column_number'] ?? null] ?? null,
                'company'          => $row[$this->aliases['company_name']['column_number'] ?? null] ?? null,
                'company_position' => $row[$this->aliases['company_position']['column_number'] ?? null] ?? null,
                'union'            => $row[$this->aliases['union_name']['column_number'] ?? null] ?? null,
                'union_position'   => $row[$this->aliases['union_position']['column_number'] ?? null] ?? null,
                'internal_id'      => $row[$this->aliases['internal_id']['column_number'] ?? null] ?? null,
                'gender'           => $this->resolveGender($row[$this->aliases['gender']['column_number'] ?? null] ?? null),
                'grade'            => $row[$this->aliases['grade']['column_number'] ?? null] ?? null,
            ];
            if (isset($this->aliases['phone_number']['column_number'])) {
                $userData['phones'] = [
                    [
                        'country_code' => $row[$this->aliases['phone_code']['column_number'] ?? null] ?? null,
                        'number'       => $row[$this->aliases['phone_number']['column_number'] ?? null] ?? null,
                    ]
                ];
            }
            if (isset($this->aliases['mobile_number']['column_number'])) {
                $userData['mobiles'] = [
                    [
                        'country_code' => $row[$this->aliases['mobile_code']['column_number'] ?? null] ?? null,
                        'number'       => $row[$this->aliases['mobile_number']['column_number'] ?? null] ?? null,
                    ]
                ];
            }
            $this->data[] = $userData;
        }
    }

    public function rules(): array {
        $rules = [
            $this->aliases['fname']['column_number'] => ["required", new UserRule('fname')],
            $this->aliases['lname']['column_number'] => ["required", new UserRule('lname')],
            $this->aliases['email']['column_number'] => ["required", new UserRule('email')],
        ];
        if(isset($this->aliases['gender']['column_number'])) {
            $rules[$this->aliases['gender']['column_number']] = ['nullable', new GenderRule];
        }
        if(isset($this->aliases['grade']['column_number'])) {
            $rules[$this->aliases['grade']['column_number']] = ['nullable', new GradeRule];
        }

        return $rules;
    }

    /**
     * @return array
     */
    public function customValidationAttributes(): array {
        return [
            $this->aliases['lname']['column_number'] => 'fname',
            $this->aliases['lname']['column_number'] => 'lname',
            $this->aliases['email']['column_number'] => 'email',
        ];
    }

    public function startRow(): int {
        return 2;
    }

    public function getData(): array {
        return $this->data;
    }
}
