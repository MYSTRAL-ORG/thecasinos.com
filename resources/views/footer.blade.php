<div class="container-fluid   ">
    <div class="row justify-content-center   footer">
        <div class="col-sm-6 col-lg-2 column text-center ">
            <a href="https://www.thecasinos.com">
                <img loading="lazy" src="/img/logo.png" alt="The Casinos">
            </a>
        </div>
        <div class="col-sm-6 col-lg-3 column-description">
            <p>With TheCasinos.com, enjoy the premier online guide reference. Explore a world of top-tier gaming and
                enhance your onsite casino journey. Trust in our curated selections to elevate your gaming
                adventure.</p>
        </div>
        <div class="col-sm-6 col-lg-2 column">
            <a href="{{ route('about')}}">About us</a>
            <a href="{{ route('terms')}}">Terms and conditions</a>
            <a href="{{ route('policy')}}">Private policy</a>
        </div>
        <div class="col-sm-6 col-lg-2 column">
            <a href="#" class="btn-download btn-ios">
                <i class="fab fa-apple" style="margin-right: 10px;"></i> iOS
            </a>
            <a href="#" class="btn-download btn-android">
                <i class="fab fa-android" style="margin-right: 10px;"></i> Android
            </a>
        </div>
    </div>
</div>


<div class=" copyright-section  d-flex justify-content-between ">
    <div><p>&copy; 2024 TheCasinos.com - Online reference to onsite experience</p></div>
    <div><a href="mailto:admin@thecasinos.com" class="contact-btn">Contact</a></div>
</div>

<meta name="_appUrl" content="{{ config('app.url')}}"/>


@vite('resources/js/lib-bootstrap.js')
@vite('resources/css/lib/lib-bootstrap.scss')


@vite('resources/css/casinos.css')



