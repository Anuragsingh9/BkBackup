
    @php
    if (session()->has('lang') && session()->get('lang') == "FR") {
        $settings['data'] = getEmailSetting(['email_graphic','alert_new_member_email']);
                }else{
        $settings['data'] = getEmailSetting(['email_graphic','alert_new_member_email_EN']);
                }
        $orgDetail=getOrgDetail();
        $member=workshopValidatorPresident($mail['workshop_data']);
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
    $mail['user_fname'],
    $mail['user_lname'],
    $mail['user_email'],
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
