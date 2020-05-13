<nav class="navbar navbar-expand-lg navbar-dark indigo">
    <a class="navbar-brand" href="{{ route('home', app()->getLocale()) }}">Dzive kody</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText"
            aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarText">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="#"> {{ __('frontend.navbar.home') }} </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#"> {{ __('frontend.navbar.home') }} </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#"> {{ __('frontend.navbar.home') }} </a>
            </li>
        </ul>
        <span class="navbar-text white-text">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item {{ app()->getLocale() == 'sk' ? 'active' : '' }}">
                <a class="nav-link" href="{{ route(Route::currentRouteName(), 'sk') }}"> SK </a>
            </li>
            <li class="nav-item {{ app()->getLocale() == 'en' ? 'active' : '' }}">
                <a class="nav-link" href="{{ route(Route::currentRouteName(), 'en') }}"> EN </a>
            </li>
        </ul>
    </span>
    </div>
</nav>