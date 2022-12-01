<?php


namespace App\Services\BlueJeans\Model;


use App\Services\BlueJeans\Abstracts\BlueJeansMeetingModel;
use App\Services\BlueJeans\BlueJeansService;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Validator;

class BlueJeansMeeting extends BlueJeansMeetingModel implements Arrayable {
    // about meeting
    public $id;
    public $uuid;
    public $title;
    public $description;
    public $start;
    public $end;
    public $timezone;
    public $status;
    public $attendees;
    public $created;
    //setting
    public $moderatorLess;
    // moderator
    public $moderator;
    public $attendeePasscode;
    public $numericMeetingId;


    public function __construct($meeting) {
        $this->id = ((isset($meeting->id) ? $meeting->id : NULL));
        $this->uuid = ((isset($meeting->uuid) ? $meeting->uuid : NULL));
        $this->title = ((isset($meeting->title) ? $meeting->title : NULL));
        $this->description = ((isset($meeting->description) ? $meeting->description : NULL));
        $this->start = ((isset($meeting->start) ? $meeting->start : NULL));
        $this->end = ((isset($meeting->end) ? $meeting->end : NULL));
        $this->timezone = ((isset($meeting->timezone) ? $meeting->timezone : NULL));
        $this->status = ((isset($meeting->status) ? $meeting->status : NULL));
        $this->attendees = (isset($meeting->attendees) ? $meeting->attendees : NULL);
        $this->created = (isset($meeting->created) ? $meeting->created : NULL);
        $this->moderatorLess = (isset($meeting->advancedMeetingOptions->moderatorLess) ? $meeting->advancedMeetingOptions->moderatorLess : NULL);
        $this->attendeePasscode = (isset($meeting->attendeePasscode) ? $meeting->attendeePasscode : NULL);
        $this->numericMeetingId = (isset($meeting->numericMeetingId) ? $meeting->numericMeetingId : NULL);
        $this->moderator = new \stdClass();
        if ($this->moderatorLess === NULL || $this->moderatorLess) {
            $this->moderator->id = NULL;
            $this->moderator->username = NULL;
            $this->moderator->firstname = NULL;
            $this->moderator->lastname = NULL;
            $this->moderator->profile_pic_url = NULL;
        } else {
            $this->moderator->id = ((isset($meeting->moderator->id)) ? $meeting->moderator->id : NULL);
            $this->moderator->username = ((isset($meeting->moderator->username)) ? $meeting->moderator->username : NULL);
            $this->moderator->firstname = ((isset($meeting->moderator->firstname)) ? $meeting->moderator->firstname : NULL);
            $this->moderator->lastname = ((isset($meeting->moderator->lastname)) ? $meeting->moderator->lastname : NULL);
            $this->moderator->profile_pic_url = ((isset($meeting->moderator->profile_pic_url)) ? $meeting->moderator->profile_pic_url : NULL);
        }
    }

    public function toArray() {
        return [
            'id'               => $this->id,
            'uuid'             => $this->uuid,
            'title'            => $this->title,
            'description'      => $this->description,
            'start'            => $this->start,
            'end'              => $this->end,
            'timezone'         => $this->timezone,
            'status'           => $this->status,
            'attendees'        => $this->attendees,
            'created'          => $this->created,
            'moderatorLess'    => $this->moderatorLess,
            'moderator'        => [
                'id'              => $this->moderator->id,
                'username'        => $this->moderator->username,
                'firstname'       => $this->moderator->firstname,
                'lastname'        => $this->moderator->lastname,
                'profile_pic_url' => $this->moderator->profile_pic_url,
            ],
            'attendeePasscode' => $this->attendeePasscode,
            'numericMeetingId' => $this->numericMeetingId,
        ];
    }


}