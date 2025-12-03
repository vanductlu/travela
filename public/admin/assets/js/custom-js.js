Dropzone.autoDiscover = false;
$(document).ready(function () {

    $("#btn-active").click(function () {
        var button = $(this);
        let dataAttr = button.data("attr");
        let userId = dataAttr.userId;
        let actionUrl = dataAttr.action;
        let formData = {
            userId: userId,
            _token: $('meta[name="csrf-token"]').attr("content"),
        };

        $.ajax({
            type: "POST",
            url: actionUrl,
            data: formData,
            success: function (response) {
                if (response.success) {
                    button.closest(".profile_view").find(".brief i").text("Đã kích hoạt");
                    button.hide();
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

    $("#btn-ban, #btn-delete, #btn-unban, #btn-restore").click(function () {
        var button = $(this);
        let dataAttr = button.data("attr");
        let userId = dataAttr.userId;
        let status = dataAttr.status;
        let actionUrl = dataAttr.action;
        let formData = {
            userId: userId,
            status: status,
            _token: $('meta[name="csrf-token"]').attr("content"),
        };

        $.ajax({
            type: "POST",
            url: actionUrl,
            data: formData,
            success: function (response) {
                if (response.success) {
                    button.closest(".profile_view").find(".brief i").text(response.status);
                    button.parent().find("button").hide();
                    if (status === "b") {
                        button.parent().find("#btn-unban").show();
                    } else if (status === "d") {
                        button.parent().find("#btn-restore").show();
                    } else {
                        button.parent().find("#btn-ban, #btn-delete, #btn-active").show();
                    }
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

    $(document).on("click", ".delete-tour", function (e) {
        e.preventDefault();
        e.stopPropagation();
        
        const $button = $(this);
        const tourId = $button.data("tourid");
        const tourName = $button.data("tourname");
        
        console.log('=== DELETE TOUR CLICKED ===');
        console.log('Tour ID:', tourId);
        console.log('Tour Name:', tourName);
        
        if (!tourId) {
            toastr.error('Không tìm thấy ID tour');
            return;
        }
        
        $.ajax({
            url: '/admin/check-before-delete-tour',
            method: 'GET',
            data: { tourId: tourId },
            success: function(response) {
                console.log('Check response:', response);
                
                if (!response.success) {
                    toastr.error(response.message);
                    return;
                }
                
                const related = response.related;
                const warnings = response.warnings;
                
                let message = `Bạn chắc chắn muốn xóa tour "${tourName}"?\n\n`;
                
                if (warnings.length > 0) {
                    message += `CẢNH BÁO: Dữ liệu sau sẽ bị xóa:\n`;
                    message += warnings.join('\n') + '\n\n';
                }
                
                message += `Chi tiết:\n`;
                message += `• ${related.bookings} booking\n`;
                message += `• ${related.checkouts} checkout\n`;
                message += `• ${related.reviews} đánh giá\n`;
                message += `• ${related.images} ảnh\n`;
                message += `• ${related.timeline} timeline\n\n`;
                message += `KHÔNG THỂ HOÀN TÁC!`;
                
                if (confirm(message)) {
                    deleteTourConfirmed(tourId);
                }
            },
            error: function(xhr) {
                console.error('Check error:', xhr);
                toastr.error('Lỗi kiểm tra tour');
            }
        });
    });

    function deleteTourConfirmed(tourId) {
        $.ajax({
            url: '/admin/delete-tour',
            method: 'POST',
            data: {
                tourId: tourId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                toastr.info('Đang xóa...');
            },
            success: function(response) {
                console.log('Delete response:', response);
                
                if (response.success) {
                    toastr.success(response.message);
                    
                    if (response.data) {
                        $('#tbody-listTours').html(response.data);
                    }
                    
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                console.error('Delete error:', xhr);
                const msg = xhr.responseJSON?.message || 'Lỗi xóa tour';
                toastr.error(msg);
            }
        });
    }

window.init_SmartWizard = function() {
    console.log("SmartWizard default init is disabled on Add Tour page!");
};
    
let timelineCounter = 1;
let maxTimelineDays = null;
var myDropzone = null;

function initCK(id) {
    if (!document.getElementById(id)) {
        console.warn("CKEDITOR: element not found", id);
        return;
    }

    if (CKEDITOR.instances[id]) {
        CKEDITOR.instances[id].destroy(true);
    }
    CKEDITOR.replace(id);
}

if ($("#description").length) initCK("description");

$('#start_date, #end_date').datetimepicker({
    format: 'd/m/Y',
    timepicker: false,
});
$(document).on('dataUpdated', function (event, days) {
    maxTimelineDays = parseInt(days);

    timelineCounter = 1;
    $("#timeline-container").html("");

    addTimelineEntry();

    console.log("Số ngày tour:", maxTimelineDays);
});


function createTimelineCard(id) {
    return `
        <div class="timeline-entry card border rounded p-3 mt-3 shadow-sm" id="timeline-entry-${id}">
            <div class="d-flex justify-content-between align-items-center bg-light p-2 rounded mb-3">
                <h4 class="m-0 text-primary">🗓️ Ngày ${id}</h4>
                ${id === 1 ? "" : `<button class="btn btn-danger btn-sm remove-btn" data-id="${id}">Xóa</button>`}
            </div>

            <label><strong>Tiêu đề ngày</strong></label>
            <input type="text" class="form-control" name="day-${id}" placeholder="Nhập tiêu đề..." required>

            <label class="mt-3"><strong>Lộ trình chi tiết</strong></label>
            <textarea id="itinerary-${id}" name="itinerary-${id}"></textarea>
        </div>
    `;
}

function addTimelineEntry() {

     if (maxTimelineDays && timelineCounter > maxTimelineDays) {
        toastr.error(`Tour chỉ có ${maxTimelineDays} ngày!`);
        return;
    }

    $('#timeline-container').append(createTimelineCard(timelineCounter));

let currentId = timelineCounter; 

setTimeout(() => {
    initCK(`itinerary-${currentId}`);
}, 200);
    timelineCounter++;
    console.log("CK instances:", CKEDITOR.instances);
}

$(document).on("click", "#add-timeline", function () {
    addTimelineEntry();
});

$(document).on('click', '.remove-btn', function () {
    let id = $(this).data('id');

    let instance = CKEDITOR.instances[`itinerary-${id}`];
    if (instance) {
    instance.destroy(true);
    }

    $(`#timeline-entry-${id}`).remove();
});

if ($('#myDropzone').length) {
    try {
        if (Dropzone.forElement('#myDropzone')) Dropzone.forElement('#myDropzone').destroy();
    } catch {}

    myDropzone = new Dropzone('#myDropzone', {
        url: '/admin/add-images-tours',
        method: 'post',
        paramName: 'image',
        acceptedFiles: 'image/*',
        addRemoveLinks: true,
        autoProcessQueue: true,
        parallelUploads: 1,
        maxFiles: 10,
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        init: function () {
            this.on('sending', function (file, xhr, formData) {
                let id = $('.hiddenTourId').val();
                if (!id) {
                    toastr.error('Chưa có Tour ID!');
                    this.removeFile(file);
                    return false;
                }
                formData.append('tourId', id);
            });
        }
    });
}

$(document).on('click', '.buttonFinish', function (e) {
    e.preventDefault();
    for (let key in CKEDITOR.instances) {
        CKEDITOR.instances[key].updateElement();
    }
    let tourId = $('.hiddenTourId').val();
    if (!tourId) {
        toastr.error("Thiếu Tour ID!");
        return;
    }

    let invalid = false;
    let timelineData = [];

    $('.timeline-entry').each(function () {
        let textareaId = $(this).find('textarea').attr('id');
        let title = $(this).find('input').val();
        let editor = CKEDITOR.instances[textareaId];
        let content = editor ? editor.getData() : "";

        if (!title.trim() || !content.trim()) {
            invalid = true;
        }

        timelineData.push({
            day: $(this).attr("id").replace("timeline-entry-", ""),
            title: title,
            content: content
        });
    });

    if (invalid) {
        toastr.error("Vui lòng điền đầy đủ thông tin timeline!");
        return;
    }

    $.ajax({
        url: "/admin/add-timeline",
        type: "POST",
        data: {
            _token: $('meta[name="csrf-token"]').attr("content"),
            tourId: tourId,
            timeline: JSON.stringify(timelineData)
        },
        success: function (res) {
            if (res.success) {
                toastr.success("Hoàn tất thêm Tour!");
            } else {
                toastr.error("Có lỗi khi thêm timeline.");
            }
        },
        error: function () {
            toastr.error("Lỗi server!");
        }
    });
    console.log(timelineData);
});






let timelineCounter_edit = 1;
let formDataEdit = {};
let tourIdSendingImage = null;
let wizardInitialized = false;

let dropzoneOldImages = null;   
let existingImages = [];        
let newImages = [];             
let removedImages = [];         

function getFileTypeFromURL(url) {
    if (!url || typeof url !== 'string') return 'application/octet-stream';
    const ext = url.split('.').pop().toLowerCase();
    const map = {
        jpg: "image/jpeg",
        jpeg: "image/jpeg",
        png: "image/png",
        webp: "image/webp",
        gif: "image/gif",
        bmp: "image/bmp",
        svg: "image/svg+xml",
        tif: "image/tiff",
        tiff: "image/tiff",
    };
    return map[ext] || "image/*";
}

$(document).on("click", ".edit-tour", function () {
    console.log("[EDIT CLICK] Bắt đầu mở Modal Edit Tour");

    const tourId = $(this).data("tourid");
    const urlEdit = $(this).data("urledit");
    console.log("tourId:", tourId, " urlEdit:", urlEdit);

    tourIdSendingImage = tourId;
    $(".hiddenTourId").val(tourId);

    $("#step-3").empty();
    timelineCounter_edit = 1;
    existingImages = [];
    newImages = [];
    removedImages = [];

    if (!wizardInitialized) {
        init_SmartWizard_Edit_Tour();
        wizardInitialized = true;
    }

    $.ajax({
        url: urlEdit,
        method: "GET",
        data: { tourId },
        success: function (res) {
            console.log("[AJAX EDIT] Response:", res);
            if (!res.success) {
                toastr.error(res.message || "Lỗi tải dữ liệu");
                return;
            }

            fillStep1Data(res.tour);
            loadTimeline(res.timeline || []);
            setTimeout(() => loadOldImages(res.images || []), 200);
        },
        error: function (xhr) {
            console.error("AJAX getTourEdit lỗi:", xhr);
            toastr.error("Lỗi tải dữ liệu từ server");
        }
    });
});

function fillStep1Data(tour) {
    console.log("[STEP1] Load dữ liệu:", tour);
    $("#edit-tour-modal input[name='name']").val(tour.title || "");
    $("#edit-tour-modal input[name='destination']").val(tour.destination || "");
    $("#domain").val(tour.domain || "");
    $("#edit-tour-modal input[name='number']").val(tour.quantity || "");
    $("#edit-tour-modal input[name='price_adult']").val(tour.priceAdult || "");
    $("#edit-tour-modal input[name='price_child']").val(tour.priceChild || "");
    $("#start_date").val(moment(tour.startDate).format("DD/MM/YYYY") || "");
    $("#end_date").val(moment(tour.endDate).format("DD/MM/YYYY") || "");

    setTimeout(() => {
        if (CKEDITOR.instances.description) {
            console.log("CKEDITOR - SetData");
            CKEDITOR.instances.description.setData(tour.description || "");
        } else {
            CKEDITOR.replace("description");
            setTimeout(()=> {
                CKEDITOR.instances.description.setData(tour.description || "");
            }, 200);
        }
    }, 100);
}

let step2Visited = false;

function init_SmartWizard_Edit_Tour() {
    console.log("[INIT WIZARD] chạy 1 lần");

    if (!CKEDITOR.instances.description) {
        console.log("CKEDITOR khởi tạo");
        CKEDITOR.replace("description");
    }

    $("#wizard").smartWizard({
        transitionEffect: "fade",
        onLeaveStep: function (obj, ctx) {
            console.log("[CHANGE STEP] From:", ctx.fromStep, "To:", ctx.toStep);
            if (ctx.toStep === 2) step2Visited = true;
            if (ctx.fromStep === 1) return validateStep1();
            if (ctx.fromStep === 2 && ctx.toStep === 3) return validateStep2();
            return true;
        }
    });
}

function validateStep1() {
    let ok = true;
    $("#form-step1 input, #form-step1 select").each(function () {
        if ($(this).prop("required") && !$(this).val()) {
            ok = false;
            toastr.error("Vui lòng nhập đầy đủ thông tin!");
        }
    });

    const desc = CKEDITOR.instances.description ? CKEDITOR.instances.description.getData().trim() : "";
    if (!desc) {
        ok = false;
        toastr.error("Mô tả không được để trống!");
    }

    formDataEdit = {
        tourId: tourIdSendingImage,
        name: $("input[name='name']").val(),
        destination: $("input[name='destination']").val(),
        domain: $("#domain").val(),
        number: $("input[name='number']").val(),
        price_adult: $("input[name='price_adult']").val(),
        price_child: $("input[name='price_child']").val(),
        start_date: $("#start_date").val(),
        end_date: $("#end_date").val(),
        description: desc,
        images: [], timeline: []
    };

    console.log("FORM DATA STEP1 built:", formDataEdit);
    return ok;
}

Dropzone.autoDiscover = false;

function loadOldImages(images) {
    console.log("[LOAD OLD IMAGES] images =", images);

    try {
        if (dropzoneOldImages && dropzoneOldImages.destroy) {
            console.log("Destroying old dropzone instance");
            dropzoneOldImages.destroy();
        }
    } catch (err) {
        console.warn("Destroy dropzone error:", err);
    }
    dropzoneOldImages = null;
    existingImages = [];
    newImages = [];
    removedImages = [];

    dropzoneOldImages = new Dropzone("#myDropzone-editTour", {
        url: "/admin/add-images-tours", 
        method: "POST",
        paramName: "image",
        acceptedFiles: "image/*",
        addRemoveLinks: true,
        autoProcessQueue: true,
        parallelUploads: 2,
        maxFiles: 20,
        params: {
            tourId: tourIdSendingImage 
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
        },
        init: function () {
            const dz = this;
            console.log("Dropzone INIT thành công (edit) - instance:", dz);

            images.forEach((img, idx) => {
                const filename = img.imageUrl || img.imageURL || img.image || img;
                if (!filename) return;

                const mockFile = {
                    name: filename,
                    size: 12345,
                    type: getFileTypeFromURL(filename),
                    accepted: true,
                    status: Dropzone.SUCCESS,
                    isOld: true 
                };
                dz.emit("addedfile", mockFile);
                dz.emit("thumbnail", mockFile, "/clients/assets/images/gallery-tours/" + filename);
                dz.emit("complete", mockFile);
                dz.files.push(mockFile);
                existingImages.push(filename);
                console.log(`Load old image #${idx}:`, filename);
            });
            dz.on("success", function (file, response) {
                console.log("Dropzone success response:", response);
                const filename = (response && (response.data && response.data.filename)) || response.filename || response.file || response.fileName || response.file || null;
                if (filename) {
                    file.isOld = false;
                    file.uploadedFilename = filename;
                    newImages.push(filename);
                    console.log("New image added:", filename);
                } else {
                    console.warn("Dropzone uploaded but server did not return filename:", response);
                }
            });

            dz.on("error", function(file, err) {
                console.error("Dropzone upload error:", err, file);
                toastr.error("Upload ảnh thất bại");
            });

            dz.on("removedfile", function (file) {
                console.log("Dropzone removedfile event:", file);
                if (file.isOld) {
                    removedImages.push(file.name);
                    existingImages = existingImages.filter(f => f !== file.name);
                    console.log("Removed existing image:", file.name);
                } else {
                    const fn = file.uploadedFilename || file.name;
                    newImages = newImages.filter(f => f !== fn);
                    console.log("Removed new image (not saved):", fn);
                }
            });

            console.log("Dropzone ready. existingImages:", existingImages);
        }
    });
}

function validateStep2() {
    if (!step2Visited) {
        console.log("[validateStep2] step2 not visited - skip");
        return true;
    }

    const total = existingImages.length + newImages.length;
    console.log("validateStep2 -> existing:", existingImages.length, "new:", newImages.length, "removed:", removedImages.length);
    if (total === 0) {
        toastr.error("Cần ít nhất 1 ảnh!");
        return false;
    }

    formDataEdit.images = existingImages.concat(newImages);
    console.log("formDataEdit.images prepared:", formDataEdit.images);
    return true;
}

function loadTimeline(list) {
    console.log("loadTimeline:", list);
    list.forEach(item => addTimeline(item));
}

function addTimeline(item) {
    const editorId = "itinerary-edit-" + timelineCounter_edit;
    const title = (item && (item.title || item.day)) || `Ngày ${timelineCounter_edit}`;
    const descr = (item && (item.description || item.itinerary)) || (item && item.content) || "";

    const html = `
        <div class="timeline-entry mb-3" id="timeline-entry-${timelineCounter_edit}">
            <label>Ngày ${timelineCounter_edit}</label>
            <input type="text" class="form-control mb-2" value="${escapeHtml(title)}" name="day-${timelineCounter_edit}">
            <textarea id="${editorId}" name="itinerary-${timelineCounter_edit}">${escapeHtml(descr)}</textarea>
        </div>
    `;
    $("#step-3").append(html);

    setTimeout(() => {

        if (CKEDITOR.instances[editorId]) {
            CKEDITOR.instances[editorId].destroy(true);
        }
        CKEDITOR.replace(editorId);
        console.log("CKEDITOR init for", editorId);
    }, 150);

    timelineCounter_edit++;
}

function escapeHtml(str) {
    if (str === null || str === undefined) return "";
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}


$(".buttonFinishEdit").on("click", function (e) {
    e.preventDefault();
    console.log("[SAVE CLICK] Bắt đầu lưu");

    for (let k in CKEDITOR.instances) {
        if (CKEDITOR.instances.hasOwnProperty(k)) {
            try { CKEDITOR.instances[k].updateElement(); } catch (err) {}
        }
    }

    formDataEdit.timeline = [];
    $(".timeline-entry").each(function () {
        const title = $(this).find("input").val();
        const textareaId = $(this).find("textarea").attr("id");
        const content = CKEDITOR.instances[textareaId] ? CKEDITOR.instances[textareaId].getData() : $(this).find("textarea").val() || "";
        formDataEdit.timeline.push({ title, itinerary: content });
    });

    formDataEdit.tourId = tourIdSendingImage;
    formDataEdit.name = $("#edit-tour-modal input[name='name']").val();
    formDataEdit.destination = $("#edit-tour-modal input[name='destination']").val();
    formDataEdit.domain = $("#domain").val();
    formDataEdit.number = $("#edit-tour-modal input[name='number']").val();
    formDataEdit.price_adult = $("#edit-tour-modal input[name='price_adult']").val();
    formDataEdit.price_child = $("#edit-tour-modal input[name='price_child']").val();
    formDataEdit.description = CKEDITOR.instances.description ? CKEDITOR.instances.description.getData() : "";

    formDataEdit.images = existingImages.concat(newImages);

    console.log("DATA GỬI LÊN:", formDataEdit);

    $.ajax({
        url: "/admin/edit-tour",
        type: "POST",
        data: JSON.stringify(formDataEdit),
        contentType: "application/json",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
        },
        success: function (res) {
            console.log("Response update:", res);
            if (res.success) {
                toastr.success(res.message || "Cập nhật thành công!");
                $("#edit-tour-modal").modal("hide");
                setTimeout(() => location.reload(), 700);
            } else {
                toastr.error(res.message || "Lỗi cập nhật");
            }
        },
        error: function (xhr) {
            console.error("AJAX update error:", xhr);
            toastr.error("Lỗi cập nhật (xem console).");
        }
    });
});




    $(document).on("click", ".confirm-booking", function (e) {
        e.preventDefault();
        const bookingId = $(this).data("bookingid");
        const urlConfirm = $(this).data("urlconfirm");

        $.ajax({
            url: urlConfirm,
            method: "POST",
            data: {
                bookingId: bookingId,
                _token: $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                if (response.success) {
                    $("#tbody-booking").html(response.data);
                    $(".confirm-booking").remove();
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (error) {
                toastr.error("Có lỗi xảy ra.");
            },
        });
    });

    $(document).on("click", ".finish-booking", function (e) {
        e.preventDefault();
        const bookingId = $(this).data("bookingid");
        const urlFinish = $(this).data("urlfinish");

        $.ajax({
            url: urlFinish,
            method: "POST",
            data: {
                bookingId: bookingId,
                _token: $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                if (response.success) {
                    $("#tbody-booking").html(response.data);
                    $(".finish-booking").remove();
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (error) {
                toastr.error("Có lỗi xảy ra.");
            },
        });
    });

    $("#send-pdf-btn").click(function () {
        const bookingId = $(this).data("bookingid");
        const email = $(this).data("email");
        const urlSendPdf = $(this).data("urlsendmail");

        $.ajax({
            url: urlSendPdf,
            type: "POST",
            data: {
                bookingId: bookingId,
                email: email,
                _token: $('meta[name="csrf-token"]').attr("content"),
            },
            beforeSend: function () {
                toastr.warning("Đang gửi mail!!!");
            },
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr, status, error) {
                toastr.error("Lỗi gửi email!");
            },
        });
    });

    $(document).on("click", "#received-money", function (e) {
        e.preventDefault();
        const bookingId = $(this).data("bookingid");
        const urlPaid = $(this).data("urlpaid");

        $.ajax({
            url: urlPaid,
            method: "POST",
            data: {
                bookingId: bookingId,
                _token: $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                if (response.success) {
                    $("#received-money").remove();
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (error) {
                toastr.error("Có lỗi xảy ra.");
            },
        });
    });

    $(".contact-item").click(function (e) {
        e.preventDefault();
        $(".mail_view").show();

        var fullName = $(this).data("name");
        var email = $(this).data("email");
        var message = $(this).data("message");
        var contactId = $(this).data("contactid");

        $(".mail_view .inbox-body .sender-info strong").text(fullName);
        $(".mail_view .inbox-body .sender-info span").text("(" + email + ")");
        $(".mail_view .view-mail p").text(message);
        $(".send-reply-contact").attr("data-email", email);
        $(".send-reply-contact").attr("data-contactid", contactId);
    });

    if ($("#editor-contact").length) {
        CKEDITOR.replace("editor-contact");
    }

    $(document).on("click", ".send-reply-contact", function (e) {
        e.preventDefault();
        var email = $(this).attr("data-email");
        var contactId = $(this).attr("data-contactid");
        var editorContent = CKEDITOR.instances["editor-contact"].getData();
        var urlReply = $(this).data("url");

        if (!email) {
            toastr.error("Không có email.");
            return;
        }

        $.ajax({
            url: urlReply,
            type: "POST",
            dataType: "json",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            data: {
                contactId: contactId,
                email: email,
                message: editorContent,
            },
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message);
                    $(".contact-item[data-contactid='" + contactId + "']").remove();
                    $(".mail_view").hide();
                    CKEDITOR.instances["editor-contact"].setData("");
                    $(".compose").slideToggle();
                }
            },
            error: function (xhr) {
                alert("Lỗi gửi email.");
            },
        });
    });

    $("#formLoginAdmin").on("submit", function (e) {
        const username = $("#username").val();
        const password = $("#password").val();
        const sqlInjectionPattern = /['";=\\-]/;

        if (sqlInjectionPattern.test(username)) {
            toastr.error("Tên tài khoản không hợp lệ!");
            e.preventDefault();
            return false;
        }

        if (sqlInjectionPattern.test(password)) {
            toastr.error("Mật khẩu không hợp lệ!");
            e.preventDefault();
            return false;
        }

        if (password.length < 6) {
            toastr.error("Mật khẩu phải có ít nhất 6 ký tự!");
            e.preventDefault();
            return false;
        }
    });

    $("#formProfileAdmin").on("submit", function (e) {
        e.preventDefault();

        var name = $("#fullName").val().trim();
        var password = $("#password").val().trim();
        var email = $("#email").val().trim();
        var address = $("#address").val().trim();
        var isValid = true;

        if (password === "" || password.length < 6) {
            isValid = false;
            toastr.error("Mật khẩu phải có ít nhất 6 ký tự.");
        }

        var emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
        if (!emailPattern.test(email)) {
            isValid = false;
            toastr.error("Email không hợp lệ.");
        }

        if (address === "") {
            isValid = false;
            toastr.error("Vui lòng nhập địa chỉ.");
        }

        if (isValid) {
            $.ajax({
                url: $(this).attr('action'),
                method: "POST",
                data: {
                    fullName: name,
                    password: password,
                    email: email,
                    address: address,
                    '_token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.success) {
                        toastr.success("Cập nhật thành công!");
                        $('#nameAdmin').text(response.data.fullName);
                        $('#emailAdmin').text(response.data.email);
                        $('#addressAdmin').text(response.data.address);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (xhr, status, error) {
                    toastr.error("Đã có lỗi xảy ra!");
                },
            });
        }
    });

    $("#avatarAdmin").on("change", function () {
        const file = event.target.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                $("#avatarAdminPreview").attr("src", e.target.result);
                $('#navbarDropdown img').attr("src", e.target.result);
                $('.profile_img').attr("src", e.target.result);
            };
            reader.readAsDataURL(file);
            
            var url = $('#btn_avatar').attr('action');
            const formData = new FormData();
            formData.append("avatarAdmin", file);

            $.ajax({
                url: url,
                type: "POST",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content'),
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
                    toastr.error("Có lỗi xảy ra.");
                },
            });
        }
    });
});