@php
    // for getting top banner for header // for previous way
    $settings['data'] = getEmailSetting(['email_graphic']);
@endphp

{{-- this will add the header --}}
@include('email_template.header',$settings)

{{-- Actual email body content here --}}

<tbody>
<tr>
    <td>{!! $text_before_link !!}</td>
</tr>
@if(isset($mail['url']))
    <tr>
        <td><a href="{{$mail['url']}}">{{isset($mail['name']) ?  $mail['name'] : __('cocktail::message.click_here')}}</a></td>
    </tr>
@endif
<tr>
    <td>{!! $text_after_link !!}</td>
</tr>
{{-- old functionality footer --}}
<tr>
    <td>{!! nl2br($settings['data'][0]->email_sign) !!}</td>
</tr>
</tbody>
@include('email_template.footer',$settings)