@php

    $settings['data'] = getEmailSetting(['email_graphic']);

@endphp
@include('email_template.header',$settings)
<tbody>
@foreach($mail['body'] as $val)
    <tr>
        <td>{!! nl2br($val) !!}</td>
    </tr>

@endforeach

@foreach($mail['footer'] as $key=>$val)
    <tr>
        <td>{{trans('message.'.$key)}} : {!! nl2br($val) !!}</td>
    </tr>

@endforeach
</tbody>
@include('email_template.footer',$settings)
