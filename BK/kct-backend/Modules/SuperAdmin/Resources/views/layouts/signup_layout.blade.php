{{--This is for the first step of registration with Email --}}
@extends('superadmin::layouts.master')
@section('content')
    <div class=" bodyDiv container mt-3 mb-5">
        <div class="col-12 custom_row rounded-3">
            <div class="row">
                {{-- Left Side Section --}}
                <div class="col-6 rounded-3  p-4 pt-5 text-white">
                    <!-- <h1 class="text-center pb-4">Left Upper </h1>
                              <p style="text-align:justify">A wonderful serenity has taken possession of my entire soul, like
                                  these sweet mornings of spring
                                  which I enjoy with my whole heart. I am alone, and feel the charm of existence in this spot,
                                  which was created fke mine. I am so happy, my dear friend in the exquisite sense of mere
                                  tranquil existence, that I neglect my talents. I should be incapable of drawing a single present
                                  moment; and yet I feel that I never was a greater artist than now. When, while the lovely valley
                                  teems with vapour around me, and the meridian sun strikes the upper surface.
                              </p> -->
                </div>
                {{-- Right Side Section --}}
                @yield('signup-body')
            </div>
        </div>
    </div>
@endsection
