<?php
namespace App\Exports;

use App\Model\ListModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
class ListsExport implements FromCollection, WithHeadings
{
    use Exportable;
    public function __construct(int $id)
    {
        $this->id = $id;
    }
    public function collection()
    {
        $list=ListModel::with('newsletter_contacts', 'users:users.id,users.fname,users.lname,users.email')->find($this->id,['id', 'name', 'description', 'type', 'typology_id']);
        if($list->type==0 || $list->type==3 ){
            $data=$list->users->map(function ($user) {
                return $user->only(['fname','lname','email']);
            });
            return $data;
        }
        else{
            $data=$list->newsletter_contacts->map(function ($user) {
                return $user->only(['fname','lname','email']);
            });
            
            return $data;
        }
    }
    public function headings(): array
    {
        return ["fname","lname","email"];
    }
}

