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
                        <li><i class="far fa-calendar-alt"></i> 
                            {{ $blog->created_at->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') }} 
                            ({{ $blog->created_at->diffForHumans() }})
                        </li>
                        <li><i class="far fa-eye"></i> {{ $blog->views }} lượt xem</li>
                    </ul>
                    <p><strong>{{ $blog->excerpt }}</strong></p>
                    <div>{!! $blog->content !!}</div>

                    <hr>
                    <div class="comment-section mt-5">
                        <h5 class="mb-4">Bình luận</h5>

                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @elseif(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        @php
                            $user = session('username'); 
                        @endphp

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

                        @php
                            $comments = DB::table('tbl_comments')
                                ->where('blog_id', $blog->blogId)
                                ->whereNull('parent_id')
                                ->orderByDesc('created_at')
                                ->get();
                        @endphp

                        @php
                        if (!function_exists('renderReplies')) {
                            function renderReplies($commentId) {
                            $replies = DB::table('tbl_comments')
                                ->where('parent_id', $commentId)
                                ->orderBy('created_at', 'asc')
                                ->get();

                            foreach ($replies as $reply) {
                                $userData = DB::table('tbl_users')->where('username', $reply->name)->first();
                                $avatarPath = $userData && $userData->avatar
                                    ? asset('clients/assets/images/user-profile/' . $userData->avatar)
                                    : 'https://i.pravatar.cc/50?u=' . urlencode($reply->name);

                                echo '<div class="comment reply d-flex align-items-start border rounded p-3 ms-5 mt-2">';
                                echo '<div class="avatar me-3">';
                                echo '<img src="' . $avatarPath . '" class="rounded-circle" width="40" height="40">';
                                echo '</div>';
                                echo '<div class="comment-content">';
                                echo '<div class="d-flex justify-content-between align-items-center mb-1">';
                                echo '<strong>' . htmlspecialchars($reply->name) . '</strong>';
                                echo '<small class="text-muted ms-2">' 
                                     . \Carbon\Carbon::parse($reply->created_at)->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') 
                                     . ' (' . \Carbon\Carbon::parse($reply->created_at)->diffForHumans() . ')' 
                                     . '</small>';
                                echo '</div>';
                                echo '<p class="mb-1">' . htmlspecialchars($reply->content) . '</p>';
                                echo '<a href="#" class="reply-btn text-primary small" data-id="' . $reply->id . '">Trả lời</a>';
                                echo '</div>';
                                echo '</div>';

                                renderReplies($reply->id);
                            }
                        }
                        }
                        @endphp

                        <div class="comments-list">
                            @foreach($comments as $comment)
                                <div class="comment d-flex align-items-start border rounded p-3 mb-3">
                                    <div class="avatar me-3">
                                        @php
                                            $userData = DB::table('tbl_users')->where('username', $comment->name)->first();
                                            $avatarPath = $userData && $userData->avatar
                                                ? asset('clients/assets/images/user-profile/' . $userData->avatar)
                                                : 'https://i.pravatar.cc/50?u=' . urlencode($comment->name);
                                        @endphp
                                        <img src="{{ $avatarPath }}" alt="{{ $comment->name }}" class="rounded-circle" width="50" height="50">
                                    </div>
                                    <div class="comment-content w-100">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <strong>{{ $comment->name }}</strong>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($comment->created_at)->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') }}
                                                ({{ \Carbon\Carbon::parse($comment->created_at)->diffForHumans() }})
                                            </small>
                                        </div>
                                        <p class="mb-1">{{ $comment->content }}</p>
                                        <a href="#" class="reply-btn text-primary small" data-id="{{ $comment->id }}">Trả lời</a>
                                    </div>
                                </div>

                                {!! renderReplies($comment->id) !!}
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
                    <div class="widget widget-categories">
                        <h5 class="widget-title">📂 Danh mục</h5>
                        <ul class="category-list">
                            @php
                                $categories = DB::table('tbl_blog')
                                    ->select('category', DB::raw('count(*) as total'))
                                    ->whereNotNull('category')
                                    ->groupBy('category')
                                    ->get();
                            @endphp
                            @if($categories->isNotEmpty())
                                @foreach($categories as $cat)
                                <li>
                                    <a href="{{ route('blog') }}?category={{ urlencode($cat->category) }}">
                                        <span class="cat-name">{{ $cat->category }}</span>
                                        <span class="cat-count">{{ $cat->total }}</span>
                                    </a>
                                </li>
                                @endforeach
                            @else
                                <li style="text-align: center; color: #65676b;">Chưa có danh mục</li>
                            @endif
                        </ul>
                    </div>

                    <div class="widget widget-recent-posts">
                        <h5 class="widget-title">🔥 Bài viết mới</h5>
                        <ul class="recent-posts-list">
                            @foreach($recent as $item)
                            <li>
                                <a href="{{ route('blog-details', $item->slug) }}" class="post-item">
                                    <div class="post-image">
                                        <img src="{{ asset('clients/assets/images/blog/' . $item->image) }}" alt="{{ $item->title }}">
                                    </div>
                                    <div class="post-content">
                                        <h6 class="post-title">{{ Str::limit($item->title, 50) }}</h6>
                                        <span class="post-date">
                                            <i class="far fa-calendar-alt"></i>
                                            {{ $item->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="widget widget-popular-posts">
                        <h5 class="widget-title">⭐ Bài viết phổ biến</h5>
                        <ul class="popular-posts-list">
                            @php
                                $popularPosts = DB::table('tbl_blog')
                                    ->orderByDesc('views')
                                    ->limit(5)
                                    ->get();
                            @endphp
                            @foreach($popularPosts as $post)
                            <li>
                                <a href="{{ route('blog-details', $post->slug) }}" class="popular-item">
                                    <div class="popular-number">{{ $loop->iteration }}</div>
                                    <div class="popular-content">
                                        <h6 class="popular-title">{{ Str::limit($post->title, 60) }}</h6>
                                        <div class="popular-meta">
                                            <span class="views"><i class="far fa-eye"></i> {{ $post->views }}</span>
                                            <span class="date"><i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($post->created_at)->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="widget widget-tags">
                        <h5 class="widget-title">🏷️ Tags phổ biến</h5>
                        <div class="tags-cloud">
                            @php
                                $tags = ['Du lịch', 'Khám phá', 'Ẩm thực', 'Văn hóa', 'Thiên nhiên', 
                                         'Phượt', 'Check-in', 'Tips', 'Review', 'Kinh nghiệm', 
                                         'Địa điểm', 'Miền Bắc', 'Miền Nam', 'Miền Trung'];
                            @endphp
                            @foreach($tags as $tag)
                            <a href="{{ route('blog') }}?tag={{ urlencode($tag) }}" class="tag-item">
                                {{ $tag }}
                            </a>
                            @endforeach
                        </div>
                    </div>


                    <div class="widget widget-social">
                        <h5 class="widget-title">🌐 Theo dõi chúng tôi</h5>
                        <div class="social-links">
                            <a href="facebook.com" class="social-item facebook">
                                <i class="fab fa-facebook-f"></i>
                                <span>Facebook</span>
                            </a>
                            <a href="instagram.com" class="social-item instagram">
                                <i class="fab fa-instagram"></i>
                                <span>Instagram</span>
                            </a>
                            <a href="youtube.com" class="social-item youtube">
                                <i class="fab fa-youtube"></i>
                                <span>Youtube</span>
                            </a>
                            <a href="tiktok.com" class="social-item tiktok">
                                <i class="fab fa-tiktok"></i>
                                <span>Tiktok</span>
                            </a>
                        </div>
                    </div>

                    <div class="widget widget-ad">
                        <a href="{{ route('about') }}" class="ad-banner">
                            <div class="ad-content">
                                <div class="ad-icon">🎉</div>
                                <h6>Khuyến mãi đặc biệt</h6>
                                <p>Giảm đến 50% cho tour mùa hè</p>
                                <span class="ad-button">Xem ngay</span>
                            </div>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const replyButtons = document.querySelectorAll('.reply-btn');
    replyButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const parentId = this.dataset.id;
            const existingForm = document.querySelector('.reply-form');
            if (existingForm) existingForm.remove();

            const formHtml = `
                <form action="{{ route('blog.comment', $blog->blogId) }}" method="POST" class="reply-form mt-2">
                    @csrf
                    <input type="hidden" name="parent_id" value="${parentId}">
                    <textarea name="content" class="form-control mb-2" rows="2" placeholder="Nhập phản hồi..." required></textarea>
                    <button type="submit" class="btn btn-sm btn-primary">Gửi</button>
                </form>
            `;
            this.insertAdjacentHTML('afterend', formHtml);
        });
    });
});
</script>

@include('clients.blocks.new_letter')
@include('clients.blocks.footer')
