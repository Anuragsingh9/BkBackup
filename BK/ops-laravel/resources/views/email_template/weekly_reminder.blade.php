@php
    $data = getEmailSetting(['email_graphic','doodle_email_setting']);
    $css_data = dynamicCss();
    $color1 = $css_data['color1'];
    $color2= $css_data['color2'];
    $color3= $css_data['color3'];
    $transprancy7=$css_data['transprancy7'];
    $transprancy1=$css_data['transprancy1'];
    $transprancy2=$css_data['transprancy2'];

@endphp
        <!DOCTYPE html>
<html>
<head>
    <title>Pa Simplify</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8 " />
    <style>
        p{
            margin-top: 0px;
            margin-bottom: 0px;
            -webkit-margin-before: 0px !important;
            -webkit-margin-after: 0px !important;
            -webkit-margin-start: 0px !important;
            -webkit-margin-end: 0px !important;
        }
    </style>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
</head>

<body style="width: 600px; max-width: 600px;" >
<table width="600" style="width: 600px; max-width: 600px;">
    <thead style="width: 600px; max-width: 600px !important;">
    <tr style="width: 600px; max-width: 600px !important; overflow: hidden;">
        <td style="width: 600px; max-width: 600px !important; overflow: hidden;">
            <img src="{{ $data[0]->top_banner }}" width="600" style="width:600px !important;"/>
        </td>
    </tr>
    </thead>
    <tbody>
    {{-- <tr><td>{!! ((str_replace($keywords,$values,$settings['data'][1]->text_before_link))) !!}</td></tr>
    <tr><td><a href="{{$mail['url']}}">Cliquez ici</a></td></tr>
    <tr><td>{!! (str_replace($keywords,$values,$settings['data'][1]->text_after_link)) !!}</td></tr> --}}
    

    {{--<style>--}}
        {{--.centerDiv {--}}
            {{--max-width: 600px;--}}
            {{--width: 100%;--}}
            {{--margin: 0 auto;--}}
        {{--}--}}

        {{--.borderDiv {--}}
            {{--border: 1px solid #ccc;--}}
            {{--padding: 30px 15px 15px 15px;--}}
        {{--}--}}

        {{--.border-right {--}}
            {{--border-right: 1px solid #ccc--}}
        {{--}--}}

        {{--.border-top {--}}
            {{--border-top: 1px solid #ccc--}}
        {{--}--}}

        {{--.divHeading {--}}
            {{--background: #005dd0;--}}
            {{--padding: 5px 10px;--}}
            {{--color: #fff;--}}
            {{--font-size: 18px;--}}
            {{--font-family: 'Lato', sans-serif;--}}
        {{--}--}}

        {{--table {--}}
            {{--border: 1px solid #ccc;--}}
        {{--}--}}

        {{--table tr th,--}}
        {{--table tr td {--}}
            {{--padding: 8px;--}}
            {{--border-spacing: 0;--}}
            {{--font-family: 'Lato', sans-serif;--}}
            {{--font-size: 14px;--}}
        {{--}--}}

        {{--table tr th {--}}
            {{--border-top: 2px solid #ccc;--}}
        {{--}--}}
        {{--.tableTopHeading {--}}
            {{--margin: 0;--}}
            {{--font-size: 24px;--}}
            {{--padding: 15px;--}}
            {{--font-family: 'Lato', sans-serif;--}}
        {{--}--}}
    {{--</style>--}}
    <tr>
        <td>
    
    <div class="centerDiv" style="width: 590px; max-width: 590px; margin-bottom: 15px; margin-top:20px;">
        <p style="font-size: 12px; width: 100%; margin-bottom: 10px;"><strong>Bonjour,</strong></p>
        <p style="font-size: 12px; width: 100%; margin-bottom: 10px;"><strong>Voici votre récapitulatif de la semaine à venir :</strong></p>
    </div>
</td>
</tr>
<tr>
<td>
    @if(count($doodle_workshop['workshop'] )>0)
    <div class="centerDiv" style="width: 590px; max-width: 590px; margin-bottom: 15px;">
        {{--<h3 class="tableTopHeading">Table Data</h3>--}}
        <table class="tableDiv" style="width: 590px; max-width: 590px;border-color: #ccc;border-width: 0px 1px 1px 1px;border-style: solid; margin-bottom: 20px;" cellpadding="0" cellspacing="0">
            {{--<div class=" divHeading " style="background: {{$color2}};padding: 5px 10px;color: #fff;font-size: 13px;font-family: 'Lato', sans-serif; margin-bottom:10px;">MES COMMISSIONS ACTIVES</div>--}}
            <thead style="width: 590px; max-width: 590px; border:0;" cellpadding="0" cellspacing="0">
                <tr style="width: 590px; max-width: 590px;">
                    <td style="background: {{$color2}};padding: 5px 10px;color: #fff;font-size: 13px;font-family: 'Lato', sans-serif; margin-bottom:0px;">MES COMMISSIONS ACTIVES </td>
                </tr>
            </thead>
            <tbody class="borderDiv" style="width: 590px !important; max-width: 590px;">
                <tr style="max-width: 590px;width: 590px !important;">
                    <td style="padding: 20px;">
                        <table class="table-bordered table-hovered" style="width: 550px;border: 1px solid #d0d0d0;border-spacing: 0px;border-top: 0px;" cellpadding="0" cellspacing="0">
                            <thead style="width: 550px;display: table-header-group;">
                                <tr style="background-color:#f7f7f7;display:table-row;width:100%;border-top: 2px solid #006ab0;">
                                    <th width="120" style="border-spacing:0;padding: 5px;borderfont-family: 'Lato', sans-serif;font-size: 10px;border-right: 1px solid #d0d0d0;display:table-cell;border-top: 2px solid {{$color2}};">Commission ou GT</th>
                                    <th style="border-spacing:0;padding: 5px;;font-family: 'Lato', sans-serif;font-size: 10px;border-right: 1px solid #d0d0d0;display:table-cell;border-top: 2px solid {{$color2}};">Dernière réunion</th>
                                    <th style="border-spacing:0;padding: 5px;font-family: 'Lato', sans-serif;font-size: 10px;display:table-cell;border-top: 2px solid {{$color2}};">Prochaine réunion</th>
                                </tr>
                            </thead>
                            <tbody style="display:table-row-group; width: 560px!important;">

                                @forelse ($doodle_workshop['workshop'] as $workshop)
                                    <tr style="display:table-row;width:100%;@if(($loop->index%2)==1) background:#f9f9f9; @endif">
                                        <td style="border-spacing:0;padding: 5px;font-family: 'Lato', sans-serif;font-size: 10px;border-right: 1px solid transparent;border-right: 1px solid #d0d0d0;display:table-cell;border-top: 1px solid #ccc;">{{$workshop['workshop_name']}}</td>

                                        <td style="border-spacing:0;padding: 5px;font-family: 'Lato', sans-serif;font-size: 10px;border-right: 1px solid transparent;border-right: 1px solid #d0d0d0;display:table-cell;border-top: 1px solid #ccc;">
                                            @if(count($workshop['last_meeting'] )>0)

                                                {{($workshop['last_meeting']->date!=null) ? \Carbon\Carbon::parse($workshop['last_meeting']->date.' '.$workshop['last_meeting']->start_time)->format('d/m/Y H:m') : ''}}
                                                @if(($workshop['last_meeting']->validated_repd==1))
                                                    <br>
                                                    <span className="show mt-5">Voir le relevé de décisions</span>
                                                @else
                                                    <br>
                                                    <span className="show mt-5">Relevé de décisions en cours</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td style="border-spacing:0;padding: 5px;border-spacing: 0;font-family: 'Lato', sans-serif;font-size: 10px;display:table-cell;border-top: 1px solid #ccc;">
                                            @if(count($workshop['next_meeting'] )>0)
                                                {{($workshop['next_meeting']->date!=null) ? \Carbon\Carbon::parse($workshop['next_meeting']->date.' '.$workshop['next_meeting']->start_time)->format('d/m/Y H:m') : ''}}
                                                @if(($workshop['next_meeting']->validated_prepd==1))
                                                    <br>
                                                    <span className="show mt-5">Voir l'ordre du jour</span>
                                                @else
                                                    <br>
                                                    <span className="show mt-5">Ordre du jour en cours</span>
                                                @endif
                                            @else
                                                <br>
                                                <span className="show mt-5">Pas de prochaine réunion</span>

                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                @endforelse

                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif
</td>
</tr>
<tr>
<td>
    @if(count($doodle_workshop['workshop_doodle'])>0)
    <div class="centerDiv" style="width: 590px; max-width: 590px; margin-bottom: 15px;">
        <table class="tableDiv" style="width: 590px; max-width: 590px;border-color: #ccc;border-width: 0px 1px 1px 1px;border-style: solid; margin-bottom: 20px;">
            {{--<div class=" divHeading " style="background: {{$color2}};padding: 5px 10px;color: #fff;font-size: 13px;font-family: 'Lato', sans-serif; margin-bottom:10px;">RÉUNION EN ATTENTE DE MES DISPONIBILITÉS</div>--}}
            <thead style="width: 590px; max-width: 590px; border:0;" cellpadding="0" cellspacing="0">
                <tr style="width: 590px; max-width: 590px;" cellpadding="0" cellspacing="0">
                    <td style="background: {{$color2}};padding: 5px 10px;color: #fff;font-size: 13px;font-family: 'Lato', sans-serif; margin-bottom:0px;" cellpadding="0" cellspacing="0">RÉUNION EN ATTENTE DE MES DISPONIBILITÉS
                    </td>
                </tr>
            </thead>
            <tbody class="borderDiv" style="width: 590px !important; max-width: 590px;">
                <tr style="max-width: 590px;width: 590px !important;">
                    <td style="padding: 20px;">
                        <table class="table-bordered table-hovered" style="width: 550px;border: 1px solid #d0d0d0;border-spacing: 0px;border-top: 0px;">
                            <thead style="width: 550px;display: table-header-group;">
                                <tr style="background-color:#f7f7f7;display:table-row;width: 550px;border-top: 2px solid #006ab0;">
                                    <th width="120" style="border-spacing:0;padding: 5px;borderfont-family: 'Lato', sans-serif;font-size: 10px;border-right: 1px solid #d0d0d0;display:table-cell;border-top: 2px solid {{$color2}};">Commission ou GT</th>
                                    <th style="border-spacing:0;padding: 5px;font-family: 'Lato', sans-serif;font-size: 10px;border-right: 1px solid #d0d0d0;display:table-cell;border-top: 2px solid {{$color2}};">Tâche à venir</th>
                                </tr>
                            </thead>
                            <tbody style="display:table-row-group; width: 550px;">

                                @forelse ($doodle_workshop['workshop_doodle'] as $workshops)
                                    <tr style="display:table-row;width:100%; @if(($loop->index%2)==1) background:#f9f9f9; @endif">
                                        <td style="border-spacing:0;padding: 5px;font-family: 'Lato', sans-serif;font-size: 10px;border-right: 1px solid transparent; vertical-align: top; border-right: 1px solid #d0d0d0;display:table-cell;border-top: 1px solid #ccc;">{{$workshops['workshop_name']}}</td>

                                        <td style="border-spacing:0;padding: 5px;font-family: 'Lato', sans-serif;font-size: 10px;line-height: 16px; display:table-cell;vertical-align: top;border-top: 1px solid #ccc;">
                                            @if(count($workshops['meetings'])>0 )
                                                @foreach($workshops['meetings'] as $meeting)
                                                    @if(($user->role=='M2' || $user->role=='M3'))

                                                        <span className="show mt-5"><strong>{{$meeting->name}}</strong>
                                                Veuillez confirmer vos disponibilités</span>   <br>
                                                    @else
                                                        @if(($meeting->doodleDates->count() >0))
                                                        <span className="show mt-5"><strong>{{$meeting->name}}</strong>
                                                Veuillez confirmer vos disponibilités</span>   <br>
                                                    @endif
                                                    @endif

                                                @endforeach
                                            @endif
                                        </td>
                                    </tr>
                                @empty

                                @endforelse

                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif
</td>
</tr>
<tr>
<td>
    @if(count($docs)>0)
    <div class="centerDiv" style="width: 590px; max-width: 590px; margin-bottom: 15px;">
        <table class="tableDiv " style="width: 590px; max-width: 590px;border-color: #ccc;border-width: 0px 1px 1px 1px;border-style: solid; margin-bottom: 20px;">
            {{--<div class=" divHeading " style="background: {{$color2}};padding: 5px 10px;color: #fff;font-size: 13px;font-family: 'Lato', sans-serif; margin-bottom:10px;">DERNIERS DOCUMENTS</div>--}}
            <thead style="width: 590px; max-width: 590px; border:0;" cellpadding="0" cellspacing="0">
                <tr style="width: 590px; max-width: 590px;">
                    <td style=" background: {{$color2}};;padding: 5px 10px;color: #fff;font-size: 13px;font-family: 'Lato', sans-serif; margin-bottom:0px;">DERNIERS DOCUMENTS
                    </td>
                </tr>
            </thead>
            <tbody class="borderDiv" style="width: 590px !important; max-width: 590px;">
                <tr style="max-width: 590px;width: 590px !important;">
                    <td style="padding: 20px;">
                        <table class="table-bordered table-hovered" style="width: 550px;border: 1px solid #d0d0d0;border-spacing: 0px;border-top: 0px;">
                            <thead style="width: 550px;display: table-header-group;">
                                <tr style="background-color:#f7f7f7;display:table-row;width: 550px;border-top: 2px solid #006ab0;">
                                    <th width="120" style="border-spacing:0;padding: 5px;borderfont-family: 'Lato', sans-serif;font-size: 10px;border-right: 1px solid #d0d0d0;display:table-cell;border-top: 2px solid {{$color2}};">Commission ou GT</th>
                                    <th style="border-spacing:0;padding: 5px;font-family: 'Lato', sans-serif;font-size: 10px;border-right: 1px solid #d0d0d0;display:table-cell;border-top: 2px solid {{$color2}};">Nouveaux documents</th>
                                </tr>
                            </thead>
                            <tbody style="display:table-row-group; width: 550px;">
                                @forelse ($docs as $doc)
                                    <tr style="display:table-row;width:100%; @if(($loop->index%2)==1) background:#f9f9f9; @endif">
                                        <td style="border-spacing:0;padding: 5px;font-family: 'Lato', sans-serif;font-size: 10px;border-right: 1px solid transparent; vertical-align: top; border-right: 1px solid #d0d0d0;display:table-cell;border-top: 1px solid #ccc;">{{$doc->workshop_name}} </td>

                                        <td style="border-spacing:0;padding: 5px;font-family: 'Lato', sans-serif;font-size: 10px;display:table-cell;border-top: 1px solid #ccc;">
                                            {{$doc->document_count}} {{($doc->document_count>1)?'Nouveaux documents en ligne':'Nouveau document en ligne'}}
                                        </td>
                                    </tr>
                                @empty

                                @endforelse

                            </tbody>
                        </table>   
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif
</td>
</tr>
<tr>
<td>
    @if(count($task)>0)
    <div class="centerDiv" style="width: 590px; max-width: 590px; margin-bottom: 15px;">
        <table class="tableDiv" style="width: 590px; max-width: 590px;border-color: #ccc;border-width: 0px 1px 1px 1px;border-style: solid; margin-bottom: 20px;">
            {{--<div class="divHeading " style="background: {{$color2}};padding: 5px 10px;color: #fff;font-size: 13px;font-family: 'Lato', sans-serif; margin-bottom:10px;">MES TÂCHES</div>--}}
            <thead style="width: 590px; max-width: 590px; border:0;" cellpadding="0" cellspacing="0">
                <tr style="width: 590px; max-width: 590px;">
                    <td style=" background: {{$color2}};;padding: 5px 10px;color: #fff;font-size: 13px;font-family: 'Lato', sans-serif; margin-bottom:0px;">MES TÂCHES </td>
                </tr>
            </thead>
            <tbody class="borderDiv" style="width: 590px !important; max-width: 590px;">
                <tr style="max-width: 590px;width: 590px !important;">
                    <td style="padding: 20px;">
                        <table class="table-bordered table-hovered" style="width: 550px;border: 1px solid #d0d0d0;border-spacing: 0px;border-top: 0px;">
                            <thead style="width: 550px;display: table-header-group;">
                            <tr style="background-color:#f7f7f7;display:table-row;width: 550px;border-top: 2px solid #006ab0;">
                                <th width="120" style="border-spacing:0;padding: 5px;borderfont-family: 'Lato', sans-serif;font-size: 10px;border-right: 1px solid #d0d0d0;display:table-cell;border-top: 2px solid {{$color2}};">Commission ou GT</th>
                                <th style="border-spacing:0;padding: 5px;;font-family: 'Lato', sans-serif;font-size: 10px;border-right: 1px solid #d0d0d0;display:table-cell;border-top: 2px solid {{$color2}};">Tâche à venir</th>
                            </tr>
                            </thead>
                            <tbody style="width: 560px !important;">

                                @forelse ($task as $tasks)

                                    <tr style="display:table-row;width: 550px;@if(($loop->index%2)==1) background:#f9f9f9; @endif">
                                        <td style="border-spacing:0;padding: 5px;font-family: 'Lato', sans-serif;font-size: 10px;border-right: 1px solid transparent;border-right: 1px solid #d0d0d0;display:table-cell;border-top: 1px solid #ccc;">{{$tasks['workshop_name']}} </td>

                                        <td style="border-spacing:0;padding: 5px;font-family: 'Lato', sans-serif;font-size: 10px;display:table-cell;border-top: 1px solid #ccc;">
                                            @foreach($tasks['task'] as $val)
                                                <div >{{Carbon\Carbon::parse($val->end_date)->format('d/m/Y')}} : {{$val->task_text}} </div>
                                            @endforeach
                                        </td>

                                    </tr>

                                @empty
                                    <tr style="display:table-row;width: 550px;">
                                        <td colspan="2" style="border-spacing:0;padding: 5px;font-family: 'Lato', sans-serif;font-size: 10px;display:table-cell;border-top: 1px solid #ccc;">No Data</tr>
                                @endforelse

                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif
</td>
</tr>
    {{--design for project--}}
   {{-- <div class="centerDiv" style="width: 590px; max-width: 590px; margin-bottom: 15px;">
        <div class="tableDiv" style="width: 590px; max-width: 590px;border-color: #ccc;border-width: 0px 1px 1px 1px;border-style: solid;">
            <div class="divHeading" style="background: {{$color2}};padding: 5px 10px;color: #fff;font-size: 13px;font-family: 'Lato', sans-serif; margin-bottom:10px;">Vos projets en un clin d’oeil</div>
            <tbody class="borderDiv" style="width: 590px !important; max-width: 590px;">
                <table class="table-bordered table-hovered" style="width: 550px;border: 1px solid #d0d0d0;border-spacing: 0px;border-top: 0px;">
                    <thead style="width: 550px;display: table-header-group;">
                    <tr style="background-color:#f7f7f7;display:table-row;width: 550px;border-top: 2px solid #006ab0;">
                        <th width="120"
                            style="border-spacing:0;padding: 5px;borderfont-family: 'Lato', sans-serif;font-size: 10px;border-right: 1px solid #d0d0d0;display:table-cell;border-top: 2px solid {{$color2}};">
                            Projets
                        </th>
                        <th style="border-spacing:0;padding: 5px;;font-family: 'Lato', sans-serif;font-size: 10px;border-right: 1px solid #d0d0d0;display:table-cell;border-top: 2px solid {{$color2}};">
                            Milestones
                        </th>
                        <th style="border-spacing:0;padding: 5px;;font-family: 'Lato', sans-serif;font-size: 10px;border-right: 1px solid #d0d0d0;display:table-cell;border-top: 2px solid {{$color2}};">
                            Tasks
                        </th>
                        <th style="border-spacing:0;padding: 5px;;font-family: 'Lato', sans-serif;font-size: 10px;border-right: 1px solid #d0d0d0;display:table-cell;border-top: 2px solid {{$color2}};">
                            Due for
                        </th>
                    </tr>
                    </thead>
                    <tbody style="display:table-row-group;">
                    {{dd($project->count()}}
                    @if($project->count()>0)
                        @forelse ($project as $doc)

                            <tr style="display:table-row;width: 550px; @if(($loop->index%2)==1) background:#f9f9f9; @endif">
                                <td style="border-spacing:0;padding: 5px;font-family: 'Lato', sans-serif;font-size: 10px;border-right: 1px solid transparent;border-right: 1px solid #d0d0d0;display:table-cell;border-top: 1px solid #ccc;">{{$doc->workshop_name}} </td>

                                <td style="border-spacing:0;padding: 5px;font-family: 'Lato', sans-serif;font-size: 10px;display:table-cell;border-top: 1px solid #ccc;">
                                    {{$doc->document_count}} {{($doc->document_count>1)?'Nouveaux documents en ligne':'Nouveau document en ligne'}}
                                </td>

                            </tr>

                        @empty

                        @endforelse
                    @endif
                    </tbody>
                </table>
            </div>

        </div>
    </div>--}}
    </td>
    </tr>
    </tbody>
    <tfoot style="width: 600px; max-width: 600px !important; overflow: hidden;">
    <tr style="width: 600px; max-width: 600px !important; overflow: hidden;">
        <td style="width: 600px; max-width: 600px !important; overflow: hidden;">
            <img src="{{ $data[0]->bottom_banner }}" width="600" style="width:600px !important;"/>
        </td>
    </tr>
    </tfoot>
</table>
</body>
</html>

{{-- @include('email_template.footer',$settings) --}}