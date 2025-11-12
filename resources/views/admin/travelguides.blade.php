@include('admin.blocks.header')
<div class="container body">
    <div class="main_container">
        @include('admin.blocks.sidebar')

        <div class="right_col" role="main">
            {{-- Hiển thị thông báo --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3>Quản lý Hướng dẫn viên</h3>
                <button id="btnShowForm" class="btn btn-success">Thêm mới</button>
            </div>

            {{-- Form thêm / sửa --}}
            <div id="teamForm" class="card mb-4 p-3" style="display:none;">
                <form id="formTravelGuide" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_method" id="form_method" value="POST">
                    <input type="hidden" name="team_id" id="team_id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Tên</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Chức vụ</label>
                            <input type="text" name="designation" id="designation" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Ảnh đại diện (tên file trong /clients/assets/images/team)</label>
                            <input type="text" name="image" id="image" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Facebook</label>
                            <input type="text" name="facebook" id="facebook" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Twitter</label>
                            <input type="text" name="twitter" id="twitter" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Instagram</label>
                            <input type="text" name="instagram" id="instagram" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Youtube</label>
                            <input type="text" name="youtube" id="youtube" class="form-control">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success" id="btnSubmit">Lưu</button>
                    <button type="button" class="btn btn-secondary" id="btnCancel">Hủy</button>
                </form>
            </div>

            {{-- Danh sách hướng dẫn viên --}}
            <div class="row team-cards">
                @foreach ($teams as $team)
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                        <div class="card shadow-sm text-center">
                            <div class="card-body">
                                <img src="{{ asset('clients/assets/images/team/' . $team->image) }}" 
                                     alt="{{ $team->name }}" class="rounded-circle mb-3" width="120" height="120">
                                <h5>{{ $team->name }}</h5>
                                <p class="text-muted mb-1">{{ $team->designation ?? 'Chưa cập nhật' }}</p>
                                <div class="mb-2">
                                    @if($team->twitter)
                                        <a href="{{ $team->twitter }}" target="_blank" class="mx-1" style="font-size: 20px; color: #1DA1F2;">
                                            <i class="fa fa-twitter"></i>
                                        </a>
                                    @endif
                                    @if($team->facebook)
                                        <a href="{{ $team->facebook }}" target="_blank" class="mx-1" style="font-size: 20px; color: #4267B2;">
                                            <i class="fa fa-facebook"></i>
                                        </a>
                                    @endif
                                    @if($team->instagram)
                                        <a href="{{ $team->instagram }}" target="_blank" class="mx-1" style="font-size: 20px; color: #E4405F;">
                                            <i class="fa fa-instagram"></i>
                                        </a>
                                    @endif
                                    @if($team->youtube)
                                        <a href="{{ $team->youtube }}" target="_blank" class="mx-1" style="font-size: 20px; color: #FF0000;">
                                            <i class="fa fa-youtube-play"></i>
                                        </a>
                                    @endif
                                </div>
                                <p>
                                    <span class="badge {{ $team->status == 'inactive' ? 'bg-warning text-dark' : 'bg-success text-white' }}">
                                        {{ $team->status == 'inactive' ? 'Chưa kích hoạt' : 'Hoạt động' }}
                                    </span>
                                </p>
                                <div class="d-flex justify-content-center gap-2 flex-wrap">
                                    @if($team->status == 'inactive')
                                        <a href="{{ route('admin.team.activate', $team->id) }}" 
                                           class="btn btn-sm btn-primary"
                                           onclick="return confirm('Bạn có chắc muốn kích hoạt hướng dẫn viên này?')">
                                            Kích hoạt
                                        </a>
                                    @endif
                                    <button class="btn btn-sm btn-warning btnEdit"
                                            data-id="{{ $team->id }}"
                                            data-name="{{ $team->name }}"
                                            data-designation="{{ $team->designation }}"
                                            data-image="{{ $team->image }}"
                                            data-facebook="{{ $team->facebook }}"
                                            data-twitter="{{ $team->twitter }}"
                                            data-instagram="{{ $team->instagram }}"
                                            data-youtube="{{ $team->youtube }}">
                                        <i class="fa fa-edit"></i> Sửa
                                    </button>
                                    <a href="{{ route('admin.team.delete', $team->id) }}" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Bạn có chắc muốn xóa hướng dẫn viên này?')">
                                        <i class="fa fa-trash"></i> Xóa
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</div>

@include('admin.blocks.footer')

{{-- Script hiển thị form và load dữ liệu khi edit --}}
<script>
    const btnShowForm = document.getElementById('btnShowForm');
    const teamForm = document.getElementById('teamForm');
    const btnCancel = document.getElementById('btnCancel');
    const form = document.getElementById('formTravelGuide');

    // Hiển thị form thêm mới
    btnShowForm.addEventListener('click', () => {
        form.reset();
        document.getElementById('team_id').value = '';
        document.getElementById('form_method').value = 'POST';
        teamForm.style.display = 'block';
        form.action = "{{ route('admin.team.store') }}";
        document.getElementById('btnSubmit').textContent = 'Thêm mới';
    });

    // Ẩn form
    btnCancel.addEventListener('click', () => {
        teamForm.style.display = 'none';
        form.reset();
    });

    // Load dữ liệu khi edit
    document.querySelectorAll('.btnEdit').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            document.getElementById('team_id').value = id;
            document.getElementById('name').value = btn.dataset.name;
            document.getElementById('designation').value = btn.dataset.designation || '';
            document.getElementById('image').value = btn.dataset.image || '';
            document.getElementById('facebook').value = btn.dataset.facebook || '';
            document.getElementById('twitter').value = btn.dataset.twitter || '';
            document.getElementById('instagram').value = btn.dataset.instagram || '';
            document.getElementById('youtube').value = btn.dataset.youtube || '';
            document.getElementById('form_method').value = 'POST';
            teamForm.style.display = 'block';
            form.action = "{{ url('admin/team/update') }}/" + id;
            document.getElementById('btnSubmit').textContent = 'Cập nhật';
        });
    });

    // Tự động ẩn thông báo sau 5 giây
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            alert.style.display = 'none';
        });
    }, 5000);
</script>