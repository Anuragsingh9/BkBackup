<?php
namespace App\Exports;

use Modules\Newsletter\Entities\IcontactMeta;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
class MessageStats implements FromCollection, WithHeadings
{
    use Exportable;
    public function __construct(int $id ,$IContact)
    {
        $this->id = $id;
        $this->IContact = $IContact;
        
    }
    public function collection()
    {
        $icontactMessageId=IcontactMeta::where(['type'=>3])->where('column_id',$this->id)->first();
        $res=$this->IContact->getStatistics($icontactMessageId->icontact_id);
        $data=[];
        if(is_object($res)){
            // $data[]=["Clicked","Opened","Bounced","Unsubscribed","No Info"];
            $data[]=[$res->statistics->clicks->total,$res->statistics->opens->total,$res->statistics->bounces,$res->statistics->unsubscribes,$res->statistics->neither];       
        }
        return collect( $data);
    }
    public function headings(): array
    {
        return ["Clicked","Opened","Bounced","Unsubscribed","No Info"];
    }
}

