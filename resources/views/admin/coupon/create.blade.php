@include('admin.blocks.header')
<div class="container body">
    <div class="main_container">
        @include('admin.blocks.sidebar')

        <!-- page content -->
        <div class="right_col" role="main">
            <div class="">
                <div class="page-title">
                    <div class="title_left">
                        <h3>Tạo mã giảm giá mới</h3>
                    </div>
                    <div class="title_right">
                        <div class="pull-right">
                            <a href="{{ route('admin.coupon.index') }}" class="btn btn-default btn-sm">
                                <i class="fa fa-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </div>
                </div>

                <div class="clearfix"></div>

                <div class="row">
                    <!-- Form chính -->
                    <div class="col-md-8 col-sm-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h2><i class="fa fa-edit"></i> Thông tin mã giảm giá</h2>
                                <ul class="nav navbar-right panel_toolbox">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                                </ul>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <form id="couponForm" action="{{ route('admin.coupon.store') }}" method="POST" class="form-horizontal form-label-left">
                                    @csrf

                                    <!-- Mã coupon -->
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3">
                                            Mã giảm giá <span class="required">*</span>
                                        </label>
                                        <div class="col-md-9 col-sm-9">
                                            <div class="input-group">
                                                <input type="text" 
                                                       class="form-control @error('code') has-error @enderror" 
                                                       id="code" 
                                                       name="code" 
                                                       value="{{ old('code') }}" 
                                                       placeholder="Ví dụ: SUMMER2024"
                                                       style="text-transform: uppercase;"
                                                       required>
                                                <span class="input-group-btn">
                                                    <button type="button" class="btn btn-default" id="btn-generate-code">
                                                        <i class="fa fa-random"></i> Tạo tự động
                                                    </button>
                                                </span>
                                            </div>
                                            @error('code')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                            <small class="text-muted">Mã phải là duy nhất và viết hoa</small>
                                        </div>
                                    </div>

                                    <!-- Tiền tố -->
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3">Tiền tố</label>
                                        <div class="col-md-9 col-sm-9">
                                            <input type="text" class="form-control" id="code_prefix" placeholder="Ví dụ: SUMMER" maxlength="10">
                                            <small class="text-muted">Nhập tiền tố rồi click "Tạo tự động"</small>
                                        </div>
                                    </div>

                                    <div class="ln_solid"></div>

                                    <!-- Loại giảm giá -->
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3">
                                            Loại giảm giá <span class="required">*</span>
                                        </label>
                                        <div class="col-md-9 col-sm-9">
                                            <select class="form-control @error('discount_type') has-error @enderror" 
                                                    id="discount_type" 
                                                    name="discount_type" 
                                                    required>
                                                <option value="percent" {{ old('discount_type') === 'percent' ? 'selected' : '' }}>
                                                    Phần trăm (%)
                                                </option>
                                                <option value="fixed" {{ old('discount_type') === 'fixed' ? 'selected' : '' }}>
                                                    Số tiền cố định (VNĐ)
                                                </option>
                                            </select>
                                            @error('discount_type')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Giá trị giảm -->
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3" id="discount_value_label">
                                            Giá trị giảm <span class="required">*</span>
                                        </label>
                                        <div class="col-md-9 col-sm-9">
                                            <input type="number" 
                                                   class="form-control currency-input @error('discount_value') has-error @enderror" 
                                                   id="discount_value" 
                                                   name="discount_value" 
                                                   value="{{ old('discount_value') }}" 
                                                   step="0.01"
                                                   min="0"
                                                   placeholder="Nhập giá trị"
                                                   required>
                                            @error('discount_value')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Giảm giá tối đa -->
                                    <div class="form-group" id="max_discount_group">
                                        <label class="control-label col-md-3 col-sm-3">Giảm giá tối đa (VNĐ)</label>
                                        <div class="col-md-9 col-sm-9">
                                            <input type="number" 
                                                   class="form-control currency-input @error('max_discount') has-error @enderror" 
                                                   id="max_discount" 
                                                   name="max_discount" 
                                                   value="{{ old('max_discount') }}" 
                                                   step="1000"
                                                   min="0"
                                                   placeholder="Để trống nếu không giới hạn">
                                            @error('max_discount')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                            <small class="text-muted">Áp dụng khi loại giảm giá là phần trăm</small>
                                        </div>
                                    </div>

                                    <div class="ln_solid"></div>
                                    <h4><i class="fa fa-cog"></i> Điều kiện áp dụng</h4>
                                    <div class="ln_solid"></div>

                                    <!-- Giá trị đơn hàng tối thiểu -->
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3">Đơn hàng tối thiểu (VNĐ)</label>
                                        <div class="col-md-9 col-sm-9">
                                            <input type="number" 
                                                   class="form-control currency-input @error('min_order_value') has-error @enderror" 
                                                   id="min_order_value" 
                                                   name="min_order_value" 
                                                   value="{{ old('min_order_value') }}" 
                                                   step="1000"
                                                   min="0"
                                                   placeholder="Để trống nếu không yêu cầu">
                                            @error('min_order_value')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Giới hạn sử dụng -->
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3">Giới hạn sử dụng</label>
                                        <div class="col-md-9 col-sm-9">
                                            <input type="number" 
                                                   class="form-control @error('usage_limit') has-error @enderror" 
                                                   id="usage_limit" 
                                                   name="usage_limit" 
                                                   value="{{ old('usage_limit') }}" 
                                                   min="1"
                                                   placeholder="Để trống nếu không giới hạn">
                                            @error('usage_limit')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="ln_solid"></div>
                                    <h4><i class="fa fa-calendar"></i> Thời gian hiệu lực</h4>
                                    <div class="ln_solid"></div>

                                    <!-- Ngày bắt đầu -->
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3">
                                            Ngày bắt đầu <span class="required">*</span>
                                        </label>
                                        <div class="col-md-9 col-sm-9">
                                            <input type="date" 
                                                   class="form-control @error('start_date') has-error @enderror" 
                                                   id="start_date" 
                                                   name="start_date" 
                                                   value="{{ old('start_date', date('Y-m-d')) }}" 
                                                   required>
                                            @error('start_date')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Ngày kết thúc -->
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3">
                                            Ngày kết thúc <span class="required">*</span>
                                        </label>
                                        <div class="col-md-9 col-sm-9">
                                            <input type="date" 
                                                   class="form-control @error('end_date') has-error @enderror" 
                                                   id="end_date" 
                                                   name="end_date" 
                                                   value="{{ old('end_date') }}" 
                                                   required>
                                            @error('end_date')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="ln_solid"></div>

                                    <!-- Trạng thái -->
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3">
                                            Trạng thái <span class="required">*</span>
                                        </label>
                                        <div class="col-md-9 col-sm-9">
                                            <select class="form-control @error('status') has-error @enderror" 
                                                    id="status" 
                                                    name="status" 
                                                    required>
                                                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>
                                                    Hoạt động
                                                </option>
                                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>
                                                    Không hoạt động
                                                </option>
                                            </select>
                                            @error('status')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Mô tả -->
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3">Mô tả</label>
                                        <div class="col-md-9 col-sm-9">
                                            <textarea class="form-control @error('description') has-error @enderror" 
                                                      id="description" 
                                                      name="description" 
                                                      rows="4" 
                                                      placeholder="Mô tả chi tiết về mã giảm giá...">{{ old('description') }}</textarea>
                                            @error('description')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="ln_solid"></div>

                                    <!-- Buttons -->
                                    <div class="form-group">
                                        <div class="col-md-9 col-sm-9 col-md-offset-3">
                                            <button type="submit" class="btn btn-success">
                                                <i class="fa fa-save"></i> Tạo mã giảm giá
                                            </button>
                                            <a href="{{ route('admin.coupon.index') }}" class="btn btn-default">
                                                <i class="fa fa-times"></i> Hủy bỏ
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="col-md-4 col-sm-12">
                        <!-- Preview Panel -->
                        <div class="x_panel">
                            <div class="x_title">
                                <h2><i class="fa fa-eye"></i> Xem trước kết quả</h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <div id="preview_result">
                                    <div class="text-center text-muted" style="padding: 30px 0;">
                                        <i class="fa fa-calculator fa-3x"></i>
                                        <p>Nhập thông tin để xem trước</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tips -->
                        <div class="x_panel">
                            <div class="x_title">
                                <h2><i class="fa fa-lightbulb-o"></i> Gợi ý hữu ích</h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <ul class="list-unstyled">
                                    <li class="mb-2"><i class="fa fa-check text-success"></i> Mã ngắn gọn, dễ nhớ</li>
                                    <li class="mb-2"><i class="fa fa-check text-success"></i> Phần trăm cho đơn lớn</li>
                                    <li class="mb-2"><i class="fa fa-check text-success"></i> Đặt giá trị tối thiểu</li>
                                    <li class="mb-2"><i class="fa fa-check text-success"></i> Giới hạn cho flash sale</li>
                                    <li class="mb-2"><i class="fa fa-check text-success"></i> Kiểm tra ngày kỹ</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Quick Templates -->
                        <div class="x_panel">
                            <div class="x_title">
                                <h2><i class="fa fa-bolt"></i> Templates nhanh</h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <button type="button" class="btn btn-primary btn-block btn-sm mb-2" onclick="fillTemplate('flash')">
                                    Flash Sale 50%
                                </button>
                                <button type="button" class="btn btn-success btn-block btn-sm mb-2" onclick="fillTemplate('newuser')">
                                    Người dùng mới -100k
                                </button>
                                <button type="button" class="btn btn-info btn-block btn-sm" onclick="fillTemplate('vip')">
                                    VIP 20% (max 500k)
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /page content -->
    </div>
</div>
<script>
// VÔ HIỆU HÓA DATEPICKER INIT CỦA GENTELELLA
$(document).ready(function() {
    // Override init_daterangepicker function
    if (typeof init_daterangepicker !== 'undefined') {
        var originalInit = init_daterangepicker;
        window.init_daterangepicker = function() {
            // Không làm gì - disable hoàn toàn
        };
    }
    
    // Override $.fn.daterangepicker
    if ($.fn.daterangepicker) {
        var originalDaterangepicker = $.fn.daterangepicker;
        $.fn.daterangepicker = function() {
            // Chỉ disable cho date inputs trong form coupon
            if (this.closest('#couponForm').length > 0) {
                return this;
            }
            return originalDaterangepicker.apply(this, arguments);
        };
    }
});
</script>
@include('admin.blocks.footer')
<script>
// RESET DATE INPUTS SAU KHI FOOTER LOAD
$(window).on('load', function() {
    setTimeout(function() {
        // Reset hoàn toàn cả 2 date inputs
        $('#start_date, #end_date').each(function() {
            var $input = $(this);
            var originalValue = $input.attr('id') === 'start_date' ? '{{ date("Y-m-d") }}' : '';
            
            // Xóa tất cả event handlers
            $input.off();
            
            // Xóa data
            $input.removeData();
            
            // Xóa classes của datepicker
            $input.removeClass('hasDatepicker single_cal1 single_cal2 single_cal3 single_cal4');
            
            // Set lại type
            $input.attr('type', 'date');
            
            // Set value
            $input.val(originalValue);
        });
        
        console.log('Date inputs đã được reset!');
    }, 500);
});
</script>

<script>
$(document).ready(function() {
    // Toggle discount type fields
    $('#discount_type').on('change', function() {
        const type = $(this).val();
        if (type === 'percent') {
            $('#max_discount_group').show();
            $('#discount_value').attr('max', '100');
            $('#discount_value_label').text('Giá trị giảm (%)');
        } else {
            $('#max_discount_group').hide();
            $('#discount_value').removeAttr('max');
            $('#discount_value_label').text('Giá trị giảm (VNĐ)');
        }
    });

    $('#discount_type').trigger('change');

    // Auto generate code
    $('#btn-generate-code').on('click', function() {
        const prefix = $('#code_prefix').val() || 'COUPON';
        const randomStr = Math.random().toString(36).substring(2, 8).toUpperCase();
        const code = prefix + randomStr;
        $('#code').val(code);
    });

    // Preview discount
    function previewDiscount() {
        const discountType = $('#discount_type').val();
        const discountValue = parseFloat($('#discount_value').val()) || 0;
        const minOrderValue = parseFloat($('#min_order_value').val()) || 0;
        const maxDiscount = parseFloat($('#max_discount').val()) || 0;

        if (!discountValue) {
            $('#preview_result').html('<div class="text-center text-muted" style="padding: 30px 0;"><i class="fa fa-calculator fa-3x"></i><p>Nhập thông tin để xem trước</p></div>');
            return;
        }

        let previewHTML = '<div class="alert alert-info"><strong>Ví dụ:</strong><br>';
        const testAmounts = [100000, 500000, 1000000, 5000000];
        
        testAmounts.forEach(amount => {
            if (minOrderValue && amount < minOrderValue) {
                previewHTML += '<div class="text-danger">Đơn ' + amount.toLocaleString() + ' VNĐ: Không đủ điều kiện</div>';
            } else {
                let discount = 0;
                if (discountType === 'percent') {
                    discount = (amount * discountValue) / 100;
                    if (maxDiscount && discount > maxDiscount) {
                        discount = maxDiscount;
                    }
                } else {
                    discount = discountValue;
                }
                
                const finalAmount = amount - discount;
                previewHTML += '<div>Đơn ' + amount.toLocaleString() + ' VNĐ: Giảm <strong>' + discount.toLocaleString() + '</strong> VNĐ → Còn ' + finalAmount.toLocaleString() + ' VNĐ</div>';
            }
        });
        
        previewHTML += '</div>';
        $('#preview_result').html(previewHTML);
    }

    $('#discount_type, #discount_value, #min_order_value, #max_discount').on('input change', previewDiscount);
    previewDiscount();
});

// Template functions
function fillTemplate(type) {
    const today = new Date().toISOString().split('T')[0];
    const nextMonth = new Date();
    nextMonth.setMonth(nextMonth.getMonth() + 1);
    const endDate = nextMonth.toISOString().split('T')[0];

    switch(type) {
        case 'flash':
            $('#code').val('FLASH50');
            $('#discount_type').val('percent').trigger('change');
            $('#discount_value').val('50');
            $('#max_discount').val('500000');
            $('#min_order_value').val('500000');
            $('#usage_limit').val('100');
            $('#start_date').val(today);
            $('#end_date').val(today);
            $('#description').val('Flash Sale giảm 50% tối đa 500k cho đơn từ 500k');
            break;
        case 'newuser':
            $('#code').val('NEWUSER100');
            $('#discount_type').val('fixed').trigger('change');
            $('#discount_value').val('100000');
            $('#min_order_value').val('');
            $('#usage_limit').val('1');
            $('#start_date').val(today);
            $('#end_date').val(endDate);
            $('#description').val('Giảm 100k cho người dùng mới');
            break;
        case 'vip':
            $('#code').val('VIP20');
            $('#discount_type').val('percent').trigger('change');
            $('#discount_value').val('20');
            $('#max_discount').val('500000');
            $('#min_order_value').val('1000000');
            $('#usage_limit').val('');
            $('#start_date').val(today);
            $('#end_date').val(endDate);
            $('#description').val('Mã VIP giảm 20% tối đa 500k');
            break;
    }
    
    new PNotify({
        title: 'Thành công',
        text: 'Đã áp dụng template!',
        type: 'success',
        styling: 'bootstrap3'
    });
}
</script>