@include('admin.blocks.header')
<div class="container body">
    <div class="main_container">
        @include('admin.blocks.sidebar')

        <div class="right_col" role="main">
            <div class="">
                <div class="page-title">
                    <div class="title_left">
                        <h3>Chi tiết mã giảm giá <small>{{ $coupon->code }}</small></h3>
                    </div>
                    <div class="title_right">
                        <div class="pull-right">
                            <a href="{{ route('admin.coupon.edit', $coupon->couponId) }}" class="btn btn-warning btn-sm">
                                <i class="fa fa-edit"></i> Chỉnh sửa
                            </a>
                            <a href="{{ route('admin.coupon.index') }}" class="btn btn-default btn-sm">
                                <i class="fa fa-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </div>
                </div>

                <div class="clearfix"></div>

                @if(\Carbon\Carbon::now()->gt($coupon->end_date))
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-danger alert-dismissible fade in" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <i class="fa fa-exclamation-circle"></i>
                            <strong>Mã này đã hết hạn</strong> vào ngày {{ $coupon->end_date->format('d/m/Y') }}
                        </div>
                    </div>
                </div>
                @elseif(\Carbon\Carbon::now()->lt($coupon->start_date))
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-warning alert-dismissible fade in" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <i class="fa fa-clock-o"></i>
                            <strong>Mã chưa có hiệu lực.</strong> Sẽ bắt đầu từ {{ $coupon->start_date->format('d/m/Y') }}
                        </div>
                    </div>
                </div>
                @elseif($coupon->status === 'inactive')
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-info alert-dismissible fade in" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <i class="fa fa-ban"></i>
                            <strong>Mã đang bị vô hiệu hóa.</strong>
                        </div>
                    </div>
                </div>
                @else
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-success alert-dismissible fade in" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <i class="fa fa-check-circle"></i>
                            <strong>Mã đang hoạt động bình thường.</strong>
                        </div>
                    </div>
                </div>
                @endif

                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h2><i class="fa fa-info-circle"></i> Thông tin cơ bản</h2>
                                <ul class="nav navbar-right panel_toolbox">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                                </ul>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <table class="table table-bordered table-hover">
                                    <tbody>
                                        <tr>
                                            <th width="40%" class="active">Mã giảm giá</th>
                                            <td>
                                                <strong class="text-primary" style="font-size: 1.2rem;">{{ $coupon->code }}</strong>
                                                <button class="btn btn-xs btn-link btn-copy-code p-0" 
                                                        data-code="{{ $coupon->code }}"
                                                        style="margin-left: 10px;">
                                                    <i class="fa fa-copy text-info"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="active">Loại giảm giá</th>
                                            <td>
                                                @if($coupon->discount_type === 'percent')
                                                    <span class="label label-info label-lg">
                                                        <i class="fa fa-percent"></i> Phần trăm
                                                    </span>
                                                @else
                                                    <span class="label label-warning label-lg">
                                                        <i class="fa fa-money"></i> Cố định
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="active">Giá trị giảm</th>
                                            <td>
                                                <strong class="text-success" style="font-size: 1.2rem;">
                                                    @if($coupon->discount_type === 'percent')
                                                        {{ $coupon->discount_value }}%
                                                    @else
                                                        {{ number_format($coupon->discount_value) }} VNĐ
                                                    @endif
                                                </strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="active">Đơn hàng tối thiểu</th>
                                            <td>
                                                @if($coupon->min_order_value)
                                                    <strong>{{ number_format($coupon->min_order_value) }} VNĐ</strong>
                                                @else
                                                    <span class="text-muted">Không yêu cầu</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="active">Giảm giá tối đa</th>
                                            <td>
                                                @if($coupon->max_discount)
                                                    <strong class="text-danger">{{ number_format($coupon->max_discount) }} VNĐ</strong>
                                                @else
                                                    <span class="text-muted">Không giới hạn</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="active">Thời gian hiệu lực</th>
                                            <td>
                                                <div class="mb-1">
                                                    <i class="fa fa-calendar text-success"></i> 
                                                    <strong>Từ:</strong> {{ $coupon->start_date->format('d/m/Y') }}
                                                </div>
                                                <div>
                                                    <i class="fa fa-calendar text-danger"></i> 
                                                    <strong>Đến:</strong> {{ $coupon->end_date->format('d/m/Y') }}
                                                </div>
                                                <div class="mt-2">
                                                    <small class="text-muted">
                                                        Còn lại: 
                                                        @php
                                                            $daysLeft = \Carbon\Carbon::now()->diffInDays($coupon->end_date, false);
                                                        @endphp
                                                        @if($daysLeft > 0)
                                                            <strong class="text-success">{{ $daysLeft }} ngày</strong>
                                                        @else
                                                            <strong class="text-danger">Đã hết hạn</strong>
                                                        @endif
                                                    </small>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="active">Trạng thái</th>
                                            <td>
                                                @if($coupon->status === 'active')
                                                    <span class="label label-success label-lg">
                                                        <i class="fa fa-check"></i> Hoạt động
                                                    </span>
                                                @else
                                                    <span class="label label-default label-lg">
                                                        <i class="fa fa-times"></i> Không hoạt động
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="active">Mô tả</th>
                                            <td>{{ $coupon->description ?? 'Không có' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="active">Ngày tạo</th>
                                            <td>{{ $coupon->created_at->format('d/m/Y H:i:s') }}</td>
                                        </tr>
                                        <tr>
                                            <th class="active">Cập nhật gần nhất</th>
                                            <td>{{ $coupon->updated_at->format('d/m/Y H:i:s') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-sm-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h2><i class="fa fa-bar-chart"></i> Thống kê sử dụng</h2>
                                <ul class="nav navbar-right panel_toolbox">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                                </ul>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <div class="row text-center" style="margin-bottom: 20px;">
                                    <div class="col-xs-6">
                                        <div class="well" style="background: #f7f7f7;">
                                            <h6 class="text-uppercase text-muted" style="margin-bottom: 10px;">Giới hạn</h6>
                                            <h2 class="text-secondary" style="margin: 0;">
                                                @if($coupon->usage_limit)
                                                    {{ $coupon->usage_limit }}
                                                @else
                                                    <i class="fa fa-infinity"></i>
                                                @endif
                                            </h2>
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="well" style="background: #f7f7f7;">
                                            <h6 class="text-uppercase text-muted" style="margin-bottom: 10px;">Đã sử dụng</h6>
                                            <h2 class="text-primary" style="margin: 0;">
                                                {{ $coupon->used_count }}
                                            </h2>
                                        </div>
                                    </div>
                                </div>

                                @if($coupon->usage_limit)
                                    @php
                                        $percentage = ($coupon->used_count / $coupon->usage_limit) * 100;
                                        $progressClass = $percentage < 50 ? 'progress-bar-success' : ($percentage < 80 ? 'progress-bar-warning' : 'progress-bar-danger');
                                    @endphp
                                    <div style="margin-bottom: 15px;">
                                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                            <small class="text-muted">Tỉ lệ sử dụng</small>
                                            <small class="text-muted">{{ number_format($percentage, 1) }}%</small>
                                        </div>
                                        <div class="progress" style="height: 25px; margin-bottom: 0;">
                                            <div class="progress-bar {{ $progressClass }}" 
                                                 role="progressbar" 
                                                 style="width: {{ min($percentage, 100) }}%">
                                                <strong>{{ $coupon->used_count }} / {{ $coupon->usage_limit }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="alert {{ $percentage < 80 ? 'alert-info' : 'alert-warning' }}">
                                        <i class="fa fa-info-circle"></i> 
                                        Còn lại: <strong>{{ max(0, $coupon->usage_limit - $coupon->used_count) }}</strong> lượt sử dụng
                                    </div>
                                @else
                                    <div class="alert alert-success">
                                        <i class="fa fa-infinity"></i> 
                                        <strong>Không giới hạn</strong> số lần sử dụng
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h2><i class="fa fa-calculator"></i> Ví dụ áp dụng mã giảm giá</h2>
                                <ul class="nav navbar-right panel_toolbox">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                                </ul>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr class="headings">
                                                <th>Giá gốc đơn hàng</th>
                                                <th>Giảm giá</th>
                                                <th>Thành tiền</th>
                                                <th>Trạng thái</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $testAmounts = [100000, 500000, 1000000, 2000000, 5000000];
                                            @endphp
                                            @foreach($testAmounts as $amount)
                                                @php
                                                    $canApply = !$coupon->min_order_value || $amount >= $coupon->min_order_value;
                                                    $discount = 0;
                                                    if ($canApply) {
                                                        $discount = $coupon->calculateDiscount($amount);
                                                    }
                                                    $final = $amount - $discount;
                                                    $savePercent = $amount > 0 ? ($discount / $amount) * 100 : 0;
                                                @endphp
                                                <tr class="{{ $canApply ? '' : 'warning' }}">
                                                    <td>
                                                        <strong>{{ number_format($amount) }} đ</strong>
                                                    </td>
                                                    <td>
                                                        @if($canApply)
                                                            <span class="text-success">
                                                                <i class="fa fa-arrow-down"></i>
                                                                <strong>{{ number_format($discount) }} đ</strong>
                                                            </span>
                                                            <br>
                                                            <small class="text-muted">({{ number_format($savePercent, 1) }}%)</small>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <strong class="{{ $canApply ? 'text-primary' : '' }}" style="font-size: 1.1rem;">
                                                            {{ number_format($final) }} đ
                                                        </strong>
                                                    </td>
                                                    <td>
                                                        @if($canApply)
                                                            <span class="label label-success">
                                                                <i class="fa fa-check"></i> Áp dụng được
                                                            </span>
                                                        @else
                                                            <span class="label label-default">
                                                                <i class="fa fa-times"></i> Không đủ điều kiện
                                                            </span>
                                                            <br>
                                                            <small class="text-muted">
                                                                Yêu cầu tối thiểu: {{ number_format($coupon->min_order_value) }} đ
                                                            </small>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="alert alert-info">
                                    <i class="fa fa-lightbulb-o"></i>
                                    <strong>Lưu ý:</strong> Đây là ví dụ minh họa. Kết quả thực tế có thể khác nhau tùy vào điều kiện áp dụng.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('admin.blocks.footer')

<style>
.label-lg {
    padding: 0.5rem 0.75rem;
    font-size: 0.9rem;
}
.well {
    min-height: 20px;
    padding: 19px;
    margin-bottom: 20px;
    background-color: #f5f5f5;
    border: 1px solid #e3e3e3;
    border-radius: 4px;
}
</style>

<script>
$(document).ready(function() {
    $('.btn-copy-code').on('click', function() {
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
});
</script>