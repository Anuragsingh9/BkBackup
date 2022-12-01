@extends('launch.header')
@section('content')
<div id="outer-wrapper" class="animate translate-z-in">
        <div id="inner-wrapper">
            <div id="table-wrapper">
                <div class="container">
                    <div id="row-header">
                        <header><a href="#" id="brand" class="animate animate fade-in animation-time-3s"><h2>OP CRM<br/>IMMINENT</h2></a></header>
                    </div>
                    <!--end row-header-->
                    <div id="row-content">
                        <div id="content-wrapper">
                            <div class="row vertical-aligned-wrapper">
                                <div class="col-md-8 col-sm-8 vertical-aligned-element">
                                    <div id="content" class="animate translate-z-out animation-time-2s delay-05s">
                                        <h2 class="opacity-70">Le CRM dont tous les dirigeants d’OPs ont rêvé !</h2>
                                        <h1>CELA VALAIT LA PEINE D’ATTENDRE</h1>
                                        <div class="row">
                                            <div class="col-md-10 col-sm-10">
                                                <p>OP CRM est le fruit de 18 mois de collaboration avec des dirigeants d’organisations professionnelles accompagnés de leurs collaborateurs pour trouver les 10 raisons pour lesquelles 94% des dirigeants d’Ops n’étaient pas satisfaits de leur CRM ou faisaient l’impasse sur le CRM.</p>
                                                <p>OP CRM intègre l’intelligence métier propre aux OPs au cœur du système qui a fait d’OP Simplify la plateforme collaborative N°1 pour les organisations professionnelles.</p>
                                                <p>OP CRM a été développé par les dirigeants d’organisations professionnelles pour les dirigeants d’organisations professionnelles, en utilisant la méthode Design Thinking utilisée par Google pour rendre simple toutes ses applications.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end content-->
                                </div>
                                <div class="col-md-4 col-sm-4 vertical-aligned-element">
                                    <form method="post" class="form clearfix has-background animate translate-z-in animation-time-2s delay-03s" action="{{url('send-launching-email')}}">
                                        <h6 id="msg4" class="text-center" style="display:none;color:#ffff00;"></h6>
                                        <h2 id="msg1" style="display:none">Être averti (e) du lancement</h2>
                                        <h6 id="msg2" class="text-center" style="display:none;color:#ffff00;">Votre demande a été enregistrée.</h6>
                                        <h2 id="msg3" class="text-center" style="display:none;color:#ffff00;font-size: 20px;">Informez un (e) ami (e) ou collègue</h2>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="name" placeholder="Prénom Nom" required>
                                        </div>
                                        <!--end form-group -->
                                        <div class="form-group">
                                            <input type="email" class="form-control" name="email" placeholder="Email" required>
                                        </div>
                                        <!--end form-group -->
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="phone" placeholder="Organisation professionnelle" required>
                                        </div>
                                        <!--end form-group -->
                                        <div class="form-group clearfix">
                                            <button type="submit" class="btn pull-right btn-default btn-framed btn-rounded" id="form-contact-submit">PRÉVENEZ-MOI</button>
                                        </div>
                                        <!--end form-group -->
                                        <div class="form-contact-status"></div>
                                    </form>
                                    <!--end form-contact -->
                                </div>
                            </div>
                        </div>
                        <!--end content-wrapper-->
                    </div>
                    <!--end row-content-->
                    <div id="row-footer">
                        <footer>
                        </footer>
                    </div>
                    <!--end row-footer-->
                </div>
                <!--end container-->
            </div>
            <!--end table-wrapper-->
            <div class="background-wrapper has-vignette">
                <div class="bg-transfer opacity-40"><img src="{{ URL::asset('public/launch/assets/img/coming_soon_background.png') }}" alt=""></div>
            </div>
            <!--end background-wrapper-->
        </div>
        <!--end inner-wrapper-->
    </div>
    <!--end outer-wrapper-->
    
    <div class="side-panel" id="works">
        <div class="close-panel"><i class="fa fa-chevron-left"></i></div>
        <div class="wrapper">
            <div class="tse-scrollable">
                <div class="tse-content">
                    <div class="wrapper">
                        <div class="container">
                            <h2>Our Works</h2>
                            <section>
                                <h3>Featured Work</h3>
                                <div class="carousel">
                                    <div class="owl-carousel" data-owl-items="1" data-owl-margin="0" data-owl-nav="0" data-owl-dots="1">
                                        <div class="image"><div class="bg-transfer"><img src="{{ URL::asset('public/launch/assets/img/work-01.jpg') }}" alt=""></div></div>
                                        <div class="image"><div class="bg-transfer"><img src="{{ URL::asset('public/launch/assets/img/work-08.jpg') }}" alt=""></div></div>
                                        <div class="image"><div class="bg-transfer"><img src="{{ URL::asset('public/launch/assets/img/work-09.jpg') }}" alt=""></div></div>
                                    </div>
                                </div>
                                <!--end gallery-->
                            </section>
                            <section>
                                <h3>All Works</h3>
                                <div class="gallery">
                                    <div class="row">
                                        <div class="col-md-4 col-sm-4">
                                            <a href="{{ URL::asset('public/launch/assets/img/work-02.jpg') }}" class="gallery-item image-popup">
                                                <div class="description">
                                                    <figure>
                                                        <h4>Logo Redesign</h4>
                                                        <div class="meta">
                                                            <figure><strong>Client:</strong>Pehaz</figure>
                                                            <figure><strong>Date:</strong>24.05.2016</figure>
                                                        </div>
                                                    </figure>
                                                </div>
                                                <div class="image"><div class="bg-transfer"><img src="{{ URL::asset('public/launch/assets/img/work-02.jpg') }}" alt=""></div></div>
                                            </a>
                                            <!--end gallery-item-->
                                        </div>
                                        <!--end col-md-4-->
                                        <div class="col-md-4 col-sm-4">
                                            <a href="{{ URL::asset('public/launch/assets/img/work-03.jpg') }}" class="gallery-item image-popup">
                                                <div class="description">
                                                    <figure>
                                                        <h4>Logo Redesign</h4>
                                                        <div class="meta">
                                                            <figure><strong>Client:</strong>Pehaz</figure>
                                                            <figure><strong>Date:</strong>24.05.2016</figure>
                                                        </div>
                                                    </figure>
                                                </div>
                                                <div class="image"><div class="bg-transfer"><img src="{{ URL::asset('public/launch/assets/img/work-03.jpg') }}" alt=""></div></div>
                                            </a>
                                            <!--end gallery-item-->
                                        </div>
                                        <!--end col-md-4-->
                                        <div class="col-md-4 col-sm-4">
                                            <a href="{{ URL::asset('public/launch/assets/img/work-04.jpg') }}" class="gallery-item image-popup">
                                                <div class="description">
                                                    <figure>
                                                        <h4>Logo Redesign</h4>
                                                        <div class="meta">
                                                            <figure><strong>Client:</strong>Pehaz</figure>
                                                            <figure><strong>Date:</strong>24.05.2016</figure>
                                                        </div>
                                                    </figure>
                                                </div>
                                                <div class="image"><div class="bg-transfer"><img src="{{ URL::asset('public/launch/assets/img/work-04.jpg') }}" alt=""></div></div>
                                            </a>
                                            <!--end gallery-item-->
                                        </div>
                                        <!--end col-md-4-->
    
                                        <div class="col-md-4 col-sm-4">
                                            <a href="{{ URL::asset('public/launch/assets/img/work-05.jpg') }}" class="gallery-item image-popup">
                                                <div class="description">
                                                    <figure>
                                                        <h4>Logo Redesign</h4>
                                                        <div class="meta">
                                                            <figure><strong>Client:</strong>Pehaz</figure>
                                                            <figure><strong>Date:</strong>24.05.2016</figure>
                                                        </div>
                                                    </figure>
                                                </div>
                                                <div class="image"><div class="bg-transfer"><img src="{{ URL::asset('public/launch/assets/img/work-05.jpg') }}" alt=""></div></div>
                                            </a>
                                            <!--end gallery-item-->
                                        </div>
                                        <!--end col-md-4-->
                                        <div class="col-md-4 col-sm-4">
                                            <a href="{{ URL::asset('public/launch/assets/img/work-06.jpg') }}" class="gallery-item image-popup">
                                                <div class="description">
                                                    <figure>
                                                        <h4>Logo Redesign</h4>
                                                        <div class="meta">
                                                            <figure><strong>Client:</strong>Pehaz</figure>
                                                            <figure><strong>Date:</strong>24.05.2016</figure>
                                                        </div>
                                                    </figure>
                                                </div>
                                                <div class="image"><div class="bg-transfer"><img src="{{ URL::asset('public/launch/assets/img/work-06.jpg') }}" alt=""></div></div>
                                            </a>
                                            <!--end gallery-item-->
                                        </div>
                                        <!--end col-md-4-->
                                        <div class="col-md-4 col-sm-4">
                                            <a href="{{ URL::asset('public/launch/assets/img/work-07.jpg') }}" class="gallery-item image-popup">
                                                <div class="description">
                                                    <figure>
                                                        <h4>Logo Redesign</h4>
                                                        <div class="meta">
                                                            <figure><strong>Client:</strong>Pehaz</figure>
                                                            <figure><strong>Date:</strong>24.05.2016</figure>
                                                        </div>
                                                    </figure>
                                                </div>
                                                <div class="image"><div class="bg-transfer"><img src="{{ URL::asset('public/launch/assets/img/work-07.jpg') }}" alt=""></div></div>
                                            </a>
                                            <!--end gallery-item-->
                                        </div>
                                        <!--end col-md-4-->
                                    </div>
                                    <!--end row-->
                                </div>
                                <!--end gallery-->
                            </section>
                        </div>
                        <!--end container-->
                    </div>
                    <!--end wrapper-->
                </div>
                <!--end tse-content-->
            </div>
            <!--end tse-scrollable-->
        </div>
        <!--end wrapper-->
    </div>
    <!--end works-->
    
    <div class="side-panel" id="services">
        <div class="close-panel"><i class="fa fa-chevron-left"></i></div>
        <div class="wrapper">
            <div class="tse-scrollable">
                <div class="tse-content">
                    <div class="wrapper">
                        <h2>Service</h2>
                        <section>
                            <h3>Why Choose Us</h3>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus laoreet sed tellus ut condimentum.
                                Aenean hendrerit, nisl sit amet molestie eleifend, magna augue pulvinar enim, nec mattis quam eros non
                                ex. Pellentesque luctus ex enim, a tempus lorem egestas quis. Nunc et tincidunt dui. Cras in fermentum leo.
                            </p>
                        </section>
                        <section>
                            <h3>What We Can do For You</h3>
                            <div class="feature">
                                <div class="circle">
                                    <i class="icon_pens"></i>
                                </div>
                                <div class="description">
                                    <h4>Graphic Design</h4>
                                    <p>Aenean hendrerit, nisl sit amet molestie eleifend, magna augue pulvinar enim, nec mattis quam eros non ex</p>
                                </div>
                            </div>
                            <!--end feature-->
                            <div class="feature">
                                <div class="circle">
                                    <i class="icon_camera_alt"></i>
                                </div>
                                <div class="description">
                                    <h4>Professional Photography</h4>
                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus laoreet sed tellus ut condimentum. Aen</p>
                                </div>
                            </div>
                            <!--end feature-->
                            <div class="feature">
                                <div class="circle">
                                    <i class="icon_genius"></i>
                                </div>
                                <div class="description">
                                    <h4>Marketing Ideas</h4>
                                    <p>Phasellus laoreet sed tellus ut condimentum. Aenean hendrerit, nisl sit amet molestie eleifend, magna augue pulvinar enim, nec mattis quam</p>
                                </div>
                            </div>
                            <!--end feature-->
                            <div class="feature">
                                <div class="circle">
                                    <i class="icon_volume-high_alt"></i>
                                </div>
                                <div class="description">
                                    <h4>Sound Recording</h4>
                                    <p>Quisque et mollis enim. Aenean placerat tincidunt magna ut iaculis. Quisque eu lorem venenatis, egestas ipsum quis, tincidunt ipsum.</p>
                                </div>
                            </div>
                            <!--end feature-->
                        </section>
                    </div>
                    <!--end wrapper-->
                </div>
                <!--end tse-content-->
            </div>
            <!--end tse-scrollable-->
        </div>
        <!--end wrapper-->
    </div>
    <!--end services-->
    
    <div class="side-panel" id="about-us">
        <div class="close-panel"><i class="fa fa-chevron-left"></i></div>
        <div class="wrapper">
            <div class="tse-scrollable">
                <div class="tse-content">
                    <div class="wrapper">
                        <div class="container">
                            <h2>About Us</h2>
                            <section>
                                <h3>The Team</h3>
                                <div class="row">
                                    <div class="col-md-4 col-sm-4">
                                        <div class="person has-divider">
                                            <div class="image"><div class="bg-transfer"><img src="{{ URL::asset('public/launch/assets/img/person-01.jpg') }}" alt=""></div></div>
                                            <h4>Jane Doe</h4>
                                            <figure>Company CEO</figure>
                                            <div class="social-icons">
                                                <a href="#"><i class="fa fa-twitter"></i></a>
                                                <a href="#"><i class="fa fa-facebook"></i></a>
                                                <a href="#"><i class="fa fa-youtube"></i></a>
                                            </div>
                                        </div>
                                        <!--end person-->
                                    </div>
                                    <!--end col-md-4-->
                                    <div class="col-md-4 col-sm-4">
                                        <div class="person has-divider">
                                            <div class="image"><div class="bg-transfer"><img src="{{ URL::asset('public/launch/assets/img/person-02.jpg') }}" alt=""></div></div>
                                            <h4>Peter Brown</h4>
                                            <figure>Marketing Specialist</figure>
                                            <div class="social-icons">
                                                <a href="#"><i class="fa fa-twitter"></i></a>
                                                <a href="#"><i class="fa fa-facebook"></i></a>
                                                <a href="#"><i class="fa fa-youtube"></i></a>
                                            </div>
                                        </div>
                                        <!--end person-->
                                    </div>
                                    <!--end col-md-4-->
                                    <div class="col-md-4 col-sm-4">
                                        <div class="person">
                                            <div class="image"><div class="bg-transfer"><img src="{{ URL::asset('public/launch/assets/img/person-03.jpg') }}" alt=""></div></div>
                                            <h4>John Lane</h4>
                                            <figure>PR Manager</figure>
                                            <div class="social-icons">
                                                <a href="#"><i class="fa fa-twitter"></i></a>
                                                <a href="#"><i class="fa fa-facebook"></i></a>
                                                <a href="#"><i class="fa fa-youtube"></i></a>
                                            </div>
                                        </div>
                                        <!--end person-->
                                    </div>
                                    <!--end col-md-4-->
                                </div>
                                <!--end persons-->
                            </section>
                            <section>
                                <h3>Shortly About Our Company</h3>
                                <p>
                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus laoreet sed tellus ut condimentum.
                                    Aenean hendrerit, nisl sit amet molestie eleifend, magna augue pulvinar enim, nec mattis quam
                                    eros non ex. Pellentesque luctus ex enim, a tempus lorem egestas quis. Nunc et tincidunt dui.
                                    Cras in fermentum leo.
                                </p>
                                <p>
                                    Mauris molestie pharetra tristique. Donec interdum odio erat, sed ullamcorper lectus egestas non.
                                    Quisque sollicitudin vestibulum leo eget malesuada. Pellentesque sem erat, tempor a odio sed,
                                    tincidunt mollis purus. Cras suscipit ultrices cursus.
                                </p>
                            </section>
                            <section>
                                <h3>Our Skills</h3>
                                <div class="skill">
                                    <h4>Webdesign</h4>
                                    <aside>80%</aside>
                                    <figure class="bar">
                                        <div class="bar-active width-80"></div>
                                        <div class="bar-background"></div>
                                    </figure>
                                </div>
                                <!--end skill-->
                                <div class="skill">
                                    <h4>Photography</h4>
                                    <aside>100%</aside>
                                    <figure class="bar">
                                        <div class="bar-active width-100"></div>
                                        <div class="bar-background"></div>
                                    </figure>
                                </div>
                                <!--end skill-->
                                <div class="skill">
                                    <h4>Marketing</h4>
                                    <aside>60%</aside>
                                    <figure class="bar">
                                        <div class="bar-active width-60"></div>
                                        <div class="bar-background"></div>
                                    </figure>
                                </div>
                                <!--end skill-->
                            </section>
                        </div>
                        <!--end container-->
                    </div>
                    <!--end wrapper-->
                </div>
                <!--end tse-content-->
            </div>
            <!--end tse-scrollable-->
        </div>
        <!--end wrapper-->
    </div>
    <!--end about-us-->
    
    <div class="side-panel" id="contact">
        <div class="close-panel"><i class="fa fa-chevron-left"></i></div>
        <div class="wrapper">
            <div class="tse-scrollable">
                <div class="tse-content">
                    <div class="wrapper">
                        <div class="container">
                            <h2>Contact Us</h2>
                            <section>
                                <div class="row">
                                    <div class="col-md-6 col-sm-6">
                                        <h3>Address</h3>
                                        <address>
                                            4758 Nancy Street
                                            <br>
                                            +1 919-571-2528
                                            <br>
                                            <a href="#">hello@example.com</a>
                                        </address>
                                    </div>
                                    <!--end col-sm-6-->
                                    <div class="col-md-6 col-sm-6">
                                        <h3>Social</h3>
                                        <figure><a href="" class="icon"><i class="fa fa-facebook"></i>Facebook</a></figure>
                                        <figure><a href="" class="icon"><i class="fa fa-twitter"></i>Twitter</a></figure>
                                        <figure><a href="" class="icon"><i class="fa fa-youtube"></i>Youtube</a></figure>
                                        <figure><a href="" class="icon"><i class="fa fa-pinterest"></i>Pinterest</a></figure>
                                    </div>
                                    <!--end col-sm-6-->
                                </div>
                            </section>
                            <section>
                                <h3>Map</h3>
                                <div class="map" id="map-contact"></div>
                            </section>
                            <section>
                                <h3>Contact Form</h3>
                                <form id="form-contact" method="post" class="form clearfix inputs-underline">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6">
                                            <div class="form-group">
                                                <input type="text" class="form-control" id="form-contact-name" name="name" placeholder="Your Name" required>
                                            </div>
                                            <!--end form-group -->
                                        </div>
                                        <!--end col-md-6 col-sm-6 -->
                                        <div class="col-md-6 col-sm-6">
                                            <div class="form-group">
                                                <input type="email" class="form-control" id="form-contact-email" name="email" placeholder="Your Email" required>
                                            </div>
                                            <!--end form-group -->
                                        </div>
                                        <!--end col-md-6 col-sm-6 -->
                                    </div>
                                    <!--end row -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <textarea class="form-control" id="form-contact-message" rows="8" name="message" placeholder="Your Message" required></textarea>
                                            </div>
                                            <!--end form-group -->
                                        </div>
                                        <!--end col-md-12 -->
                                    </div>
                                    <!--end row -->
                                    <div class="form-group clearfix">
                                        <button type="submit" class="btn pull-right btn-default btn-framed btn-rounded" id="form-contact-submit">Send a Message</button>
                                    </div>
                                    <!--end form-group -->
                                    <div class="form-contact-status"></div>
                                </form>
                                <!--end form-contact -->
                            </section>
                        </div>
                        <!--end container-->
                    </div>
                    <!--end wrapper-->
                </div>
                <!--end tse-content-->
            </div>
            <!--end tse-scrollable-->
        </div>
        <!--end wrapper-->
    </div>
    <!--end contact-->
    
    <div class="backdrop"></div>
@endsection