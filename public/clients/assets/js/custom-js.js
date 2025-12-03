$(document).ready(function () {

    var sqlInjectionPattern = /[<>'"%;()&+]/;

    $("#sign-up").click(function () {
        $(".sign-in").hide();
        $(".signup").show();
    });
    $("#sign-in").click(function () {
        $(".signup").hide();
        $(".sign-in").show();
    });

    $("#login-form").on("submit", function (e) {
        e.preventDefault();
        var userName = $("#username_login").val().trim();
        var password = $("#password_login").val().trim();
        $("#validate_username").hide().text("");
        $("#validate_password").hide().text("");

        var isValid = true;

        if (password.length < 6) {
            isValid = false;
            $("#validate_password")
                .show()
                .text("Mật khẩu phải có ít nhất 6 ký tự.");
        }
        
        if (sqlInjectionPattern.test(userName)) {
            isValid = false;
            $("#validate_username")
                .show()
                .text("Tên đăng nhập không được chứa ký tự đặc biệt.");
        }

        if (sqlInjectionPattern.test(password)) {
            isValid = false;
            $("#validate_password")
                .show()
                .text("Mật khẩu không được chứa ký tự đặc biệt.");
        }

        if (isValid) {
            var formData = {
                username: userName,
                password: password,
                _token: $('input[name="_token"]').val(),
            };
            console.log(formData, $(this).attr("action"));

            $.ajax({
                type: "POST",
                url: $(this).attr("action"),
                data: formData,
                success: function (response) {
                    if (response.success) {
                        window.location.href = "/";
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (xhr, textStatus, errorThrown) {
                    toastr.error("Có lỗi xảy ra. Vui lòng thử lại sau.");
                },
            });
        }
    });

    $("#register-form").on("submit", function (e) {
        e.preventDefault();
        $(".loader").show();
        $("#register-form").addClass("hidden-content");

        var userName = $("#username_register").val().trim();
        var email = $("#email_register").val().trim();
        var password = $("#password_register").val().trim();
        var rePass = $("#re_pass").val().trim();

        $("#validate_username_regis").hide().text("");
        $("#validate_email_regis").hide().text("");
        $("#validate_password_regis").hide().text("");
        $("#validate_repass").hide().text("");

        var isValid = true;

        if (sqlInjectionPattern.test(userName)) {
            isValid = false;
            $("#validate_username_regis")
                .show()
                .text("Tên tài khoản không được chứa ký tự đặc biệt.");
        }

        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            isValid = false;
            $("#validate_email_regis").show().text("Email không hợp lệ.");
        }

        if (password.length < 6) {
            isValid = false;
            $("#validate_password_regis")
                .show()
                .text("Mật khẩu phải có ít nhất 6 ký tự.");
        }

        if (sqlInjectionPattern.test(password)) {
            isValid = false;
            $("#validate_password_regis")
                .show()
                .text("Mật khẩu không được chứa ký tự đặc biệt.");
        }

        if (password !== rePass) {
            isValid = false;
            $("#validate_repass").show().text("Mật khẩu nhập lại không khớp.");
        }

        if (isValid) {
            var formData = {
                username_regis: userName,
                email: email,
                password_regis: password,
                _token: $('input[name="_token"]').val(),
            };
            console.log(formData, $(this).attr("action"));

            $.ajax({
                type: "POST",
                url: $(this).attr("action"),
                data: formData,
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message, { timeOut: 5000 });
                    } else {
                        toastr.error(response.message);
                    }
                    $("#register-form")
                        .removeClass("hidden-content")
                        .trigger("reset");
                    $(".loader").hide();
                },
                error: function (xhr, textStatus, errorThrown) {
                    toastr.error("Có lỗi xảy ra. Vui lòng thử lại sau.");
                },
            });
        }
    });

    $("#start_date, #end_date").datetimepicker({
        format: "d/m/Y",
        timepicker: false,
    });
    

    $("#userDropdown").click(function () {
        $("#dropdownMenu").toggle(); 
    });

    if ($(".price-slider-range").length) {
        $(".price-slider-range").on("slide", function (event, ui) {
            filterTours(ui.values[0], ui.values[1]);
        });
    }
    $('input[name="domain"]').on("change", filterTours);
    $('input[name="filter_star"]').on("change", filterTours);
    $('input[name="duration"]').on("change", filterTours);

    $("#sorting_tours").on("change", function () {
        filterTours(null, null);
    });

    function filterTours(minPrice = null, maxPrice = null) {
        $(".loader").show();
        $("#tours-container").addClass("hidden-content");

        if (minPrice === null || maxPrice === null) {
            minPrice = $(".price-slider-range").slider("values", 0);
            maxPrice = $(".price-slider-range").slider("values", 1);
        }

        var domain = $('input[name="domain"]:checked').val();
        var star = $('input[name="filter_star"]:checked').val();
        var duration = $('input[name="duration"]:checked').val();
        var sorting = $("#sorting_tours").val();

        formDataFilter = {
            minPrice: minPrice,
            maxPrice: maxPrice,
            domain: domain,
            filter_star: star,
            duration: duration,
            sorting: sorting,
        };
        console.log(formDataFilter);

        $.ajax({
            url: filterToursUrl,
            method: "GET",
            data: formDataFilter,
            success: function (res) {
                $("#tours-container").html(res).removeClass("hidden-content");
                $("#tours-container .destination-item").addClass("aos-animate");
                $(".loader").hide();
            },
        });
    }

    $(document).on("click", ".pagination-tours a", function (e) {
        e.preventDefault();
        $(".loader").show();
        $("#tours-container").addClass("hidden-content");

        var url = $(this).attr("href");
        console.log(url);

        $.ajax({
            url: url,
            type: "GET",
            dataType: "json",
            success: function (response) {
                $("#tours-container")
                    .html(response.tours)
                    .removeClass("hidden-content");
                $("#tours-container .destination-item").addClass("aos-animate");
                $("#tours-container .pagination-tours").addClass("aos-animate");
                $(".loader").hide();
            },
            error: function (xhr, status, error) {
                console.log("Có lỗi xảy ra trong quá trình tải dữ liệu!");
            },
        });
    });

    $(".clear_filter a").on("click", function (e) {
        e.preventDefault();
        $(".loader").show();
        $("#tours-container").addClass("hidden-content");
        $(".price-slider-range").slider("values", [0, 20000000]);

        $('input[name="domain"]').prop("checked", false);
        $('input[name="filter_star"]').prop("checked", false);
        $('input[name="duration"]').prop("checked", false);

        
        var url = $(this).attr("href");

        $.ajax({
            url: url,
            type: "GET",
            dataType: "json",
            headers: {
            'X-Requested-With': 'XMLHttpRequest'  
          },
            success: function (response) {
                $("#tours-container")
                    .html(response.tours)
                    .removeClass("hidden-content");
                $("#tours-container .destination-item").addClass("aos-animate");
                $("#tours-container .pagination-tours").addClass("aos-animate");
                $(".loader").hide();
            },
            error: function (xhr, status, error) {
                console.log("Có lỗi xảy ra trong quá trình tải dữ liệu!");
            },
        });
    });

    $(".updateUser").on("submit", function (e) {
        e.preventDefault();
        var fullName = $("#inputFullName").val();
        var address = $("#inputLocation").val();
        var email = $("#inputEmailAddress").val();
        var phone = $("#inputPhone").val();

        var dataUpdate = {
            fullName: fullName,
            address: address,
            email: email,
            phone: phone,
            _token: $('input[name="_token"]').val(),
        };

        console.log(dataUpdate);

        $.ajax({
            type: "POST",
            url: $(this).attr("action"),
            data: dataUpdate,
            success: function (response) {
                console.log(response);

                if (response.success) {
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr, textStatus, errorThrown) {
                toastr.error("Có lỗi xảy ra. Vui lòng thử lại sau.");
            },
        });
    });

    $("#update_password_profile").click(function () {
        $("#card_change_password").toggle();
    });

    $(".change_password_profile").on("submit", function (e) {
        e.preventDefault();
        var oldPass = $("#inputOldPass").val();
        var newPass = $("#inputNewPass").val();
        var isValid = true;

        if (oldPass.length < 6 || newPass.length < 6) {
            isValid = false;
            $("#validate_password")
                .show()
                .text("Mật khẩu phải có ít nhất 6 ký tự.");
        }

        if (sqlInjectionPattern.test(newPass)) {
            isValid = false;
            $("#validate_password")
                .show()
                .text("Mật khẩu không được chứa ký tự đặc biệt.");
        }

        if (isValid) {
            $("#validate_password").hide().text("");
            var updatePass = {
                oldPass: oldPass,
                newPass: newPass,
                _token: $('input[name="_token"]').val(),
            };

            console.log(updatePass);

            $.ajax({
                type: "POST",
                url: $(this).attr("action"),
                data: updatePass,
                success: function (response) {
                    if (response.success) {
                        $("#validate_password").hide().text("");
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (xhr, textStatus, errorThrown) {
                    $("#validate_password")
                        .show()
                        .text(xhr.responseJSON.message);
                    toastr.error(xhr.responseJSON.message);
                },
            });
        }
    });

    $("#avatar").on("change", function () {
        const file = event.target.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                $("#avatarPreview").attr("src", e.target.result);
                $(".img-account-profile").attr("src", e.target.result);
            };
            reader.readAsDataURL(file);
            var __token = $(this)
                .closest(".card-body")
                .find("input.__token")
                .val();
            var url_avatar = $(this)
                .closest(".card-body")
                .find("input.label_avatar")
                .val();
            const formData = new FormData();
            formData.append("avatar", file);

            console.log(url_avatar);
            $.ajax({
                url: url_avatar,
                type: "POST",
                headers: {
                    "X-CSRF-TOKEN": __token,
                },
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (xhr, status, error) {
                    toastr.error("Có lỗi xảy ra. Vui lòng thử lại sau.");
                },
            });
        }
    });

let discount = 0;
let totalPrice = 0;
let appliedCoupon = null;

function updateSummary() {
  const numAdults = parseInt($("#numAdults").val()) || 0;
  const numChildren = parseInt($("#numChildren").val()) || 0;
  const adultPrice = parseInt($("#numAdults").data("price-adults")) || 0;
  const childPrice = parseInt($("#numChildren").data("price-children")) || 0;

  const adultsTotal = numAdults * adultPrice;
  const childrenTotal = numChildren * childPrice;
  const subtotal = adultsTotal + childrenTotal;

  if (appliedCoupon) {
    if (appliedCoupon.discount_type === 'percent') {
      discount = Math.round((subtotal * appliedCoupon.discount_value) / 100);
      if (appliedCoupon.max_discount && discount > appliedCoupon.max_discount) {
        discount = appliedCoupon.max_discount;
      }
    } else {
      discount = appliedCoupon.discount_amount || appliedCoupon.discount_value || 0;
    }
  } else {
    discount = 0;
  }

  totalPrice = Math.max(0, subtotal - discount);

  $(".quantity__adults").text(numAdults);
  $(".quantity__children").text(numChildren);

  $(".summary-item:nth-child(1) .total-price").text(adultPrice.toLocaleString() + " VNĐ");
  $(".summary-item:nth-child(2) .total-price").text(childPrice.toLocaleString() + " VNĐ");
  $(".summary-item:nth-child(3) .total-price").text(discount.toLocaleString() + " VNĐ");
  $(".summary-item.total-price span:last").text(totalPrice.toLocaleString() + " VNĐ");

  updateHiddenInput('original_price', 'originalPrice', subtotal);
  updateHiddenInput('total_price', 'totalPrice', totalPrice);

  if (appliedCoupon) {
    updateHiddenInput('applied_coupon_code', 'couponCode', appliedCoupon.coupon_code);
  } else {
    $('#applied_coupon_code').remove();
  }
}

function updateHiddenInput(id, name, value) {
  if ($('#' + id).length === 0) {
    $('<input>').attr({
      type: 'hidden',
      id: id,
      name: name,
      value: value
    }).appendTo('.booking-container');
  } else {
    $('#' + id).val(value);
  }
}

$(".quantity-selector").on("click", ".quantity-btn", function () {
  const input = $(this).siblings("input");
  const min = parseInt(input.attr("min")) || 0;
  let value = parseInt(input.val()) || 0;

  const quantityAvailable = parseInt($(".quantityAvailable").text().match(/\d+/)[0]) || 0;
  const totalAdults = parseInt($("#numAdults").val()) || 0;
  const totalChildren = parseInt($("#numChildren").val()) || 0;

  if ($(this).text().trim() === "+") {
    if (input.attr("id") === "numAdults") {
      if (totalAdults + totalChildren < quantityAvailable) {
        value++;
      } else {
        toastr.error("Không thể thêm số người lớn vượt quá số chỗ còn nhận!");
      }
    } else if (input.attr("id") === "numChildren") {
      if (totalAdults + totalChildren < quantityAvailable) {
        value++;
      } else {
        toastr.error("Không thể thêm số trẻ em vượt quá số chỗ còn nhận!");
      }
    }
  } else {
    if (value > min) value--;
  }

  input.val(value);
  updateSummary();
});

$(".btn-coupon").on("click", function (e) {
  e.preventDefault();
  const couponCode = $(".order-coupon input").val().trim();
  
  if (!couponCode) {
    toastr.error("Vui lòng nhập mã giảm giá!");
    return;
  }

  const numAdults = parseInt($("#numAdults").val()) || 0;
  const numChildren = parseInt($("#numChildren").val()) || 0;
  const adultPrice = parseInt($("#numAdults").data("price-adults")) || 0;
  const childPrice = parseInt($("#numChildren").data("price-children")) || 0;
  const subtotal = (numAdults * adultPrice) + (numChildren * childPrice);

  $.ajax({
    url: '/tour/apply-coupon',
    method: 'POST',
    data: {
      coupon_code: couponCode,
      order_total: subtotal,
      _token: $('meta[name="csrf-token"]').attr('content')
    },
    beforeSend: function () {
      $(".btn-coupon").prop('disabled', true).text('Đang kiểm tra...');
    },
    success: function (response) {
      if (response.success) {
        appliedCoupon = response.data;
        discount = response.data.discount_amount || 0;

        $(".order-coupon input").prop('disabled', true);
        $(".btn-coupon").prop('disabled', true)
          .removeClass('btn-primary')
          .addClass('btn-success')
          .html('<i class="fas fa-check"></i> Đã áp dụng');

        if ($('.btn-remove-coupon').length === 0) {
          $(".order-coupon").append(
            '<button type="button" class="btn-remove-coupon" style="width: 15%; margin-left: 5px; background: #dc3545; color: white; border: none; padding: 10px; cursor: pointer; border-radius: 4px;"><i class="fas fa-times"></i></button>'
          );
        }

        toastr.success(response.message + ' - ' + response.data.save_text);
        updateSummary();
      } else {
        appliedCoupon = null;
        discount = 0;
        toastr.error(response.message || "Mã giảm giá không hợp lệ.");
        updateSummary();
      }
    },
    error: function (xhr) {
      appliedCoupon = null;
      discount = 0;
      const errorMsg = xhr.responseJSON?.message || "Có lỗi xảy ra khi kiểm tra mã giảm giá!";
      toastr.error(errorMsg);
      updateSummary();
    },
    complete: function () {
      if (!appliedCoupon) {
        $(".btn-coupon").prop('disabled', false).text('Áp dụng');
      }
    }
  });
});

$(document).on('click', '.btn-remove-coupon', function () {
  appliedCoupon = null;
  discount = 0;
  $(".order-coupon input").val('').prop('disabled', false);
  $(".btn-coupon").prop('disabled', false)
    .removeClass('btn-success')
    .addClass('btn-primary')
    .text('Áp dụng');
  $('.btn-remove-coupon').remove();
  $('#applied_coupon_code').remove();
  toastr.info("Đã xóa mã giảm giá");
  updateSummary();
});

$("#agree").on("change", function () {
  toggleButtonState();
});

function toggleButtonState() {
  if ($("#agree").is(":checked")) {
    $(".btn-submit-booking")
      .removeClass("inactive")
      .css("pointer-events", "auto");
  } else {
    $(".btn-submit-booking")
      .addClass("inactive")
      .css("pointer-events", "none");
  }
}

function validateBookingForm() {
  let isValid = true;
  $(".error-message").hide();

  const username = $("#username").val().trim();
  if (username === "") {
    $("#usernameError").text("Họ và tên không được để trống").show();
    isValid = false;
  }

  const email = $("#email").val().trim();
  const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$/;
  if (email === "") {
    $("#emailError").text("Email không được để trống").show();
    isValid = false;
  } else if (!emailPattern.test(email)) {
    $("#emailError").text("Email không đúng định dạng").show();
    isValid = false;
  }

  const tel = $("#tel").val().trim();
  const telPattern = /^[0-9]{10,11}$/;
  if (tel === "") {
    $("#telError").text("Số điện thoại không được để trống").show();
    isValid = false;
  } else if (!telPattern.test(tel)) {
    $("#telError").text("Số điện thoại phải có 10-11 chữ số").show();
    isValid = false;
  }

  const address = $("#address").val().trim();
  if (address === "") {
    $("#addressError").text("Địa chỉ không được để trống").show();
    isValid = false;
  }

  const paymentMethod = $("input[name='payment']:checked").val();
  if (!paymentMethod) {
    toastr.error("Vui lòng chọn phương thức thanh toán.");
    isValid = false;
  }

  const numAdults = parseInt($("#numAdults").val()) || 0;
  const numChildren = parseInt($("#numChildren").val()) || 0;
  if (numAdults === 0 && numChildren === 0) {
    toastr.error("Vui lòng chọn ít nhất 1 người!");
    isValid = false;
  }

  return isValid;
}

$(".btn-submit-booking").on("click", function (e) {
  e.preventDefault();
  if (validateBookingForm()) {
    $(".booking-container").submit();
  }
});

$('input[name="payment"]').change(function () {
  const paymentMethod = $(this).val();
  $("#payment_hidden").val(paymentMethod);

  const isOnline = (paymentMethod === "momo-payment");
  $(".btn-submit-booking").toggle(!isOnline);

  if (paymentMethod === "momo-payment") {
    $("#btn-momo-payment").show();
  } else {
    $("#btn-momo-payment").hide();
  }
});

$("#btn-momo-payment").click(function (e) {
  e.preventDefault();
  
  if (!validateBookingForm()) return;

  var urlMomo = $(this).data("urlmomo");
  const token = $('input[name="_token"]').val() || $('meta[name="csrf-token"]').attr('content');

  var bookingData = {
    fullName: $("#username").val(),
    email: $("#email").val(),
    tel: $("#tel").val(),
    address: $("#address").val(),
    numAdults: $("#numAdults").val(),
    numChildren: $("#numChildren").val(),
    totalPrice: $("#total_price").val(),
    originalPrice: $("#original_price").val(),
    couponCode: appliedCoupon ? appliedCoupon.coupon_code : '',
    payment_hidden: $("#payment_hidden").val(),
    tourId: $("input[name='tourId']").val(),
    _token: token
  };

  $.ajax({
    url: urlMomo,
    method: "POST",
    data: bookingData,
    success: function (response) {
      if (response && response.payUrl) {
        window.location.href = response.payUrl;
      } else {
        toastr.error("Không thể tạo thanh toán Momo.");
      }
    },
    error: function (xhr) {
      const errorMsg = xhr.responseJSON?.message || "Có lỗi xảy ra khi kết nối đến Momo.";
      toastr.error(errorMsg);
    }
  });
});

const savedData = localStorage.getItem("bookingData");
if (savedData) {
  try {
    const bookingData = JSON.parse(savedData);
    $("#username").val(bookingData.fullName);
    $("#email").val(bookingData.email);
    $("#tel").val(bookingData.tel);
    $("#address").val(bookingData.address);
    $("#numAdults").val(bookingData.numAdults);
    $("#numChildren").val(bookingData.numChildren);
    $("input[name='payment'][value='" + bookingData.payment + "']").prop("checked", true);
    $("#payment_hidden").val(bookingData.payment);
    $("#agree").prop("checked", true);
  } catch (e) {
    console.error('Error parsing booking data:', e);
  }
  localStorage.removeItem("bookingData");
  updateSummary();
}

updateSummary();
toggleButtonState();


    let currentRating = 0;

    $("#rating-stars i").on("mouseover", function () {
        let rating = $(this).data("value");
        highlightStars(rating);
    });

    $("#rating-stars i").on("click", function () {
        currentRating = $(this).data("value");
        console.log("Sao đã chọn :", currentRating);
    });

    $("#rating-stars i").on("mouseout", function () {
        resetStars();
        if (currentRating > 0) {
            highlightStars(currentRating);
        }
    });

    function highlightStars(rating) {
        $("#rating-stars i").each(function () {
            if ($(this).data("value") <= rating) {
                $(this).removeClass("far").addClass("fas active");
            } else {
                $(this).removeClass("fas active").addClass("far");
            }
        });
    }

    function resetStars() {
        $("#rating-stars i").each(function () {
            $(this).removeClass("fas active").addClass("far");
        });
    }
    let urlCheckBooking = $("#submit-reviews").attr("data-url-checkBooking");
    let urlSubmitReview = $("#comment-form").attr("action");
    let tourIdReview = $("#submit-reviews").attr("data-tourId-reviews");

    $("#comment-form").on("submit", function (e) {
        e.preventDefault();

        let message = $("#message").val().trim();

        if (currentRating === 0) {
            toastr.warning("Vui lòng chọn số sao để đánh giá.");
            return;
        } else if (message === "") {
            toastr.warning("Vui lòng nhập nội dung phản hồi.");
            return;
        }

        $.ajax({
            url: urlCheckBooking,
            method: "POST",
            data: {
                tourId: tourIdReview,
                _token: $('input[name="_token"]').val(),
            },
            success: function (response) {
                if (response.success) {
                    formReviews = {
                        tourId: tourIdReview,
                        rating: currentRating,
                        message: message,
                        _token: $('input[name="_token"]').val(),
                    };

                    $.ajax({
                        url: urlSubmitReview, 
                        method: "POST",
                        data: formReviews,
                        success: function (response) {
                            if (response.success) {
                                toastr.success(response.message);
                                $("#partials_reviews").html(response.data);
                                $("#partials_reviews .comment-body").addClass(
                                    "aos-animate"
                                );
                                $("#message").val("");
                                $('#comment-form').hide();
                                resetStars();
                                currentRating = 0;
                            }
                        },
                        error: function (xhr, status, error) {
                            toastr.error("Đã có lỗi xảy ra. Vui lòng thử lại.");
                            console.error("Error:", error);
                        },
                    });
                } else {
                    toastr.error(
                        "Vui lòng đặt tour và trải nghiệm để có thể đánh giá!"
                    );
                }
            },
            error: function (xhr, status, error) {
                toastr.error("Đã có lỗi xảy ra. Vui lòng thử lại.");
                console.error("Error:", error);
            },
        });
    });

    $(document).ready(function() {
    var sqlInjectionPattern = /(\b(SELECT|INSERT|UPDATE|DELETE|DROP|CREATE|ALTER|EXEC|EXECUTE|UNION|OR|AND)\b|--|\/\*|\*\/|;|'|"|<|>)/i;

    $('#contactForm').submit(function (event) {
        $('.error').remove();
        
        var name = $('#name').val().trim();
        var phoneNumber = $('#phone_number').val().trim();
        var email = $('#email').val().trim();
        var message = $('#message').val().trim();
        
        var hasError = false;

        if (name === '') {
            $('#name').after('<span class="error" style="color: red; font-size: 13px; display: block; margin-top: 5px;">Vui lòng nhập họ tên.</span>');
            hasError = true;
        } else if (sqlInjectionPattern.test(name)) {
            $('#name').after('<span class="error" style="color: red; font-size: 13px; display: block; margin-top: 5px;">Tên không hợp lệ. Vui lòng không nhập ký tự đặc biệt.</span>');
            hasError = true;
        }

        if (phoneNumber === '') {
            $('#phone_number').after('<span class="error" style="color: red; font-size: 13px; display: block; margin-top: 5px;">Vui lòng nhập số điện thoại.</span>');
            hasError = true;
        } else if (sqlInjectionPattern.test(phoneNumber)) {
            $('#phone_number').after('<span class="error" style="color: red; font-size: 13px; display: block; margin-top: 5px;">Số điện thoại không hợp lệ.</span>');
            hasError = true;
        } else if (!/^[0-9]{10,11}$/.test(phoneNumber)) {
            $('#phone_number').after('<span class="error" style="color: red; font-size: 13px; display: block; margin-top: 5px;">Số điện thoại phải có 10-11 chữ số.</span>');
            hasError = true;
        }

        if (email === '') {
            $('#email').after('<span class="error" style="color: red; font-size: 13px; display: block; margin-top: 5px;">Vui lòng nhập email.</span>');
            hasError = true;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            $('#email').after('<span class="error" style="color: red; font-size: 13px; display: block; margin-top: 5px;">Email không đúng định dạng.</span>');
            hasError = true;
        }

        if (message === '') {
            $('#message').after('<span class="error" style="color: red; font-size: 13px; display: block; margin-top: 5px;">Vui lòng nhập nội dung.</span>');
            hasError = true;
        } else if (sqlInjectionPattern.test(message)) {
            $('#message').after('<span class="error" style="color: red; font-size: 13px; display: block; margin-top: 5px;">Nội dung không hợp lệ.</span>');
            hasError = true;
        }

        if (hasError) {
            event.preventDefault();
            return false;
        }

        return true;
    });
});


$('#search_form').on('submit', function(event) {
    var destination = $('#destination').val().trim();
    var startDate = $('#start_date').val().trim();
    var endDate = $('#end_date').val().trim();

    $('.error').remove();

    if (destination === "") {
        event.preventDefault();
        $('#destination').after('<span class="error" style="color:red;">Vui lòng nhập điểm đến.</span>');
        return false;
    }

    if (sqlInjectionPattern.test(destination)) {
        event.preventDefault();
        $('#destination').after('<span class="error" style="color:red;">Không được chứa ký tự đặc biệt.</span>');
        return false;
    }

    if (startDate && endDate) {
        const start = new Date(startDate.split("/").reverse().join("-"));
        const end = new Date(endDate.split("/").reverse().join("-"));
        if (start > end) {
            event.preventDefault();
            $('#end_date').after('<span class="error" style="color:red;">Ngày kết thúc phải sau ngày bắt đầu.</span>');
            return false;
        }
    }
});



     if ('SpeechRecognition' in window || 'webkitSpeechRecognition' in window) {
        var recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
        recognition.lang = 'vi-VN'; 
        recognition.continuous = true; 
        recognition.interimResults = true; 
    
        var isRecognizing = false;
    
        $('#voice-search').on('click', function() {
            if (isRecognizing) {
                recognition.stop(); 
                $(this).removeClass('fa-microphone-slash').addClass('fa-microphone'); 
            } else {
                recognition.start(); 
                $(this).removeClass('fa-microphone').addClass('fa-microphone-slash'); 
            }
        });
    
        recognition.onstart = function() {
            console.log('Speech recognition started');
            isRecognizing = true; 
            $('#voice-search').removeClass('fa-microphone').addClass('fa-microphone-slash'); 
        };
    
        recognition.onresult = function(event) {
            var transcript = event.results[0][0].transcript; 
            if (event.results[0].isFinal) {
                $('input[name="keyword"]').val(transcript);
            } else {
                $('input[name="keyword"]').val(transcript);
            }
        };
    
        recognition.onerror = function(event) {
            console.log('Speech recognition error', event.error);
            toastr.error('Có lỗi xảy ra khi nhận diện giọng nói: ' + event.error);
        };
    
        recognition.onend = function() {
            console.log('Speech recognition ended');
            $('#voice-search').removeClass('fa-microphone-slash').addClass('fa-microphone'); 
            isRecognizing = false; 
        };
    } else {
        console.log('Speech recognition not supported in this browser.');
        toastr.error('Trình duyệt của bạn không hỗ trợ nhận diện giọng nói.');
    }
});
