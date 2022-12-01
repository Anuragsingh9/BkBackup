@php

    $settings['data'] = getEmailSetting(['email_graphic',$mail['template_setting']]);
    if(isset($mail['workshop_data'])){
   $member=workshopValidatorPresident($mail['workshop_data']);
    $workshopSetting=getWorkshopSettingData($mail['workshop_data']->id);
    $WorkshopSignatory=getWorkshopSignatoryData($mail['workshop_data']->id);
            if(isset($workshopSetting['email'])){
                    $settings['data'][0]->top_banner=env('AWS_PATH').$workshopSetting['email']['top_banner'];
                    $settings['data'][0]->bottom_banner=env('AWS_PATH').$workshopSetting['email']['bottom_banner'];
                    $settings['data'][0]->email_sign=$workshopSetting['email']['email_sign'];
            }

            }
    $orgDetail=getOrgDetail();
    $user=[];
    $mail['enable_signature'] = isset($mail['enable_signature']) ? $mail['enable_signature'] : TRUE;
    if(isset($mail['candidateId'])){
        $user=getCandidateUser($mail['candidateId']);
        $mail['candidate_fname']=isset($user['fname'])?$user['fname']:'';
        $mail['candidate_lname']=isset($user['lname'])?$user['lname']:'';
        $mail['candidate_company']=isset($user['company'])?$user['company']:'';
        $mail['candidate_email']=isset($user['email'])?$user['email']:'';
        $mail['candidate_phone']=isset($user['phone'])?$user['phone']:'';
        $mail['candidate_address']=isset($user['address'])?$user['address']:'';
        if($mail['candidate_company']==''){
           $mail['candidate_company']= isset($user->userSkillCompany->text_input)?$user->userSkillCompany->text_input: '';
        }
    }
@endphp
​
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
'[[WorkshopSecEmail]]',
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
'[[referreeFN]]',
'[[referreeLN]]',
'[[referreeCompanyName]]',
'[[DomainOfTheMagicLink]]',
'[[listOfDomainsGranted]]',
'[[CardDateOfValidation]]',
'[[CardExpirationDate]]',
'[[EventName]]',
'[[OrganiserFN]]',
'[[OrganiserEmail]]' ,
'[[ParticipantFN]]',
'[[ParticipantLN]]',
'[[RecipientFirstName]]',
 '[[RecipientLastName]]',
'[[ReinventUrl]]',
'[[AccountLongName]]',
 '[[AccountShortName]]',
 '[[ConsultationName]]',
 '[[CommitteeName]]',
 '[[CommiteeName]]',
 '[[ConsultationVeryLongName]]',
 '[[ConsulationVeryLongName]]',
 '[[CommitteeSecFirstName]]',
 '[[CommiteeSecLastName]]',
 '[[CommitteePresidentFullName]]',
 '[[CommitteevalidatorFullName]]',
 '[[CommitteeValidatorEmail]]',
 '[[CommitteePresidentEmail]]',
 '[[staticSiteUrl]]',
 '[[NewOption]]',
 '[[Sprint]]',
 '[[Step]]',
 '[[Question]]',
 '[[DateTime]]',
];
$values =[

$mail['workshop_data']->workshop_name,
$mail['workshop_data']->code1,
$member['p']['fullname'],
isset($member['v']['fullname'])?$member['v']['fullname']:'',
isset($mail['meeting']->name)?$mail['meeting']->name:'',
isset($mail['meeting']->date)?$mail['meeting']->date:'',
isset($mail['meeting']->start_time)?$mail['meeting']->start_time:'',
isset($mail['meeting']->place)?$mail['meeting']->place:'',
$member['v']['email'],
$member['p']['email'],
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
isset($mail['event']->title)?$mail['event']->title:'',
isset($mail['organiser']->fname)?$mail['organiser']->fname. ' '. $mail['organiser']->lname:'',
isset($mail['organiser']->email)?$mail['organiser']->email:'',
isset($mail['participant']->fname)?$mail['participant']->fname:'',
isset($mail['participant']->lname)?$mail['participant']->lname:'',
isset($mail['participant']->fname)?$mail['participant']->fname:'',
isset($mail['participant']->lname)?$mail['participant']->lname:'',
 isset($mail['reinvent_url'])?$mail['reinvent_url']:'',
 isset($mail['acc_long_name'])?$mail['acc_long_name']:'',
 isset($mail['acc_short_name'])?$mail['acc_short_name']:'',
isset($mail['consultation'])?$mail['consultation']:'',
 isset($mail['committe_name'])?$mail['committe_name']:'',
 isset($mail['committe_name'])?$mail['committe_name']:'',
 isset($mail['ConsultationVeryLongName'])?$mail['ConsultationVeryLongName']:'',
 isset($mail['ConsultationVeryLongName'])?$mail['ConsultationVeryLongName']:'',
 isset($mail['CommitteeSecFirstName'])?$mail['CommitteeSecFirstName']:'',
 isset($mail['CommiteeSecLastName'])?$mail['CommiteeSecLastName']:'',
 isset($mail['committe_PresidentFullName'])?$mail['committe_PresidentFullName']:'',
isset($mail['committe_ValidatorFullName'])?$mail['committe_ValidatorFullName']:'',
isset($mail['committe_ValidatorEmail'])?$mail['committe_ValidatorEmail']:'',
isset($mail['committe_PresidentEmail'])?$mail['committe_PresidentEmail']:'',
isset($mail['staticSiteUrl'])?$mail['staticSiteUrl']:'',
isset($mail['NewOption'])?$mail['NewOption']:'',
isset($mail['sprint'])?$mail['sprint']:'',
isset($mail['step'])?$mail['step']:'',
isset($mail['question'])?$mail['question']:'',
isset($mail['dateTime'])?$mail['dateTime']:'',
];
    if(isset($mail['token'])){

        $mail['url']=$mail['url'].'/'.$mail['token'];
    }
@endphp
<tbody>
<tr>
    <td>{!! str_replace($keywords,$values,$settings['data'][1]->text_before_link) !!}</td>
</tr>
@if(isset($mail['url']))
    @if(is_array($mail['url']))
        @foreach ($mail['url'] as $url)
            <tr>
                <td>{{$url['name']}} - <a href="{{$url['link']}}">Cliquez ici</a></td>
            </tr>
        @endforeach
    @else
        <tr>
            <td><a href="{{$mail['url']}}">Cliquez ici</a></td>
        </tr>
    @endif
@endif
<tr>
    <td>{!! (str_replace($keywords,$values,$settings['data'][1]->text_after_link)) !!}</td>
</tr>
@if($mail['enable_signature'])
    <tr>
        <td>{!! nl2br(e(str_replace($keywords,$values,$settings['data'][0]->email_sign))) !!}</td>
    </tr>
@endif
</tbody>
@include('email_template.footer',$settings)