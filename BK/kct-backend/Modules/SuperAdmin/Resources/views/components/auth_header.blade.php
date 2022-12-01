<header id="header">
    <div class="container">
        <div class="row">
            <nav class="navbar navbar-expand-lg navbar-light">
                <div class="container-fluid">
                    {{-- user name and logout button bar --}}
                    <div class="me-auto app-icon ">
                        <img src="{{ $mainLogo??null }}" alt="{{ env("APP_NAME") }}" width="100" />
                    </div>
                    <div class="d-flex">
                        <div class="me-2">
                            {{ \Illuminate\Support\Facades\Auth::user()->fname}} {{\Illuminate\Support\Facades\Auth::user()->lname }}
                        </div>
                        <a href="{{route('su-logout')}}">
                            <i class="fa fa-power-off mr-3" aria-hidden="true"></i>
                            Logout
                        </a>
                    </div>
                </div>
            </nav>
        </div>
    </div>
</header>
