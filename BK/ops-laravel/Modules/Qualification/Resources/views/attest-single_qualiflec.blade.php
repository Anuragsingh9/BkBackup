<!DOCTYPE html>
<html>
<head>
	<title>Carte TPPRO Artisan</title>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="{{ URL::asset('public/css/attest-style.css')}}" />
</head>
<body class="pdf-bg-wrap">
	<header id="header" class="clearfix">
		<div class="inner-wrap flexbox">
			<div class="header-col-left">
				{{-- <img src={{url('public/qualification/attest/carte_tppro_artisan.jpg')}} class="img-responsive" /> --}}
				<img src={{url('public/qualification/attest/logo_qualifelec.png')}} class="img-responsive" />
			</div>
			{{-- <div class="header-col-center">
				<div class="">
					<h1><strong>Attestation</strong>De Travaux 2019</h1>
				</div>
			</div>
			<div class="header-col-right">
				<img src={{url('public/qualification/attest/ffb-logo.png')}} class="img-responsive" />
			</div> --}}
		</div>
	</header>
	{{-- {{dd($basic['candidate']['user_skill_siret']['numerical_input'])}} --}}

	<main class="clearfix">
		<section id="" class="clearfix">
			<div class="inner-wrap clearfix">
				<div class="clearfix text-center">
					<h1 class="main-heading">Attestation de travaux</h1>
				</div>
				<div class="sec-first-detail">
					<div class="sec1-left-col">
						J’atteste que l’entreprise
					</div>
					<div class="sec1-right-col">
						<div class="sec1-right-first-col">
							<div class="label-detail">
								<ul>
									<li><span class="label"></span>
										@if(isset($basic['candidate']['user_skill_company']['text_input']))
											{{--@php
												$company='';
                                                    $str=$basic['candidate']['user_skill_company']['text_input'];
                                                        $exp=explode(' ',$str);
                                                      if(strlen($exp[0]) > 18){
                                                      $trimstring = substr($str, 0, 18). '...';
                                                        $company=$trimstring;
                                                      }else{
                                                        $company=$exp[0]. '...';
                                                      }

											@endphp--}}
											<span class="value">{{$basic['candidate']['user_skill_company']['text_input']?? 'Martin & Fils'}}</span>
										@endif

									</li>
									<li><span class="label">SIRET :</span>
										@if(isset($basic['candidate']['user_skill_siret']['numerical_input']))
										<span class="value">{{$basic['candidate']['user_skill_siret']['numerical_input']?? '123456AZ790'}}</span>
										@endif
									</li>
									<li><span class="label">Adresse : </span>
									@if(isset($basic['candidate']['address']))
										<span class="value">{{$basic['candidate']['address'] ?? 'Candidate_address'}}</span>
									@endif
								</li>
									<li><span class="label">Code postal:</span>
										@if(isset($basic['candidate']['postal'] ))
											<span class="value">{{$basic['candidate']['postal'] ?? '00019'}}</span></li>        
										@endif
									{{--<li>
										<span class="label">Ville :</span>
										@if(isset($basic['candidate']['postal'] ))
											<span class="value">{{$basic['candidate']['city'] ?? 'Bateauville'}}</span>
											@endif
									</li>--}}
								</ul>
							</div>
						</div>
						<div class="sec1-right-second-col">
							<h3>a bien réalisé pour moi des travaux dans le domaine suivant :</h3>
							<ul>
								@if(isset($domain[0]['name']) )
									<li class="active">{{$domain[0]['name'] ?$domain[0]['name']: 'Aménagements, voiries et routes'}}</li>
								<!-- <li>Aménagements, voiries et routes</li> -->
								@endif
							</ul>
						</div>
					</div>
				</div>
			</div>
		</section>
		{{-- <section id="building-img-bg" class="clearfix">
			<img src={{url('public/qualification/attest/building-icons-img.jpg')}} class="img-responsive" width="100%"/>
		</section> --}}
		<section id="works-details" class="clearfix">
			<div class="inner-wrap clearfix">
				<div class="work-detail-top-sec clearfix flexbox">
					<div class="works-details-left">
						Détails des travaux
					</div>
					<div class="work-place text-right">
						<h5>Lieu des travaux</h5>
						<div class="address text-right">
						
						</div>
						<div class="postal-code text-right">
						
						</div>
{{--						<div class="ville text-right">--}}
{{--					--}}
{{--						</div>--}}
					</div>
					<div class="work-dates">
						<h5>Début des travaux</h5>
						<div class="date-start"></div>
						<h5>Fin des travaux</h5>
						<div class="date-end"></div>
					</div>
					<div class="service-review">
						<h5>Appréciation des prestations</h5>
						<div class="service-rank">
							<div class="radio-style1">
								<label>
									<input type="radio" name="service_rank" id="" class="" />
									<span class="radio-icon"></span>
									Excellent
								</label>
							</div>
							<div class="radio-style1">
								<label>
									<input type="radio" name="service_rank" id="" class="" />
									<span class="radio-icon"></span>
									Bien
								</label>
							</div>
							<div class="radio-style1">
								<label>
									<input type="radio" name="service_rank" id="" class="" />
									<span class="radio-icon"></span>
									Moyen
								</label>
							</div>
							<div class="radio-style1">
								<label>
									<input type="radio" name="service_rank" id="" class="" />
									<span class="radio-icon"></span>
									Médiocre
								</label>
							</div>
						</div>
					</div>
				</div>
				<div class="work-detail-bottom-sec clearfix flexbox">
					<div class="work-detail-btm-lsec text-right">
						<div class="txt-value-field">
							<span class="text-label">Montant des travaux TTC (en €) :</span>
							<span class="text-value"></span>
						</div>
						
						<div class="txt-value-field">
							<span class="text-label">Des travaux ont-ils été sous-traités ?</span>
							<div class="radio-inline">
								<div class="radio-style1">
									<label>
										<input type="radio" name="service_rank" id="" class="" />
										<span class="radio-icon"></span>
										OUI
									</label>
								</div>
								<div class="radio-style1">
									<label>
										<input type="radio" name="service_rank" id="" class="" />
										<span class="radio-icon"></span>
										NON
									</label>
								</div>
							</div>
						</div>
						
						<div class="txt-value-field">
							<span class="text-label">Si oui, montant des travaux sous-traités (en €) :</span>
							<span class="text-value"></span>
						</div>
						
						<div class="txt-value-field">
							<span class="text-label">Nature des travaux sous-traités :</span>
							<span class="text-value"></span>
						</div>
					</div>
					<div class="work-detail-btm-rsec">
						<label>Commentaires :</label>
						<textarea></textarea>
					</div>
				</div>
			</div>
		</section>
	
		<section id="" class="clearfix" style="min-height: 400px;">
			<div class="inner-wrap clearfix">		
				<div class="main-detail-sec clearfix flexbox">
					<div class="main-detail-sec-lsec text-right">
						<div class="txt-value-field">
							<span class="text-label">Vous êtes :</span>
							<div class="radio-inline">
								<div class="radio-style1">
									<label>
										<input type="radio" name="you_are" id="" class="" />
										<span class="radio-icon"></span>
										Maître d’ouvrage
									</label>
								</div>
								<div class="radio-style1">
									<label>
										<input type="radio" name="you_are" id="" class="" />
										<span class="radio-icon"></span>
										Maître d’oeuvre
									</label>
								</div>
							</div>
						</div>
						
						<div class="txt-value-field">
							<span class="text-label">Nom de la Structure :</span>
							<span class="text-value"></span>
						</div>
						
						<div class="txt-value-field address-block">
							<span class="text-label">Adresse :</span>
							<span class="text-value"></span>
						</div>
						
{{--						<div class="txt-value-field">--}}
{{--							<span class="text-label">Ville :</span>--}}
{{--							<span class="text-value"></span>--}}
{{--						</div>--}}
						<div class="txt-value-field">
							<span class="text-label">Téléphone :</span>
							<span class="text-value"></span>
						</div>
						<div class="txt-value-field">
							<span class="text-label">Email :</span>
							<span class="text-value"></span>
						</div>
					</div>
					<div class="main-detail-sec-rsec">
						<div class="txt-value-field">
							<span class="text-label">Prénom :</span>
							<span class="text-value"></span>
						</div>
						<div class="txt-value-field">
							<span class="text-label">Nom :</span>
							<span class="text-value"></span>
						</div>
						<div class="txt-value-field">
							<span class="text-label">Fonction :</span>
							<span class="text-value"></span>
						</div>
						<div class="txt-value-field">
							<span class="text-label">Date:</span>
							<span class="text-value"></span>
						</div>
						<div class="txt-value-field">
							<span class="text-label">Signature :</span>
							<div class="signature-block">
								<img src="" class="signature-img" />
							</div>
						</div>
						
					</div>
				</div>
			</div>
		</section>
	</main>

    {{-- <footer></footer> --}}

</body>
</html>