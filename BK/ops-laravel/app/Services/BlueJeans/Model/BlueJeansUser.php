<?php


namespace App\Services\BlueJeans\Model;


use App\Services\BlueJeans\Abstracts\BlueJeansUserModel;
use Illuminate\Contracts\Support\Arrayable;


class BlueJeansUser extends BlueJeansUserModel implements Arrayable {

    public $id,$firstName,$middleName,$lastName,$emailId,$username,$phone,$company,$language,$skypeId,$linkedinProfileUrl,$moderatorPasscode,$timezone;
    
    public function __construct($user) {
        $this->id =                     ((isset($user->id))?$user->id: null);
        $this->firstName =              ((isset($user->firstName))?$user->firstName: null);
        $this->lastName =               ((isset($user->lastName))?$user->lastName: null);
        $this->emailId =                ((isset($user->email))?$user->email: null);
        $this->username =               ((isset($user->username))?$user->username: null);
        $this->company =                ((isset($user->company))?$user->company: null);
        $this->middleName =             ((isset($user->middleName))?$user->middleName: null);
        $this->phone =                  ((isset($user->phone))?$user->phone: null);
        $this->language =               ((isset($user->language))?$user->language: null);
        $this->skypeId =                ((isset($user->skypeId))?$user->skypeId: null);
        $this->linkedinProfileUrl =     ((isset($user->linkedinProfileUrl))?$user->linkedinProfileUrl: null);
        $this->timezone =               ((isset($user->timezone))?$user->timezone: null);
        $this->moderatorPasscode =      ((isset($user->moderatorPasscode))?$user->moderatorPasscode: null);
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'emailId' => $this->emailId,
            'username' => $this->username,
            'company' => $this->company,
            'middleName' => $this->middleName,
            'phone' => $this->phone,
            'language' => $this->language,
            'skypeId' => $this->skypeId,
            'linkedinProfileUrl' => $this->linkedinProfileUrl,
            'timezone' => $this->timezone,
            'moderatorPasscode' => $this->moderatorPasscode,
        ];
    }
}