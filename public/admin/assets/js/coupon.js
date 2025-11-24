// Admin Coupon Management JavaScript

$(document).ready(function() {
    // Initialize DataTable
    if ($('#couponTable').length) {
        $('#couponTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/vi.json'
            },
            order: [[0, 'desc']],
            columnDefs: [
                { orderable: false, targets: -1 }
            ]
        });
    }

    // Toggle discount type fields
    $('#discount_type').on('change', function() {
        const type = $(this).val();
        if (type === 'percent') {
            $('#max_discount_group').show();
            $('#discount_value').attr('max', '100');
            $('#discount_value_label').text('Phần trăm giảm (%)');
        } else {
            $('#max_discount_group').hide();
            $('#discount_value').removeAttr('max');
            $('#discount_value_label').text('Số tiền giảm (VNĐ)');
        }
    });

    // Trigger on page load
    $('#discount_type').trigger('change');

    // Preview discount
    function previewDiscount() {
        const discountType = $('#discount_type').val();
        const discountValue = parseFloat($('#discount_value').val()) || 0;
        const minOrderValue = parseFloat($('#min_order_value').val()) || 0;
        const maxDiscount = parseFloat($('#max_discount').val()) || 0;

        if (!discountValue) {
            $('#preview_result').html('');
            return;
        }

        let previewHTML = '<div class="alert alert-info mt-3"><strong>Ví dụ:</strong><br>';
        
        const testAmounts = [100000, 500000, 1000000, 5000000];
        
        testAmounts.forEach(amount => {
            if (minOrderValue && amount < minOrderValue) {
                previewHTML += `<div>Đơn ${amount.toLocaleString()} VNĐ: <span class="text-danger">Không đủ điều kiện</span></div>`;
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
                previewHTML += `<div>Đơn ${amount.toLocaleString()} VNĐ: Giảm <strong>${discount.toLocaleString()}</strong> VNĐ → Còn ${finalAmount.toLocaleString()} VNĐ</div>`;
            }
        });
        
        previewHTML += '</div>';
        $('#preview_result').html(previewHTML);
    }

    // Auto preview on input change
    $('#discount_type, #discount_value, #min_order_value, #max_discount').on('input change', function() {
        previewDiscount();
    });

    // Initial preview
    previewDiscount();

    // Delete coupon
    $(document).on('click', '.btn-delete-coupon', function(e) {
        e.preventDefault();
        const couponId = $(this).data('id');
        const couponCode = $(this).data('code');
        
        if (!confirm(`Bạn có chắc chắn muốn xóa mã giảm giá "${couponCode}"?`)) {
            return;
        }

        $.ajax({
            url: `/admin/coupon/${couponId}`,
            method: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message || 'Xóa mã giảm giá thành công');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    toastr.error(response.message || 'Không thể xóa mã giảm giá');
                }
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON?.message || 'Có lỗi xảy ra khi xóa mã giảm giá';
                toastr.error(errorMsg);
            }
        });
    });

    // Toggle status
    $(document).on('click', '.btn-toggle-status', function(e) {
        e.preventDefault();
        const couponId = $(this).data('id');
        const $btn = $(this);
        
        $.ajax({
            url: `/admin/coupon/${couponId}/toggle-status`,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    
                    // Update button
                    if (response.status === 'active') {
                        $btn.removeClass('btn-secondary').addClass('btn-success')
                            .html('<i class="fas fa-check"></i> Hoạt động');
                    } else {
                        $btn.removeClass('btn-success').addClass('btn-secondary')
                            .html('<i class="fas fa-times"></i> Không hoạt động');
                    }
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                toastr.error('Có lỗi xảy ra khi cập nhật trạng thái');
            }
        });
    });

    // Auto generate coupon code
    $('#btn-generate-code').on('click', function() {
        const prefix = $('#code_prefix').val() || 'COUPON';
        const randomStr = Math.random().toString(36).substring(2, 8).toUpperCase();
        const code = `${prefix}${randomStr}`;
        $('#code').val(code);
    });

    // Copy coupon code
    $(document).on('click', '.btn-copy-code', function() {
        const code = $(this).data('code');
        const tempInput = $('<input>');
        $('body').append(tempInput);
        tempInput.val(code).select();
        document.execCommand('copy');
        tempInput.remove();
        toastr.success('Đã copy mã: ' + code);
    });

    // Filter coupons
    $('#filter_status').on('change', function() {
        const status = $(this).val();
        const table = $('#couponTable').DataTable();
        
        if (status) {
            table.column(5).search(status).draw();
        } else {
            table.column(5).search('').draw();
        }
    });

    // Validate form before submit
    $('#couponForm').on('submit', function(e) {
        let isValid = true;
        
        // Validate discount value
        const discountType = $('#discount_type').val();
        const discountValue = parseFloat($('#discount_value').val());
        
        if (discountType === 'percent' && discountValue > 100) {
            toastr.error('Giá trị giảm giá phần trăm không được vượt quá 100%');
            isValid = false;
        }
        
        if (discountValue <= 0) {
            toastr.error('Giá trị giảm giá phải lớn hơn 0');
            isValid = false;
        }
        
        // Validate dates
        const startDate = new Date($('#start_date').val());
        const endDate = new Date($('#end_date').val());
        
        if (endDate < startDate) {
            toastr.error('Ngày kết thúc phải sau ngày bắt đầu');
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
        }
    });

    // Format currency inputs
    $('.currency-input').on('blur', function() {
        const value = parseFloat($(this).val());
        if (!isNaN(value)) {
            $(this).val(value.toFixed(0));
        }
    });

    // Statistics animation
    if ($('.stat-number').length) {
        $('.stat-number').each(function() {
            const $this = $(this);
            const countTo = parseInt($this.text());
            
            $({ countNum: 0 }).animate({
                countNum: countTo
            }, {
                duration: 1000,
                easing: 'swing',
                step: function() {
                    $this.text(Math.floor(this.countNum).toLocaleString());
                },
                complete: function() {
                    $this.text(countTo.toLocaleString());
                }
            });
        });
    }

    // Export coupons
    $('#btn-export-coupons').on('click', function() {
        const table = $('#couponTable').DataTable();
        const data = table.rows({ search: 'applied' }).data();
        
        let csv = 'Mã,Loại,Giá trị,Đơn tối thiểu,Giảm tối đa,Giới hạn,Đã dùng,Bắt đầu,Kết thúc,Trạng thái\n';
        
        data.each(function(row) {
            csv += `${row[1]},${row[2]},${row[3]},${row[4]},${row[5]},${row[6]},${row[7]},${row[8]},${row[9]},${row[10]}\n`;
        });
        
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `coupons_${new Date().getTime()}.csv`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
        
        toastr.success('Xuất file thành công');
    });
});