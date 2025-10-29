@include('clients.blocks.header')
@include('clients.blocks.banner')

<section class="blog-details-page py-100 rel z-1">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <article class="blog-details-content">
                    <div class="image mb-4">
                        <img src="{{ asset('clients/assets/images/blog/' . $blog->image) }}" alt="{{ $blog->title }}">
                    </div>
                    <h2>{{ $blog->title }}</h2>
                    <ul class="blog-meta mb-3">
                        <li><i class="far fa-user"></i> {{ $blog->author }}</li>
                        <li><i class="far fa-calendar-alt"></i> {{ $blog->created_at->format('d/m/Y') }}</li>
                        <li><i class="far fa-eye"></i> {{ $blog->views }} lượt xem</li>
                    </ul>
                    <p><strong>{{ $blog->excerpt }}</strong></p>
                    <div>{!! $blog->content !!}</div>
                </article>
            </div>

            <div class="col-lg-4 col-md-8 col-sm-10 rmt-75">
                <div class="blog-sidebar">
                    <div class="widget widget-news">
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
