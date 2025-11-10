@include('clients.blocks.header')
@include('clients.blocks.banner')
@include('clients.partials.chat')
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
                            <hr>
<div class="comment-section mt-5">
    <h5 class="mb-4">💬 Bình luận</h5>

    {{-- Thông báo thành công hoặc lỗi --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @php
        $user = session('user'); // Lấy thông tin người dùng từ session
    @endphp

    {{-- Form bình luận --}}
    @if($user)
        <form action="{{ route('blog.comment', $blog->blogId) }}" method="POST" class="mb-4">
            @csrf
            <div class="mb-3">
                <textarea name="content" class="form-control" rows="3" placeholder="Nhập bình luận..." required></textarea>
            </div>
            <button type="submit" class="btn btn-success">Gửi bình luận</button>
        </form>
    @else
        <p class="text-muted">
            Bạn cần <a href="{{ route('login', ['redirect' => request()->fullUrl()]) }}">đăng nhập</a> để bình luận.
        </p>
    @endif

    {{-- Danh sách bình luận --}}
    @php
        $comments = DB::table('tbl_comments')
            ->where('blog_id', $blog->blogId)
            ->orderByDesc('created_at')
            ->get();
    @endphp

    <div class="comments-list">
        @foreach($comments as $comment)
            <div class="comment d-flex align-items-start border rounded p-3 mb-3">
                {{-- Avatar ngẫu nhiên hoặc từ user --}}
                <div class="avatar me-3">
                    <img src="https://i.pravatar.cc/50?u={{ $comment->user_id ?? $comment->name }}" 
                         alt="{{ $comment->name }}" class="rounded-circle" width="50" height="50">
                </div>
                <div class="comment-content">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <strong>{{ $comment->name }}</strong>
                        <small class="text-muted">{{ \Carbon\Carbon::parse($comment->created_at)->format('d/m/Y H:i') }}</small>
                    </div>
                    <p class="mb-0">{{ $comment->content }}</p>
                </div>
            </div>
        @endforeach

        @if($comments->isEmpty())
            <p class="text-muted">Chưa có bình luận nào. Hãy là người đầu tiên bình luận!</p>
        @endif
    </div>
</div>


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
