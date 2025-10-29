@include('admin.blocks.header')
<div class="container body">
    <div class="main_container">
        @include('admin.blocks.sidebar')

        <!-- page content -->
        <div class="right_col" role="main">
            <div class="">
                <div class="page-title">
                    <div class="title_left">
                        <h3>Quản lý Bài viết</h3>
                    </div>
                </div>
                <div class="clearfix"></div>

                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h2>Danh sách bài viết</h2>
                                <ul class="nav navbar-right panel_toolbox">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                                    <li><a class="close-link"><i class="fa fa-close"></i></a></li>
                                </ul>
                                <div class="clearfix"></div>
                            </div>

                            <div class="x_content">

                                <!-- Nút thêm bài viết -->
                                <a href="{{ route('admin.blog.create') }}" class="btn btn-success mb-3">
                                    <i class="fa fa-plus"></i> Thêm bài viết mới
                                </a>

                                <!-- Bảng danh sách -->
                                <table id="datatable" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Hình ảnh</th>
                                            <th>Tiêu đề</th>
                                            <th>Thể loại</th>
                                            <th>Lượt xem</th>
                                            <th>Ngày tạo</th>
                                            <th>Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($blogs as $key => $blog)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>
                                                @if($blog->image)
                                                    <img src="{{ asset('clients/assets/images/blog/'.$blog->image) }}"
                                                         width="70" height="50" style="object-fit:cover">
                                                @else
                                                    <span class="text-muted">Không có ảnh</span>
                                                @endif
                                            </td>
                                            <td>{{ $blog->title }}</td>
                                            <td>{{ $blog->category }}</td>
                                            <td>{{ $blog->views }}</td>
                                            <td>{{ date('d/m/Y', strtotime($blog->created_at)) }}</td>
                                            <td>
                                                <a href="{{ route('admin.blog.edit', $blog->blogId) }}"
                                                   class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i></a>

                                                <form action="{{ route('admin.blog.delete', $blog->blogId) }}"
                                                      method="POST" style="display:inline-block"
                                                      onsubmit="return confirm('Bạn có chắc muốn xóa bài viết này?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
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

                {{-- Form thêm bài viết --}}
                @if(request()->is('admin/blog/create'))
                <div class="row">
                    <div class="col-md-12 col-sm-12 ">
                        <div class="x_panel">
                            <div class="x_title">
                                <h2>Thêm bài viết mới</h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <form action="{{ route('admin.blog.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <label>Tiêu đề *</label>
                                        <input type="text" name="title" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Thể loại *</label>
                                        <input type="text" name="category" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Tóm tắt</label>
                                        <textarea name="excerpt" class="form-control" rows="3"></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label>Nội dung *</label>
                                        <textarea name="content" id="content" rows="10" class="form-control"></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label>Ảnh đại diện</label>
                                        <input type="file" name="image" class="form-control">
                                    </div>

                                    <button type="submit" class="btn btn-success">Lưu bài viết</button>
                                    <a href="{{ route('admin.blog') }}" class="btn btn-secondary">Hủy</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Form chỉnh sửa --}}
                @if(request()->is('admin/blog/*/edit'))
                <div class="row">
                    <div class="col-md-12 col-sm-12 ">
                        <div class="x_panel">
                            <div class="x_title">
                                <h2>Chỉnh sửa bài viết</h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <form action="{{ route('admin.blog.update', $blog->blogId) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')

                                    <div class="form-group">
                                        <label>Tiêu đề *</label>
                                        <input type="text" name="title" class="form-control" value="{{ $blog->title }}" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Thể loại *</label>
                                        <input type="text" name="category" class="form-control" value="{{ $blog->category }}" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Tóm tắt</label>
                                        <textarea name="excerpt" class="form-control" rows="3">{{ $blog->excerpt }}</textarea>
                                    </div>

                                    <div class="form-group">
                                        <label>Nội dung *</label>
                                        <textarea name="content" id="content" rows="10" class="form-control">{{ $blog->content }}</textarea>
                                    </div>

                                    <div class="form-group">
                                        <label>Ảnh đại diện</label>
                                        <input type="file" name="image" class="form-control">
                                        @if($blog->image)
                                            <img src="{{ asset('clients/assets/images/blog/'.$blog->image) }}"
                                                 width="120" height="80" class="mt-2">
                                        @endif
                                    </div>

                                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                                    <a href="{{ route('admin.blog') }}" class="btn btn-secondary">Hủy</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

            </div>
        </div>
        <!-- /page content -->

        @include('admin.blocks.footer')
    </div>
</div>