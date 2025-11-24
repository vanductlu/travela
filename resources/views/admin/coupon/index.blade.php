@include('admin.blocks.header')
<div class="container body">
    <div class="main_container">
        @include('admin.blocks.sidebar')

        <!-- page content -->
        <div class="right_col" role="main">
            <div class="">
                <div class="page-title">
                    <div class="title_left">
                        <h3>{{ $title }} <small>Quản lý mã giảm giá</small></h3>
                    </div>
                    <div class="title_right">
                        <div class="pull-right">
                            <button type="button" class="btn btn-success btn-sm" id="btn-export-coupons">
                                <i class="fa fa-file-excel-o"></i> Xuất Excel
                            </button>
                            <a href="{{ route('admin.coupon.create') }}" class="btn btn-primary btn-sm">
                                <i class="fa fa-plus"></i> Thêm mã giảm giá
                            </a>
                        </div>
                    </div>
                </div>

                <div class="clearfix"></div>

                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-md-3 col-sm-6">
                        <div class="x_panel tile">
                            <div class="x_content">
                                <div class="row">
                                    <div class="col-xs-7">
                                        <span class="text-muted">Tổng số mã</span>
                                        <h2 class="stat-number">{{ $stats['total'] }}</h2>
                                    </div>
                                    <div class="col-xs-5 text-right">
                                        <i class="fa fa-ticket fa-3x" style="opacity: 0.3;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <div class="x_panel tile">
                            <div class="x_content">
                                <div class="row">
                                    <div class="col-xs-7">
                                        <span class="text-success">Đang hoạt động</span>
                                        <h2 class="stat-number text-success">{{ $stats['active'] }}</h2>
                                    </div>
                                    <div class="col-xs-5 text-right">
                                        <i class="fa fa-check-circle fa-3x text-success" style="opacity: 0.3;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <div class="x_panel tile">
                            <div class="x_content">
                                <div class="row">
                                    <div class="col-xs-7">
                                        <span class="text-danger">Đã hết hạn</span>
                                        <h2 class="stat-number text-danger">{{ $stats['expired'] }}</h2>
                                    </div>
                                    <div class="col-xs-5 text-right">
                                        <i class="fa fa-calendar-times-o fa-3x text-danger" style="opacity: 0.3;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <div class="x_panel tile">
                            <div class="x_content">
                                <div class="row">
                                    <div class="col-xs-7">
                                        <span class="text-info">Lượt sử dụng</span>
                                        <h2 class="stat-number text-info">{{ $stats['used'] }}</h2>
                                    </div>
                                    <div class="col-xs-5 text-right">
                                        <i class="fa fa-bar-chart fa-3x text-info" style="opacity: 0.3;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h2>Danh sách mã giảm giá</h2>
                                <ul class="nav navbar-right panel_toolbox">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                                    <li><a class="close-link"><i class="fa fa-close"></i></a></li>
                                </ul>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <!-- Filters -->
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label>Lọc theo trạng thái</label>
                                        <select id="filter_status" class="form-control">
                                            <option value="">Tất cả</option>
                                            <option value="Hoạt động">Hoạt động</option>
                                            <option value="Không hoạt động">Không hoạt động</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="card-box table-responsive">
                                            <p class="text-muted font-13 m-b-30">
                                                Chào mừng bạn đến với trang quản lý mã giảm giá. Tại đây, bạn có thể tạo, chỉnh sửa, 
                                                và quản lý tất cả các mã giảm giá cho hệ thống.
                                            </p>
                                            <table id="datatable-coupon" class="table table-striped table-bordered" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Mã</th>
                                                        <th>Loại</th>
                                                        <th>Giá trị</th>
                                                        <th>Đơn tối thiểu</th>
                                                        <th>Giảm tối đa</th>
                                                        <th>Giới hạn</th>
                                                        <th>Đã dùng</th>
                                                        <th>Thời gian</th>
                                                        <th>Trạng thái</th>
                                                        <th>Hành động</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($coupons as $coupon)
                                                    <tr>
                                                        <td>{{ $coupon->couponId }}</td>
                                                        <td>
                                                            <strong class="text-primary">{{ $coupon->code }}</strong>
                                                            <button class="btn btn-xs btn-link btn-copy-code p-0" 
                                                                    data-code="{{ $coupon->code }}" 
                                                                    title="Copy mã">
                                                                <i class="fa fa-copy text-info"></i>
                                                            </button>
                                                        </td>
                                                        <td>
                                                            @if($coupon->discount_type === 'percent')
                                                                <span class="label label-info">Phần trăm</span>
                                                            @else
                                                                <span class="label label-warning">Cố định</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($coupon->discount_type === 'percent')
                                                                <strong>{{ $coupon->discount_value }}%</strong>
                                                            @else
                                                                <strong>{{ number_format($coupon->discount_value) }} VNĐ</strong>
                                                            @endif
                                                        </td>
                                                        <td>{{ $coupon->min_order_value ? number_format($coupon->min_order_value) . ' VNĐ' : '-' }}</td>
                                                        <td>{{ $coupon->max_discount ? number_format($coupon->max_discount) . ' VNĐ' : '-' }}</td>
                                                        <td>
                                                            @if($coupon->usage_limit)
                                                                <span class="label label-default">{{ $coupon->usage_limit }}</span>
                                                            @else
                                                                <span class="text-muted">Không giới hạn</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span class="label label-primary">{{ $coupon->used_count }}</span>
                                                            @if($coupon->usage_limit)
                                                                <small class="text-muted">/ {{ $coupon->usage_limit }}</small>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <small>
                                                                <i class="fa fa-calendar"></i> {{ \Carbon\Carbon::parse($coupon->start_date)->format('d/m/Y') }}<br>
                                                                <i class="fa fa-calendar"></i> {{ \Carbon\Carbon::parse($coupon->end_date)->format('d/m/Y') }}
                                                            </small>
                                                        </td>
                                                        <td class="text-center">
                                                            <button class="btn btn-xs btn-toggle-status {{ $coupon->status === 'active' ? 'btn-success' : 'btn-default' }}"
                                                                    data-id="{{ $coupon->couponId }}"
                                                                    title="Click để đổi trạng thái">
                                                                @if($coupon->status === 'active')
                                                                    <i class="fa fa-check"></i> Hoạt động
                                                                @else
                                                                    <i class="fa fa-times"></i> Không hoạt động
                                                                @endif
                                                            </button>
                                                        </td>
                                                        <td class="text-center">
                                                            <a href="{{ route('admin.coupon.show', $coupon->couponId) }}" 
                                                               class="btn btn-info btn-xs" 
                                                               title="Xem chi tiết">
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                            <a href="{{ route('admin.coupon.edit', $coupon->couponId) }}" 
                                                               class="btn btn-warning btn-xs" 
                                                               title="Chỉnh sửa">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                            <button class="btn btn-danger btn-xs btn-delete-coupon" 
                                                                    data-id="{{ $coupon->couponId }}"
                                                                    data-code="{{ $coupon->code }}"
                                                                    title="Xóa">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    @empty
                                                    <tr>
                                                        <td colspan="11" class="text-center text-muted">
                                                            <i class="fa fa-inbox fa-3x"></i>
                                                            <p>Chưa có mã giảm giá nào</p>
                                                        </td>
                                                    </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /page content -->
    </div>
</div>
@include('admin.blocks.footer')

<style>
.stat-number {
    font-weight: bold;
    font-size: 2rem;
    margin: 5px 0;
}
.tile {
    margin-bottom: 20px;
    min-height: 100px;
}
.btn-copy-code {
    cursor: pointer;
    padding: 0;
    margin-left: 5px;
}
.btn-copy-code:hover {
    transform: scale(1.2);
}
</style>

<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#datatable-coupon').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/vi.json'
        },
        order: [[0, 'desc']],
        columnDefs: [
            { orderable: false, targets: -1 }
        ]
    });

    // Filter by status
    $('#filter_status').on('change', function() {
        const status = $(this).val();
        if (status) {
            table.column(9).search(status).draw();
        } else {
            table.column(9).search('').draw();
        }
    });

    // Statistics animation
    $('.stat-number').each(function() {
        const $this = $(this);
        const countTo = parseInt($this.text());
        
        $({ countNum: 0 }).animate({
            countNum: countTo
        }, {
            duration: 1000,
            easing: 'swing',
            step: function() {
                $this.text(Math.floor(this.countNum));
            },
            complete: function() {
                $this.text(countTo);
            }
        });
    });

    // Copy coupon code
    $(document).on('click', '.btn-copy-code', function() {
        const code = $(this).data('code');
        const tempInput = $('<input>');
        $('body').append(tempInput);
        tempInput.val(code).select();
        document.execCommand('copy');
        tempInput.remove();
        
        new PNotify({
            title: 'Thành công',
            text: 'Đã copy mã: ' + code,
            type: 'success',
            styling: 'bootstrap3'
        });
    });

    // Delete coupon
    $(document).on('click', '.btn-delete-coupon', function(e) {
        e.preventDefault();
        const couponId = $(this).data('id');
        const couponCode = $(this).data('code');
        
        if (!confirm('Bạn có chắc chắn muốn xóa mã giảm giá "' + couponCode + '"?')) {
            return;
        }

        $.ajax({
            url: '/admin/coupon/' + couponId,
            method: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    new PNotify({
                        title: 'Thành công',
                        text: response.message || 'Xóa mã giảm giá thành công',
                        type: 'success',
                        styling: 'bootstrap3'
                    });
                    setTimeout(() => location.reload(), 1000);
                } else {
                    new PNotify({
                        title: 'Lỗi',
                        text: response.message || 'Không thể xóa mã giảm giá',
                        type: 'error',
                        styling: 'bootstrap3'
                    });
                }
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON?.message || 'Có lỗi xảy ra khi xóa mã giảm giá';
                new PNotify({
                    title: 'Lỗi',
                    text: errorMsg,
                    type: 'error',
                    styling: 'bootstrap3'
                });
            }
        });
    });

    // Toggle status
    $(document).on('click', '.btn-toggle-status', function(e) {
        e.preventDefault();
        const couponId = $(this).data('id');
        const $btn = $(this);
        
        $.ajax({
            url: '/admin/coupon/' + couponId + '/toggle-status',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    new PNotify({
                        title: 'Thành công',
                        text: response.message,
                        type: 'success',
                        styling: 'bootstrap3'
                    });
                    
                    // Update button
                    if (response.status === 'active') {
                        $btn.removeClass('btn-default').addClass('btn-success')
                            .html('<i class="fa fa-check"></i> Hoạt động');
                    } else {
                        $btn.removeClass('btn-success').addClass('btn-default')
                            .html('<i class="fa fa-times"></i> Không hoạt động');
                    }
                } else {
                    new PNotify({
                        title: 'Lỗi',
                        text: response.message,
                        type: 'error',
                        styling: 'bootstrap3'
                    });
                }
            },
            error: function(xhr) {
                new PNotify({
                    title: 'Lỗi',
                    text: 'Có lỗi xảy ra khi cập nhật trạng thái',
                    type: 'error',
                    styling: 'bootstrap3'
                });
            }
        });
    });

    // Export Excel
    $('#btn-export-coupons').on('click', function() {
        const table = $('#datatable-coupon').DataTable();
        const data = table.rows({ search: 'applied' }).data();
        
        let csv = 'Mã,Loại,Giá trị,Đơn tối thiểu,Giảm tối đa,Giới hạn,Đã dùng,Bắt đầu,Kết thúc,Trạng thái\n';
        
        data.each(function(row) {
            // Extract text from HTML
            const code = $(row[1]).find('strong').text();
            const type = $(row[2]).text();
            const value = $(row[3]).text();
            const min = row[4];
            const max = row[5];
            const limit = $(row[6]).text();
            const used = $(row[7]).find('.label').text();
            const dates = $(row[8]).text().replace(/\s+/g, ' ').trim();
            const status = $(row[9]).text().trim();
            
            csv += `"${code}","${type}","${value}","${min}","${max}","${limit}","${used}","${dates}","${status}"\n`;
        });
        
        const blob = new Blob(["\uFEFF" + csv], { type: 'text/csv;charset=utf-8;' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'coupons_' + new Date().getTime() + '.csv';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
        
        new PNotify({
            title: 'Thành công',
            text: 'Xuất file thành công',
            type: 'success',
            styling: 'bootstrap3'
        });
    });
});
</script>