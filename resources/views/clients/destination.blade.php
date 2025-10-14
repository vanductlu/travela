@include('clients.blocks.header')
@include('clients.blocks.banner')
@include('clients.blocks.search')
<!-- Popular Destinations Area start -->
<section class="popular-destinations-area pt-100 pb-90 rel z-1">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="section-title text-center counter-text-wrap mb-40" data-aos="fade-up" data-aos-duration="1500"
                    data-aos-offset="50">
                    <h2>Khám phá các điểm đến phổ biến</h2>
                    <p>Website <span class="count-text plus" data-speed="3000" data-stop="34500">0</span> trải nghiệm phổ
                        biến nhất mà bạn sẽ nhớ</p>
                    <ul class="destinations-nav mt-30">
                        <li data-filter="*" class="active">Tất cả</li>
                        <li data-filter=".domain-b">Miền Bắc</li>
                        <li data-filter=".domain-t">Miền Trung</li>
                        <li data-filter=".domain-n">Miền Nam</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="section-title text-center counter-text-wrap mb-40" data-aos="fade-up" data-aos-duration="1500" data-aos-offset="50">
                            <h2>Khám phá điểm đến nổi bật</h2>
                            <p>Một địa điểm <span class="count-text plus" data-speed="3000" data-stop="34500">0</span> trải nghiệm nổi bật nhất</p>
                            <ul class="destinations-nav mt-30">
                                <li data-filter="*" class="active">Hiển thị tất cả</li>
                                <li data-filter=".features">Nổi bật</li>
                                <li data-filter=".recent">Mới nhất</li>
                                <li data-filter=".city">Thành phố</li>
                                <li data-filter=".rating">Đánh giá</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="row gap-10 destinations-active justify-content-center">
                        <div class="col-xl-3 col-md-6 item recent rating">
                            <div class="destination-item style-two" data-aos="flip-up" data-aos-duration="1500" data-aos-offset="50">
                                <div class="image">
                                    <a href="#" class="heart"><i class="fas fa-heart"></i></a>
                                    <img src="{{ asset('clients/assets/images/destinations/destination1.jpg') }}" alt="Điểm đến">
                                </div>
                                <div class="content">
                                    <h6><a href="destination-details.html">Bãi biển Thái Lan</a></h6>
                                    <span class="time">5352+ tour & 856+ hoạt động</span>
                                    <a href="#" class="more"><i class="fas fa-chevron-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 item features">
                            <div class="destination-item style-two" data-aos="flip-up" data-aos-delay="100" data-aos-duration="1500" data-aos-offset="50">
                                <div class="image">
                                    <a href="#" class="heart"><i class="fas fa-heart"></i></a>
                                    <img src="{{ asset('clients/assets/images/destinations/destination2.jpg') }}" alt="Điểm đến">
                                </div>
                                <div class="content">
                                    <h6><a href="destination-details.html">Parga, Hy Lạp</a></h6>
                                    <span class="time">5352+ tour & 856+ hoạt động</span>
                                    <a href="#" class="more"><i class="fas fa-chevron-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 item recent city rating">
                            <div class="destination-item style-two" data-aos="flip-up" data-aos-delay="200" data-aos-duration="1500" data-aos-offset="50">
                                <div class="image">
                                    <a href="#" class="heart"><i class="fas fa-heart"></i></a>
                                    <img src="{{ asset('clients/assets/images/destinations/destination3.jpg') }}" alt="Điểm đến">
                                </div>
                                <div class="content">
                                    <h6><a href="destination-details.html">Castellammare del Golfo, Ý</a></h6>
                                    <span class="time">5352+ tour & 856+ hoạt động</span>
                                    <a href="#" class="more"><i class="fas fa-chevron-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 item city features">
                            <div class="destination-item style-two" data-aos="flip-up" data-aos-duration="1500" data-aos-offset="50">
                                <div class="image">
                                    <a href="#" class="heart"><i class="fas fa-heart"></i></a>
                                    <img src="{{ asset('clients/assets/images/destinations/destination4.jpg') }}" alt="Điểm đến">
                                </div>
                                <div class="content">
                                    <h6><a href="destination-details.html">Khu bảo tồn Canada, Canada</a></h6>
                                    <span class="time">5352+ tour & 856+ hoạt động</span>
                                    <a href="#" class="more"><i class="fas fa-chevron-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 item recent">
                            <div class="destination-item style-two" data-aos="flip-up" data-aos-delay="100" data-aos-duration="1500" data-aos-offset="50">
                                <div class="image">
                                    <a href="#" class="heart"><i class="fas fa-heart"></i></a>
                                    <img src="{{ asset('clients/assets/images/destinations/destination5.jpg') }}" alt="Điểm đến">
                                </div>
                                <div class="content">
                                    <h6><a href="destination-details.html">Dubai, Hoa Kỳ</a></h6>
                                    <span class="time">5352+ tour & 856+ hoạt động</span>
                                    <a href="#" class="more"><i class="fas fa-chevron-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 item features rating">
                            <div class="destination-item style-two" data-aos="flip-up" data-aos-delay="200" data-aos-duration="1500" data-aos-offset="50">
                                <div class="image">
                                    <a href="#" class="heart"><i class="fas fa-heart"></i></a>
                                    <img src="{{ asset('clients/assets/images/destinations/destination6.jpg') }}" alt="Điểm đến">
                                </div>
                                <div class="content">
                                    <h6><a href="destination-details.html">Milos, Hy Lạp</a></h6>
                                    <span class="time">5352+ tour & 856+ hoạt động</span>
                                    <a href="#" class="more"><i class="fas fa-chevron-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    </div>
</section>
<!-- Popular Destinations Area end -->

<!-- Hotel Area start -->
        <section class="hotel-area bgc-black pt-100 pb-70 rel z-1">
            <div class="container">
                <div class="row">
                    <div class="col-xl-4">
                        <div class="destination-left-content mb-35">
                            <div class="section-title text-white counter-text-wrap mb-45" data-aos="fade-up" data-aos-duration="1500" data-aos-offset="50">
                                <h2>Khám phá khách sạn hàng đầu thế giới</h2>
                                <p>Một địa điểm <span class="count-text plus" data-speed="3000" data-stop="34500">0</span> trải nghiệm nổi bật bạn sẽ nhớ mãi</p>
                            </div>
                            <a href="destination1.html" class="theme-btn style-four mb-15">
                                <span data-hover="Xem thêm khách sạn">Xem thêm khách sạn</span>
                                <i class="fal fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-xl-8">
                        <div class="destination-item style-three" data-aos="fade-up" data-aos-duration="1500" data-aos-offset="50">
                            <div class="image">
                                <div class="ratting"><i class="fas fa-star"></i> 4.8</div>
                                <a href="#" class="heart"><i class="fas fa-heart"></i></a>
                                <img src="{{asset('clients/assets/images/destinations/hotel1.jpg" a') }}lt="Khách sạn">
                            </div>
                            <div class="content">
                                <span class="location"><i class="fal fa-map-marker-alt"></i> Ao Nang, Thái Lan</span>
                                <h5><a href="tour-details.html">Khách sạn ghế nâu cạnh hồ bơi</a></h5>
                                <ul class="list-style-one">
                                    <li><i class="fal fa-bed-alt"></i> 2 phòng ngủ</li>
                                    <li><i class="fal fa-hat-chef"></i> 1 nhà bếp</li>
                                    <li><i class="fal fa-bath"></i> 2 phòng tắm</li>
                                    <li><i class="fal fa-router"></i> Internet</li>
                                </ul>
                                <div class="destination-footer">
                                    <span class="price"><span>$85.00</span>/đêm</span>
                                    <a href="tour-details.html" class="read-more">Đặt ngay <i class="fal fa-angle-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="destination-item style-three" data-aos="fade-up" data-aos-delay="50" data-aos-duration="1500" data-aos-offset="50">
                            <div class="image">
                                <div class="ratting"><i class="fas fa-star"></i> 4.8</div>
                                <a href="#" class="heart"><i class="fas fa-heart"></i></a>
                                <img src="{{asset('clients/assets/images/destinations/hotel2.jpg" a') }}lt="Khách sạn">
                            </div>
                            <div class="content">
                                <span class="location"><i class="fal fa-map-marker-alt"></i> Kigali, Rwanda</span>
                                <h5><a href="tour-details.html">Khách sạn Marriott cạnh hồ nước và cây xanh</a></h5>
                                <ul class="list-style-one">
                                    <li><i class="fal fa-bed-alt"></i> 2 phòng ngủ</li>
                                    <li><i class="fal fa-hat-chef"></i> 1 nhà bếp</li>
                                    <li><i class="fal fa-bath"></i> 2 phòng tắm</li>
                                    <li><i class="fal fa-router"></i> Internet</li>
                                </ul>
                                <div class="destination-footer">
                                    <span class="price"><span>$85.00</span>/đêm</span>
                                    <a href="tour-details.html" class="read-more">Đặt ngay <i class="fal fa-angle-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="destination-item style-three" data-aos="fade-up" data-aos-duration="1500" data-aos-offset="50">
                            <div class="image">
                                <div class="ratting"><i class="fas fa-star"></i> 4.8</div>
                                <a href="#" class="heart"><i class="fas fa-heart"></i></a>
                                <img src="{{ asset('clients/assets/images/destinations/hotel3.jpg" a') }}lt="Khách sạn">
                            </div>
                            <div class="content">
                                <span class="location"><i class="fal fa-map-marker-alt"></i> Ao Nang, Thái Lan</span>
                                <h5><a href="tour-details.html">Khách sạn nhà sơn cạnh cây xanh</a></h5>
                                <ul class="list-style-one">
                                    <li><i class="fal fa-bed-alt"></i> 2 phòng ngủ</li>
                                    <li><i class="fal fa-hat-chef"></i> 1 nhà bếp</li>
                                    <li><i class="fal fa-bath"></i> 2 phòng tắm</li>
                                    <li><i class="fal fa-router"></i> Internet</li>
                                </ul>
                                <div class="destination-footer">
                                    <span class="price"><span>$85.00</span>/đêm</span>
                                    <a href="tour-details.html" class="read-more">Đặt ngay <i class="fal fa-angle-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="destination-item style-three" data-aos="fade-up" data-aos-delay="50" data-aos-duration="1500" data-aos-offset="50">
                            <div class="image">
                                <div class="ratting"><i class="fas fa-star"></i> 4.8</div>
                                <a href="#" class="heart"><i class="fas fa-heart"></i></a>
                                <img src="{{ asset('clients/assets/images/destinations/hotel4.jpg" a') }}lt="Khách sạn">
                            </div>
                            <div class="content">
                                <span class="location"><i class="fal fa-map-marker-alt"></i> Ao Nang, Thái Lan</span>
                                <h5><a href="tour-details.html">Khách sạn hồ bơi rừng nhiệt đới Indonesia</a></h5>
                                <ul class="list-style-one">
                                    <li><i class="fal fa-bed-alt"></i> 2 phòng ngủ</li>
                                    <li><i class="fal fa-hat-chef"></i> 1 nhà bếp</li>
                                    <li><i class="fal fa-bath"></i> 2 phòng tắm</li>
                                    <li><i class="fal fa-router"></i> Internet</li>
                                </ul>
                                <div class="destination-footer">
                                    <span class="price"><span>$85.00</span>/đêm</span>
                                    <a href="tour-details.html" class="read-more">Đặt ngay <i class="fal fa-angle-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Hotel Area end -->
        
        
        <!-- Hot Deals Area start -->
        <section class="hot-deals-area pt-70 pb-50 rel z-1">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="section-title text-center counter-text-wrap mb-50" data-aos="fade-up" data-aos-duration="1500" data-aos-offset="50">
                            <h2>Khám phá ưu đãi hấp dẫn</h2>
                            <p>Một địa điểm <span class="count-text plus" data-speed="3000" data-stop="34500">0</span> trải nghiệm nổi bật bạn sẽ nhớ mãi</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div class="destination-item style-four no-border" data-aos="flip-left" data-aos-duration="1500" data-aos-offset="50">
                            <div class="image">
                                <span class="badge">Giảm 10%</span>
                                <a href="#" class="heart"><i class="fas fa-heart"></i></a>
                                <img src="{{ asset('clients/assets/images/destinations/hot-deal1.jpg') }}" alt="Hot Deal">
                            </div>
                            <div class="content">
                                <span class="location"><i class="fal fa-map-marker-alt"></i> Thành phố Venice, Ý</span>
                                <h5><a href="tour-details.html">Kênh đào Venice, mùa hè Metropolitan tại Venice</a></h5>
                            </div>
                            <div class="destination-footer">
                                <span class="price"><span>$58.00</span>/người</span>
                                <div class="ratting">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </div>
                            </div>
                            <a href="destination-details.html" class="theme-btn style-three">
                                <span data-hover="Explore">Khám phá</span>
                                <i class="fal fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="destination-item style-four no-border" data-aos="flip-left" data-aos-duration="1500" data-aos-offset="50">
                            <div class="image">
                                <span class="badge">Giảm 10%</span>
                                <a href="#" class="heart"><i class="fas fa-heart"></i></a>
                                <img src="{{ asset('clients/assets/images/destinations/hot-deal2.jpg') }}" alt="Hot Deal">
                            </div>
                            <div class="content">
                                <span class="location"><i class="fal fa-map-marker-alt"></i> Kyoto, Nhật Bản</span>
                                <h5><a href="tour-details.html">Nhật Bản, Kyoto, du lịch và con người tại Kyoto, Nhật Bản bởi Sorasak</a></h5>
                            </div>
                            <div class="destination-footer">
                                <span class="price"><span>$58.00</span>/người</span>
                                <div class="ratting">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </div>
                            </div>
                            <a href="destination-details.html" class="theme-btn style-three">
                                <span data-hover="Explore">Khám phá</span>
                                <i class="fal fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="destination-item style-four no-border" data-aos="flip-left" data-aos-duration="1500" data-aos-offset="50">
                            <div class="image">
                                <span class="badge">Giảm 10%</span>
                                <a href="#" class="heart"><i class="fas fa-heart"></i></a>
                                <img src="{{ asset('clients/assets/images/destinations/hot-deal3.jpg') }}" alt="Hot Deal">
                            </div>
                            <div class="content">
                                <span class="location"><i class="fal fa-map-marker-alt"></i> Tamnougalt, Morocco</span>
                                <h5><a href="tour-details.html">Lạc đà trên sa mạc dưới bầu trời xanh Morocco, Sahara.</a></h5>
                            </div>
                            <div class="destination-footer">
                                <span class="price"><span>$58.00</span>/người</span>
                                <div class="ratting">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </div>
                            </div>
                            <a href="destination-details.html" class="theme-btn style-three">
                                <span data-hover="Explore">Khám phá</span>
                                <i class="fal fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Hot Deals Area end -->
@include('clients.blocks.new_letter')
@include('clients.blocks.footer')
