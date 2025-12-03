<footer class="main-footer footer-two bgp-bottom bgc-black rel z-15 pt-100 pb-115" style="background-image: url({{ asset('clients/assets/images/backgrounds/footer-two.png')}});">
    <div class="widget-area">
        <div class="container">
            <div class="row row-cols-xxl-5 row-cols-xl-4 row-cols-md-3 row-cols-2">
                <div class="col col-small" data-aos="fade-up" data-aos-duration="1500" data-aos-offset="50">
                    <div class="footer-widget footer-text">
                        <div class="footer-logo mb-40">
                            <a href="{{ route('home') }}"><img src="{{ asset('clients/assets/images/logos/logo.png') }}" alt="Logo"></a>
                        </div>
                        <div class="footer-map">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d61349.64701146602!2d108.16542067386848!3d16.047164798501537!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x314219c792252a13%3A0xfc14e3a044436487!2sDa%20Nang%2C%20H%E1%BA%A3i%20Ch%C3%A2u%20District%2C%20Da%20Nang%2C%20Vietnam!5e0!3m2!1sen!2s!4v1729087157388!5m2!1sen!2s" 
                            style="border:0; width: 100%;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                </div>
                <div class="col col-small" data-aos="fade-up" data-aos-delay="50" data-aos-duration="1500" data-aos-offset="50">
                    <div class="footer-widget footer-links ms-sm-5">
                        <div class="footer-title">
                            <h5>Dịch vụ</h5>
                        </div>
                        <ul class="list-style-three">
                            <li><a href="{{ route('travel-guides') }}">Hướng dẫn viên du lịch tốt nhất</a></li>
                            <li><a href="{{ route('tours') }}">Đặt tour</a></li>
                            <li><a href="{{ route('tours') }}">Đặt vé</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col col-small" data-aos="fade-up" data-aos-delay="100" data-aos-duration="1500" data-aos-offset="50">
                    <div class="footer-widget footer-links ms-md-4">
                        <div class="footer-title">
                            <h5>Công ty</h5>
                        </div>
                        <ul class="list-style-three">
                            <li><a href="{{ route('about') }}">Giới thiệu về công ty</a></li>
                            <li><a href="{{ route('contact') }}">Việc làm và nghề nghiệp</a></li>
                            <li><a href="{{ route('contact') }}">Liên hệ với chúng tôi</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col col-small" data-aos="fade-up" data-aos-delay="150" data-aos-duration="1500" data-aos-offset="50">
                    <div class="footer-widget footer-links ms-lg-4">
                        <div class="footer-title">
                            <h5>Điểm đến</h5>
                        </div>
                        <ul class="list-style-three">
                            <li><a href="{{ route('destination') }}">Miền Bắc</a></li>
                            <li><a href="{{ route('destination') }}">Miền Trung</a></li>
                            <li><a href="{{ route('destination') }}">Miền Nam</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col col-md-6 col-10 col-small" data-aos="fade-up" data-aos-delay="200" data-aos-duration="1500" data-aos-offset="50">
                    <div class="footer-widget footer-contact">
                        <div class="footer-title">
                            <h5>Liên hệ</h5>
                        </div>
                        <ul class="list-style-one">
                            <li><i class="fal fa-map-marked-alt"></i> 173 Khâm Thiên, Quận Đống Đa, Hà Nội</li>
                            <li><i class="fal fa-envelope"></i> <a
                                    href="mailto:duc">nvd2k3@gmail.com</a></li>
                            <li><i class="fal fa-clock"></i> Thứ 2 - Thứ 6, 08am - 05pm</li>
                            <li><i class="fal fa-phone-volume"></i> <a href="callto:+364869849">+364869849</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom bg-transparent pt-20 pb-5">
        <div class="container">
            <div class="row">
               <div class="col-lg-5">
                    <div class="copyright-text text-center text-lg-start">
                        <p>@Copy 2024 <a href="\">Travela</a>, All rights reserved</p>
                    </div>
               </div>
               <div class="col-lg-7 text-center text-lg-end">
                   <ul class="footer-bottom-nav">
                    <li><a href="{{ route('about') }}">Điều khoản</a></li>
                    <li><a href="{{ route('about') }}">Chính sách bảo mật</a></li>
                    <li><a href="{{ route('about') }}">Thông báo pháp lý</a></li>
                    <li><a href="{{ route('about') }}">Khả năng truy cập</a></li>
                   </ul>
               </div>
            </div>
        </div>
    </div>
</footer>

</div>

    @if(session('error'))
        <script>
            alert("{{ session('error') }}");
        </script>
    @endif
    <script src="{{asset('clients/assets/js/jquery-3.6.0.min.js')}}"></script>
    <script src="{{asset('clients/assets/js/bootstrap.min.js')}}"></script>
    <script src="{{asset('clients/assets/js/appear.min.js')}}"></script>
    <script src="{{asset('clients/assets/js/slick.min.js')}}"></script>
    <script src="{{asset('clients/assets/js/jquery.magnific-popup.min.js')}}"></script>
    <script src="{{asset('clients/assets/js/jquery.nice-select.min.js')}}"></script>
    <script src="{{asset('clients/assets/js/imagesloaded.pkgd.min.js')}}"></script>
    <script src="{{asset('clients/assets/js/skill.bars.jquery.min.js')}}"></script>
    <script src="{{asset('clients/assets/js/jquery-ui.min.js')}}"></script>
    <script src="{{asset('clients/assets/js/isotope.pkgd.min.js')}}"></script>
    <script src="{{asset('clients/assets/js/aos.js')}}"></script>
    <script src="{{asset('clients/assets/js/script.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZQL/ZnLwN3U9057S34aIWg6w8fXwA9e7d9jO5p3c50A53a9f0Q0g0M0K0b0A9e0D0g0A9e0D0g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>  
    <script src="https://www.paypal.com/sdk/js?client-id={{ env('PAYPAL_SANDBOX_CLIENT_ID') }}"></script>
    <script src="{{asset('clients/assets/js/custom-js.js')}}"></script>
    <script src="{{ asset('clients/assets/js/search-autocomplete.js') }}"></script>
    <script src="{{asset('clients/assets/js/jquery.datetimepicker.full.min.js')}}"></script>

</body>

</html>