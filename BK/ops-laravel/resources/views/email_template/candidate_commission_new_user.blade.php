
    @php
        $settings['data'] = getEmailSetting(['email_graphic','qulification_welcome_workshop']);
        $orgDetail=getOrgDetail();
        $member=workshopValidatorPresident($mail['workshop_data']);
        $workshopSetting=getWorkshopSettingData($mail['workshop_data']->id);
        if(isset($workshopSetting['email'])){
        // var_dump($settings);
            if(isset($workshopSetting['email']['top_banner']) && $workshopSetting['email']['top_banner']!=null){
                $settings['data'][0]->top_banner=env('AWS_PATH').$workshopSetting['email']['top_banner'];
            }
            if(isset($workshopSetting['email']['bottom_banner']) && $workshopSetting['email']['bottom_banner']!=null){
                $settings['data'][0]->bottom_banner=env('AWS_PATH').$workshopSetting['email']['bottom_banner'];
            }
            $settings['data'][0]->email_sign=$workshopSetting['email']['email_sign'];
        }
        // var_dump($workshopSetting);die;
        $keywords =[
        '[[UserFirstName]]',
        '[[UserLastName]]',
        '[[UserEmail]]',
        '[[WorkshopLongName]]',
        '[[WorkshopShortName]]',
        '[[WorkshopPresidentFullName]]',
        '[[WorkshopvalidatorFullName]]',
        '[[WorkshopMeetingName]]',
        '[[WorkshopMeetingDate]]',
        '[[WorkshopMeetingTime]]',
        '[[WorkshopMeetingAddress]]',
        '[[ValidatorEmail]]',
        '[[PresidentEmail]]',
        '[[PresidentPhone]]',
        '[[OrgName]]',
        '[[OrgShortName]]'
        ];
    $values =[
    '',
    '',
    '',
    $mail['workshop_data']->workshop_name,
    '',
    $member['p']['fullname'],
    '',
    '',
    '',
    '',
    '',
    $member['v']['email'],
    $member['p']['email'],
    $member['p']['phone'],
    $orgDetail->org_name,
    $orgDetail->acronym
    ];
    @endphp
    @include('email_template.header',$settings)
        <tbody>
        <tr><td>{!! (str_replace($keywords,$values,$settings['data'][1]->text_before_link)) !!}</td></tr>
        <tr><td><a href="{{url($mail['url'])}}">Cliquez ici.</a></td></tr>
        <tr><td>{!! (str_replace($keywords,$values,$settings['data'][1]->text_after_link)) !!}</td></tr>
        
        </tbody>
    @include('email_template.footer',$settings)
