@extends('carte-pro.layouts.app')

@section('content')
<main id="main-content">
    <section id="home-page">
        <div class="container">
            <div class="row flex-box">
                <div class="col-xs-12 col-sm-6 col-md-6 pull-right">
                    <figure>
                        <img width="415" height="325" src="{{ asset('public/carte_pro/images/card.jpg') }}"
                             class="img-responsive center-block wp-post-image" alt=""
                             sizes="(max-width: 415px) 100vw, 415px"/>
                    </figure>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-6 tp-text pull-left">
                    <h1>La carte TP-Pro</h1>
                    <h4>La carte des professionnels du BTP</h4>
                    <p>
                        <a href="{{ route('demandez-votre-carte', $account) }}">DEMANDEZ VOTRE CARTE </a>
                    </p>
                    <p>Les avantages de la carte </p>
                    <ul>
                        <li><a href="#">Pour les entreprises</a></li>
                        <li><a href="#">Pour les maîtres d’ouvrage et maîtres d’oeuvre</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection


{{--
    Footer will come here
--}}
