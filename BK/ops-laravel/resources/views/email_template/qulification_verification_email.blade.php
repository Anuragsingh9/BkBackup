@php
    $settings['data'] = getEmailSetting(['email_graphic',$mail['type']]);
    $member=workshopValidatorPresident($mail['workshop_data']);
    $workshopSetting=getWorkshopSettingData($mail['workshop_data']->id);
    $WorkshopSignatory=getWorkshopSignatoryData($mail['workshop_data']->id);

            if(isset($workshopSetting['email'])){
                    $settings['data'][0]->top_banner=env('AWS_PATH').$workshopSetting['email']['top_banner'];
                    $settings['data'][0]->bottom_banner=env('AWS_PATH').$workshopSetting['email']['bottom_banner'];
                    $settings['data'][0]->email_sign=$workshopSetting['email']['email_sign'];
            }
    $orgDetail=getOrgDetail();
    $user=[];
    if(isset($mail['candidateId'])){
        $user=getCandidateUser($mail['candidateId']);

        $mail['candidate_fname']=isset($user['fname'])?$user['fname']:'';
        $mail['candidate_lname']=isset($user['lname'])?$user['lname']:'';
        $mail['candidate_company']=isset($user->userSkillCompany->text_input)?$user->userSkillCompany->text_input:'';
        $mail['candidate_email']=isset($user['email'])?$user['email']:'';
        $mail['candidate_phone']=isset($user['phone'])?$user['phone']:'';
        $mail['candidate_address']=isset($user['address'])?$user['address']:'';
    }
@endphp
@include('email_template.header',$settings)

@php
    $keywords =
     [

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
 '[[MessageCategory]]',
 '[[OrgName]]',
 '[[OrgShortName]]',
 '[[UserFirstName]]',
 '[[UserLastName]]',
 '[[UserEmail]]',
 '[[SignatoryFname]]',
 '[[Signatorylname]]',
 '[[SignatoryPossition]]',
 '[[SignatoryEmail]]',
 '[[SignatoryPhone]]',
 '[[SignatoryMobile]]',
 '[[candidateFN]]',
 '[[candidateLN]]',
 '[[candidateCompanyName]]',
 '[[candidateEmail]]',
 '[[candidatePhone]]',
 '[[CandidateAddress]]',
 '[[referreeLN]]',
 '[[referreeFN]]',
 '[[referreeCompanyName]]',
 '[[DomainOfTheMagicLink]]',
 '[[listOfDomainsGranted]]',
 '[[CardDateOfValidation]]',
 '[[CardExpirationDate]]',
 ];
 $values =[

 $mail['workshop_data']->workshop_name,
 $mail['workshop_data']->code1,
 $member['p']['fullname'],
 $member['v']['fullname'],
 '',
 '',
 '',
 '',
 $member['v']['email'],
 $member['p']['email'],
 $member['p']['phone'],
 '',
 $orgDetail->name_org,
 $orgDetail->acronym,
 (\Auth::check())?\Auth::user()->fname:'',
 (\Auth::check())?\Auth::user()->lname:'',
 (\Auth::check())?\Auth::user()->email:'',
 //signatory tag
 isset($WorkshopSignatory['signatory_fname'])?$WorkshopSignatory['signatory_fname']:'',
 isset($WorkshopSignatory['signatory_lname'])?$WorkshopSignatory['signatory_lname']:'',
 isset($WorkshopSignatory['signatory_possition'])?$WorkshopSignatory['signatory_possition']:'',
 isset($WorkshopSignatory['signatory_email'])?$WorkshopSignatory['signatory_email']:'',
 isset($WorkshopSignatory['signatory_phone'])?$WorkshopSignatory['signatory_phone']:'',
 isset($WorkshopSignatory['signatory_mobile'])?$WorkshopSignatory['signatory_mobile']:'',
 //candidate tag
 isset($mail['candidate_fname'])?$mail['candidate_fname']:'',
 isset($mail['candidate_lname'])?$mail['candidate_lname']:'',
 isset($mail['candidate_company'])?$mail['candidate_company']:'',
 isset($mail['candidate_email'])?$mail['candidate_email']:'',
 isset($mail['candidate_phone'])?$mail['candidate_phone']:'',
 isset($mail['candidate_address'])?$mail['candidate_address']:'',
 //referrer tag
 isset($mail['firstname'])?$mail['firstname']:'',
 isset($mail['lastname'])?$mail['lastname']:'',
 isset($mail['company'])?$mail['company']:'',
 isset($mail['domainOfMagicLink'])?$mail['domainOfMagicLink']:'',
 //card tag
 isset($mail['domain'])?$mail['domain']:'',
 isset($mail['date']['deliverydate'])?$mail['date']['deliverydate']:'',
 isset($mail['date']['expdeliverydate'])?$mail['date']['expdeliverydate']:'',
 ];
     if(isset($mail['token'])){
         $mail['url']=$mail['url'].'/'.$mail['token'][$mail['email']];
     }
@endphp
<tbody>
<tr>
    <td>{!! str_replace($keywords,$values,$settings['data'][1]->text_before_code) !!}</td>
</tr>

<tr>
    <td>
        {{-- @if(isset($workshopSetting['web']))
        <p style="font-size:36px;color:{{ $workshopSetting['web']['color1'] }}"><strong>{!! implode('-',str_split($mail['otp'])) !!}</strong></p>
    @else --}}

        <p @if(isset($workshopSetting['web']['color1']))
           style="font-size:36px;color:rgba({{implode(',',$workshopSetting['web']['color1'])}})"
           @else
           style="font-size:36px;color:#0a8fc0"
                @endif
        ><strong>{!! implode('-',str_split($mail['otp'])) !!}</strong></p>
        {{-- @endif --}}
    </td>
</tr>
<tr>
    <td>{!! str_replace($keywords,$values,$settings['data'][1]->text_between_code_and_link) !!}</td>
</tr>
<tr>
    <td><a href="{{$mail['path']}}">cliquer ici</a></td>
</tr>
<tr>
    <td>{!! str_replace($keywords,$values,$settings['data'][1]->text_after_link) !!}</td>
</tr>
</tr>
<tr>
    <td>{!! nl2br(e(str_replace($keywords,$values,$settings['data'][0]->email_sign))) !!}</td>
</tr>
</tr>
</tbody>
@include('email_template.footer',$settings)