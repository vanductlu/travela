@include('admin.blocks.header')
<div class="container body">
    <div class="main_container">
        @include('admin.blocks.sidebar')

        <div class="right_col" role="main">
            <div class="page-title d-flex justify-content-between align-items-center mb-3">
                <h3>Quản lý người dùng</h3>
            </div>

            <div class="clearfix"></div>

            <div class="row user-cards">
                @foreach ($users as $user)
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                        <div class="card user-card shadow-sm">
                            <div class="card-body d-flex flex-column align-items-center text-center">
                                <div class="user-avatar mb-3">
                                    <img src="{{ asset('clients/assets/images/user-profile/' . $user->avatar) }}" 
                                         alt="{{ $user->fullName }}" 
                                         class="rounded-circle"
                                         style="width: 100px; height: 100px; object-fit: cover;">
                                </div>

                                <h5 class="card-title mb-2">{{ $user->fullName }}</h5>
                                <p class="text-muted mb-1"><strong>Username:</strong> {{ $user->username }}</p>
                                <p class="text-muted mb-1"><strong>Email:</strong> {{ $user->email ?? 'Chưa cập nhật' }}</p>
                                <p class="text-muted mb-1"><strong>Phone:</strong> {{ $user->phoneNumber ?? 'Chưa cập nhật' }}</p>
                                <p class="text-muted mb-3"><strong>Address:</strong> {{ $user->address ?? 'Chưa cập nhật' }}</p>
                                
                                <div class="status-badges mb-3">
                                    <span class="badge {{ $user->isActiveText == 'Chưa kích hoạt' ? 'bg-warning' : 'bg-success' }} me-1">
                                        {{ $user->isActiveText }}
                                    </span>
                                    
                                    @if($user->status === 'b')
                                        <span class="badge bg-danger">Đã chặn</span>
                                    @elseif($user->status === 'd')
                                        <span class="badge bg-secondary">Đã xóa</span>
                                    @else
                                        <span class="badge bg-primary">Hoạt động</span>
                                    @endif
                                </div>

                                <div class="d-flex flex-wrap justify-content-center gap-2">
                                    @if ($user->isActiveText == 'Chưa kích hoạt')
                                        <button class="btn btn-sm btn-primary btn-activate" 
                                                data-user-id="{{ $user->userId }}"
                                                data-user-name="{{ $user->fullName }}">
                                            <i class="fa fa-check"></i> Kích hoạt
                                        </button>
                                    @endif

                                    @if($user->status === 'b')
                                        <button class="btn btn-sm btn-success btn-unblock" 
                                                data-user-id="{{ $user->userId }}"
                                                data-user-name="{{ $user->fullName }}">
                                            <i class="fa fa-unlock"></i> Khôi phục
                                        </button>
                                    @elseif($user->status !== 'd')
                                        <button class="btn btn-sm btn-warning btn-block" 
                                                data-user-id="{{ $user->userId }}"
                                                data-user-name="{{ $user->fullName }}">
                                            <i class="fa fa-ban"></i> Chặn
                                        </button>
                                    @endif

                                    @if($user->status !== 'd')
                                        <button class="btn btn-sm btn-danger btn-delete" 
                                                data-user-id="{{ $user->userId }}"
                                                data-user-name="{{ $user->fullName }}">
                                            <i class="fa fa-trash"></i> Xóa
                                        </button>
                                    @endif
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
<script>
$(document).ready(function() {
    $(document).on('click', '.btn-activate', function() {
        const button = $(this);
        const userId = button.data('user-id');
        const userName = button.data('user-name');

        Swal.fire({
            title: 'Kích hoạt tài khoản?',
            text: `Bạn có muốn kích hoạt tài khoản ${userName}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#007bff',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Kích hoạt',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: '{{ route("admin.active-user") }}',
                    data: {
                        userId: userId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Thành công!',
                                text: response.message,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Lỗi!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Lỗi!', 'Có lỗi xảy ra khi kích hoạt người dùng', 'error');
                    }
                });
            }
        });
    });

    $(document).on('click', '.btn-block', function() {
        const button = $(this);
        const userId = button.data('user-id');
        const userName = button.data('user-name');

        Swal.fire({
            title: 'Chặn người dùng?',
            html: `
                <p>Bạn có chắc muốn chặn <strong>${userName}</strong>?</p>
                <p class="text-warning">Người dùng sẽ không thể đăng nhập</p>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ffc107',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Chặn',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: '{{ route("admin.block-user") }}',
                    data: {
                        userId: userId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Đã chặn!',
                                text: response.message,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Lỗi!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        const errorMsg = xhr.responseJSON?.message || 'Có lỗi xảy ra khi chặn người dùng';
                        Swal.fire('Lỗi!', errorMsg, 'error');
                    }
                });
            }
        });
    });

    $(document).on('click', '.btn-unblock', function() {
        const button = $(this);
        const userId = button.data('user-id');
        const userName = button.data('user-name');

        Swal.fire({
            title: 'Khôi phục người dùng?',
            html: `
                <p>Bạn có muốn khôi phục <strong>${userName}</strong>?</p>
                <p class="text-success">Người dùng sẽ có thể đăng nhập lại</p>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Khôi phục',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: '{{ route("admin.unblock-user") }}',
                    data: {
                        userId: userId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Đã khôi phục!',
                                text: response.message,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Lỗi!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        const errorMsg = xhr.responseJSON?.message || 'Có lỗi xảy ra khi khôi phục người dùng';
                        Swal.fire('Lỗi!', errorMsg, 'error');
                    }
                });
            }
        });
    });

$(document).on('click', '.btn-delete', function() {
    const button = $(this);
    const userId = button.data('user-id');
    const userName = button.data('user-name');

    Swal.fire({
        title: 'Xác nhận xóa?',
        html: `
            <p>Bạn có chắc muốn xóa <strong>${userName}</strong>?</p>
            <div class="text-danger text-left" style="font-size: 14px; margin: 15px 0;">
                <p><strong>Hành động này sẽ xóa:</strong></p>
                <ul style="list-style-position: inside;">
                    <li>Tất cả bookings của user</li>
                    <li>Tất cả checkout/thanh toán</li>
                    <li>Tất cả reviews của user</li>
                    <li>Tất cả comments của user</li>
                </ul>
                <p class="font-weight-bold"> KHÔNG THỂ KHÔI PHỤC!</p>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Xóa vĩnh viễn',
        cancelButtonText: 'Hủy',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '/admin/delete-user/' + userId,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        resolve(response);
                    },
                    error: function (xhr) {
                        reject(xhr);
                    }
                });
            }).catch(xhr => {
                Swal.showValidationMessage(
                    `Lỗi: ${xhr.responseJSON?.message || 'Không thể xóa người dùng'}`
                );
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed && result.value.success) {
            Swal.fire({
                title: 'Đã xóa!',
                html: `
                    <p>${result.value.message}</p>
                    <div class="text-left" style="font-size: 14px; margin-top: 15px;">
                        <p><strong>Thống kê dữ liệu đã xóa:</strong></p>
                        <ul style="list-style-position: inside;">
                            <li>Checkouts: ${result.value.data.checkouts_deleted}</li>
                            <li>Bookings: ${result.value.data.bookings_deleted}</li>
                            <li>Reviews: ${result.value.data.reviews_deleted}</li>
                            <li>Comments: ${result.value.data.comments_deleted}</li>
                        </ul>
                    </div>
                `,
                icon: 'success',
                timer: 4000,
                showConfirmButton: true,
                confirmButtonText: 'OK'
            }).then(() => {
                location.reload();
            });
        }
    });
});


});
</script>
<style>
.user-card {
    transition: all 0.3s ease;
    border: 1px solid #e0e0e0;
}

.user-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.15) !important;
}

.user-avatar img {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border: 3px solid #007bff;
}

.status-badges {
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
    justify-content: center;
}

.btn-action {
    min-width: 90px;
}

.gap-2 {
    gap: 0.5rem !important;
}
</style>
