
<!-- Footer -->
<footer id="footer" class="section">

    <!-- container -->
    <div class="container">

        <!-- row -->
        <div class="row">

            <!-- footer logo -->
            <div class="col-md-6">
                <div class="footer-logo">
                    <a class="logo" href="#">
                        <img src="{{ option('logo') }}" height="70" width="70" alt="logo">
                    </a>
                </div>
            </div>
            <!-- footer logo -->

            <!-- footer nav -->
            <div class="col-md-6">
                <ul class="footer-nav">
                    @include('frontend.layout.links')
                </ul>
            </div>
            <!-- /footer nav -->

        </div>
        <!-- /row -->

        <!-- row -->
        <div id="bottom-footer" class="row">

            <!-- social -->
            <div class="col-md-4 col-md-push-8">
                <ul class="footer-social row">
                    <li><a href="{{ option('facebook_url') }}" class="facebook"><i class="fa fa-facebook"></i></a></li>
                    <li><a href="{{ option('twitter_url') }}" class="twitter"><i class="fa fa-twitter"></i></a></li>
                    <li><a href="{{ option('google_url') }}" class="google-plus"><i class="fa fa-google-plus"></i></a></li>
                    <li><a href="{{ option('instagram_url') }}" class="instagram"><i class="fa fa-instagram"></i></a></li>
                    <li><a href="{{ option('youtube_url') }}" class="linkedin"><i class="fa fa-linkedin"></i></a></li>
                    <li><a href="{{ option('linkedin_url') }}" class="youtube"><i class="fa fa-youtube"></i></a></li>
                    <li><a href="{{ option('pinterest_url') }}" class="pinterest"><i class="fa fa-pinterest"></i></a></li>
                </ul>
            </div>
            <!-- /social -->

            <!-- copyright -->
            <div class="col-md-8 col-md-pull-4">
                <div>
                    <p>
                        {{ option('footer_about_text') }}
                    </p>
                </div>
                <div class="footer-copyright">
                    <span>&copy; {{ option('copyright_text') }} </span>
                </div>
            </div>
            <!-- /copyright -->

        </div>
        <!-- row -->

    </div>
    <!-- /container -->

</footer>
<!-- /Footer -->
