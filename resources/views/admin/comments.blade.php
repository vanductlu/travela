@include('admin.blocks.header')
<div class="container body">
    <div class="main_container">
        @include('admin.blocks.sidebar')

        <div class="right_col" role="main">
            <div class="page-title">
                <div class="title_left">
                    <h3>Quản lý Bình luận</h3>
                </div>
            </div>

            <div class="clearfix"></div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="row">
                <div class="col-md-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Danh sách bình luận</h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a></li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>

                        <div class="x_content">
                            <table id="datatable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Tên người dùng</th>
                                        <th>Nội dung</th>
                                        <th>Bài viết</th>
                                        <th>Ngày bình luận</th>
                                        <th>Trả lời cho</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($comments as $key => $comment)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $comment->name }}</td>
                                        <td>{{ Str::limit($comment->content, 80) }}</td>
                                        <td>
                                            @if($comment->blog_title)
                                                <a href="{{ route('blog-details', $comment->blog_id) }}" target="_blank">
                                                    {{ $comment->blog_title }}
                                                </a>
                                            @else
                                                <span class="text-muted">Đã xóa</span>
                                            @endif
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($comment->created_at)->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @if($comment->parent_id)
                                                <span class="badge bg-info">Trả lời #{{ $comment->parent_id }}</span>
                                            @else
                                                <span class="badge bg-secondary">Bình luận gốc</span>
                                            @endif
                                        </td>
                                        <td>
                                            <form action="{{ route('admin.comments.delete', $comment->id) }}"
                                                  method="POST" onsubmit="return confirm('Xóa bình luận này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        @include('admin.blocks.footer')
    </div>
</div>
