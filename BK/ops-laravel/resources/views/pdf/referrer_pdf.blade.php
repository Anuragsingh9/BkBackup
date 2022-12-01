@extends('layouts.master_pdf')
@section('content')
    @php
        $month = array('1'=>'Janvier','2'=>'Février','3'=>'Mars','4'=>'Avril','5'=>'Mai','6'=>'Juin','7'=>'Juillet','8'=>'Août','9'=>'Septembre','10'=>'Octobre','11'=>'Novembre','12'=>'Décembre');
        $days = array('Monday'=>'Lundi','Tuesday'=>'Mardi','Wednesday'=>'Mercredi','Thursday'=>'Jeudi','Friday'=>'Vendredi','Saturday'=>'Samedi','Sunday'=>'Dimanche');
    @endphp
    <div class="container tab-section">
        <div class="col-xs-12 nopadding tab-menu-content">
            <div class="agenda-header"><img class="img-responsive pdf-logo" src="">
            </div>
            <div class="header-line" style=""></div>
            <div class="agenda-heading mt-30 mb-20 clearfix">
                <div class="col-xs-6">
                    <h4 class="pdf-doc-title text-uppercase">RELEVÉ DE Décision</h4>
                </div>
                <div class="col-xs-6 text-right">
                    <h4 class="pdf-doc-name text-uppercase">{{ @$workshop_data->workshop_name }}</h4>

                    <span class="pdf-small">

                </span>
                </div>
            </div>


        </div>
@endsection