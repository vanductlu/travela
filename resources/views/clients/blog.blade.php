@include('clients.blocks.header')
@include('clients.blocks.banner')
@include('clients.partials.chat')
<section class="blog-list-page py-100 rel z-1">
    <div class="container">
        <div class="row">
            <!-- Danh sách bài viết -->
            <div class="col-lg-8">
                @foreach($blogs as $blog)
                <div class="blog-item style-three" data-aos="fade-up" data-aos-duration="1500" data-aos-offset="50">
                    <div class="image">
                        <img src="{{ asset('clients/assets/images/blog/' . $blog->image) }}" alt="{{ $blog->title }}">
                    </div>
                    <div class="content">
                        <a href="#" class="category">{{ $blog->category }}</a>
                        <h5><a href="{{ route('blog-details', $blog->slug) }}">{{ $blog->title }}</a></h5>
                        <ul class="blog-meta">
                            <li><i class="far fa-user"></i> {{ $blog->author }}</li>
                            <li><i class="far fa-calendar-alt"></i> {{ $blog->created_at->format('d/m/Y') }}</li>
                            <li><i class="far fa-eye"></i> {{ $blog->views }} lượt xem</li>
                        </ul>
                        <p>{{ Str::limit(strip_tags($blog->excerpt), 150, '...') }}</p>
                        <a href="{{ route('blog-details', $blog->slug) }}" class="theme-btn style-two style-three">
                            <span data-hover="Đọc thêm">Đọc thêm</span>
                            <i class="fal fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                @endforeach

                <div class="mt-4">
                    {{ $blogs->links('pagination::bootstrap-4') }}
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4 col-md-8 col-sm-10 rmt-75">
                <div class="blog-sidebar">

                    <div class="widget widget-search" data-aos="fade-up">
                        <form action="#" class="default-search-form">
                            <input type="text" placeholder="Tìm kiếm..." required="">
                            <button type="submit" class="searchbutton far fa-search"></button>
                        </form>
                    </div>

                    <div class="widget widget-category" data-aos="fade-up">
                        <h5 class="widget-title">Danh mục</h5>
                        <ul class="list-style-three">
                            @foreach($blogs->unique('category') as $cat)
                                <li><a href="#">{{ $cat->category }}</a></li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="widget widget-news" data-aos="fade-up">
                        <h5 class="widget-title">Bài viết mới</h5>
                        <ul>
                            @foreach($recent as $item)
                            <li>
                                <div class="image">
                                    <img src="{{ asset('clients/assets/images/blog/' . $item->image) }}" alt="{{ $item->title }}">
                                </div>
                                <div class="content">
                                    <h6><a href="{{ route('blog-details', $item->slug) }}">{{ Str::limit($item->title, 50) }}</a></h6>
                                    <span class="date"><i class="far fa-calendar-alt"></i> {{ $item->created_at->format('d/m/Y') }}</span>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@include('clients.blocks.new_letter')
@include('clients.blocks.footer')
