Dropzone.autoDiscover = false;
$(document).ready(function () {
    /********************************************
     * USER MANAGEMENT                          *
     ********************************************/
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

    /********************************************
     * ✅ XÓA TOUR                              *
     ********************************************/
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
                    message += `⚠️ CẢNH BÁO: Dữ liệu sau sẽ bị xóa:\n`;
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
    
    /********************************************
 * THÊM TOUR - HOÀN CHỈNH                *
 ********************************************/
let timelineCounter = 1;
let maxTimelineDays = null;
var myDropzone = null;

// ===================================
// 2. INIT CKEDITOR
// ===================================
if ($(".add-tours #description").length) {
    CKEDITOR.replace("description");
    console.log('✅ CKEDITOR init: description');
}

// ===================================
// 3. INIT DATEPICKER
// ===================================
$("#start_date, #end_date").datetimepicker({
    format: "d/m/Y",
    timepicker: false,
});

// ===================================
// 4. EVENT LISTENER CHO maxTimelineDays
// ===================================
$(document).on("dataUpdated", function (event, daysDifference) {
    maxTimelineDays = daysDifference;
    console.log('📅 maxTimelineDays updated:', maxTimelineDays);
});

// ===================================
// 5. ĐỊNH NGHĨA TIMELINE FUNCTIONS
// ===================================
function addTimelineEntry() {
    console.log('➕ Adding timeline entry. Counter:', timelineCounter, 'Max:', maxTimelineDays);
    
    if (maxTimelineDays && timelineCounter > maxTimelineDays) {
        toastr.error(`Không thể thêm quá ${maxTimelineDays} ngày.`);
        return;
    }
    
    const timelineEntry = `
        <div class="timeline-entry" id="timeline-entry-${timelineCounter}">
            <label for="day-${timelineCounter}">Ngày ${timelineCounter}</label>
            <input type="text" class="form-control" id="day-${timelineCounter}" 
                   name="day-${timelineCounter}" placeholder="Ngày thứ..." required>
            
            <label for="itinerary-${timelineCounter}" style="margin-top: 10px; display: block;">Lộ trình:</label>
            <textarea id="itinerary-${timelineCounter}" name="itinerary-${timelineCounter}" required></textarea>
            
            <button type="button" class="btn btn-round btn-danger remove-btn" data-id="${timelineCounter}">
                Xóa Timeline này
            </button>
        </div>
    `;

    $(".add-tours #step-3").append(timelineEntry);

    if ($(`#itinerary-${timelineCounter}`).length) {
        try {
            CKEDITOR.replace(`itinerary-${timelineCounter}`);
            console.log(`✅ CKEDITOR init: itinerary-${timelineCounter}`);
        } catch (e) {
            console.error('CKEDITOR error:', e);
        }
    }

    timelineCounter++;
}

// ===================================
// 6. INIT SMARTWIZARD v3.3.1 - CHỈ CHO ADD-TOURS
// ===================================
if ($(".add-tours #wizard").length && !$(".wizard-edit-tour").length) {
    $("#wizard").smartWizard({
        selected: 0,
        keyNavigation: false,
        enableAllSteps: false,
        transitionEffect: 'fade',
        cycleSteps: false,
        enableFinishButton: false,
        labelNext: 'Tiếp theo',
        labelPrevious: 'Quay lại',
        labelFinish: 'Hoàn thành'
    });
    
    console.log('✅ SmartWizard v3.3.1 initialized');

    // ✅ BIND NÚT NEXT (CHỈ 1 LẦN DUY NHẤT)
    $(document).off('click', '.buttonNext').on('click', '.buttonNext', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var currentStep = $('#wizard').smartWizard('currentStep');
        console.log('🔘 Next button clicked! Current step:', currentStep);
        
        // STEP 1: Validate và tạo tour
        if (currentStep === 1) {
            console.log('📝 Validating step 1...');
            var isValid = true;
            
            $("#form-step1 input[required], #form-step1 select[required]").each(function() {
                if (!$(this).val()) {
                    isValid = false;
                    $(this).addClass("is-invalid");
                    toastr.error("Vui lòng điền đầy đủ thông tin!");
                    return false;
                } else {
                    $(this).removeClass("is-invalid");
                }
            });
            
            if (CKEDITOR.instances['description']) {
                var desc = CKEDITOR.instances['description'].getData();
                if (!desc || desc.trim() === '') {
                    isValid = false;
                    toastr.error("Vui lòng điền mô tả!");
                }
            }
            
            if (!isValid) {
                return false;
            }
            
            console.log('✅ Validation passed. Creating tour...');
            
            $.ajax({
                url: '/admin/add-tours',
                method: 'POST',
                data: $("#form-step1").serialize(),
                success: function(response) {
                    console.log('📥 Response:', response);
                    if (response.success) {
                        $('.hiddenTourId').val(response.tourId);
                        toastr.success("Tạo tour thành công");
                        
                        // ✅ CHO PHÉP CHUYỂN STEP
                        $('#wizard').smartWizard('goForward');
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function() {
                    toastr.error("Lỗi tạo tour!");
                }
            });
            
            return false;
        }
        
        // STEP 2: Validate images
        if (currentStep === 2) {
            console.log('📸 Validating step 2...');
            
            if (!myDropzone) {
                toastr.error('Dropzone chưa khởi tạo!');
                return false;
            }
            
            const uploaded = myDropzone.files.filter(f => f.status === "success").length;
            console.log('📊 Images uploaded:', uploaded);
            
            if (uploaded < 1) {
                toastr.error('Vui lòng upload ít nhất 1 ảnh!');
                return false;
            }
            
            console.log('✅ Images OK');
            $('#wizard').smartWizard('goForward');
            return false;
        }
        
        // ✅ Các step khác cho phép chuyển bình thường
        $('#wizard').smartWizard('goForward');
        return false;
    });
    
    // ✅ SAU KHI SMARTWIZARD INIT, THÊM TIMELINE
    if ($(".add-tours #step-3").length) {
        const addButton = `<button type="button" id="add-timeline" class="btn btn-round btn-info" style="margin-top: 20px;">Thêm Timeline</button>`;
        $(".add-tours #step-3").append(addButton);
        addTimelineEntry();
        console.log('✅ First timeline entry added');
    }
}

// ===================================
// 7. INIT DROPZONE
// ===================================
if ($("#myDropzone").length) {
    try {
        if (Dropzone.forElement("#myDropzone")) {
            Dropzone.forElement("#myDropzone").destroy();
            console.log('Destroyed existing Dropzone');
        }
    } catch (e) {
        // Chưa có Dropzone
    }
    
    myDropzone = new Dropzone("#myDropzone", {
        url: "/admin/add-images-tours",
        method: "post",
        paramName: "image",
        acceptedFiles: "image/*",
        addRemoveLinks: true,
        dictRemoveFile: "Xóa ảnh",
        autoProcessQueue: true,
        parallelUploads: 1,
        maxFiles: 10,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
        },
        init: function() {
            this.on("sending", function(file, xhr, formData) {
                const tourId = $('.hiddenTourId').val();
                
                if (!tourId) {
                    console.error('❌ tourId is missing!');
                    toastr.error('Lỗi: Chưa có Tour ID! Vui lòng quay lại bước 1.');
                    this.removeFile(file);
                    return false;
                }
                
                formData.append("tourId", tourId);
                console.log('📤 Uploading image with tourId:', tourId);
            });
            
            this.on("success", function(file, response) {
                console.log('✅ Upload success:', response);
                toastr.success('Upload ảnh thành công: ' + file.name);
            });
            
            this.on("error", function(file, errorMessage, xhr) {
                console.error('❌ Upload error:', errorMessage);
                
                let msg = 'Lỗi upload ảnh';
                if (typeof errorMessage === 'string') {
                    msg = errorMessage;
                } else if (errorMessage.message) {
                    msg = errorMessage.message;
                }
                
                toastr.error(msg);
                this.removeFile(file);
            });
            
            this.on("complete", function(file) {
                console.log('Upload complete:', file.name, 'Status:', file.status);
            });
        }
    });
    
    console.log('✅ Dropzone initialized');
}

// ===================================
// 8. EVENT HANDLERS CHO TIMELINE
// ===================================
$(".add-tours #step-3").on("click", "#add-timeline", function () {
    addTimelineEntry();
});

$(".add-tours #step-3").on("click", ".remove-btn", function () {
    const id = $(this).data("id");
    const editorId = `itinerary-${id}`;
    
    if (CKEDITOR.instances[editorId]) {
        CKEDITOR.instances[editorId].destroy();
        console.log(`✅ Destroyed: ${editorId}`);
    }
    
    $(`#timeline-entry-${id}`).remove();
});

// ===================================
// 9. XỬ LÝ NÚT FINISH
// ===================================
$(document).on("click", ".buttonFinish", function (e) {
    e.preventDefault();
    
    const form = $("#timeline-form")[0];
    const tourId = $('.hiddenTourId').val();

    console.log('=== FINISH BUTTON CLICKED ===');
    console.log('Tour ID:', tourId);

    if (!tourId) {
        toastr.error('Không tìm thấy Tour ID! Vui lòng quay lại bước 1.');
        return;
    }

    // ✅ Kiểm tra tour có ảnh chưa
    $.ajax({
        url: '/admin/check-tour-images',
        method: 'GET',
        data: { tourId: tourId },
        success: function(response) {
            console.log('Check images response:', response);
            
            if (!myDropzone) {
                toastr.error('Dropzone chưa được khởi tạo!');
                return;
            }
            
            const uploaded = myDropzone.getAcceptedFiles().filter(f => f.status === "success").length;
            if (uploaded === 0) {
                toastr.error('Vui lòng upload ít nhất 1 ảnh trước khi hoàn tất!');
                $("#wizard").smartWizard("goToStep", 2);
                return;
            }

            console.log(`✅ Tour có ${response.count} ảnh`);

            // ✅ Kiểm tra timeline
            const timelineCount = $('.timeline-entry').length;
            if (timelineCount === 0) {
                toastr.error('Vui lòng thêm ít nhất 1 ngày trong lộ trình!');
                return;
            }

            // ✅ Kiểm tra tất cả timeline đều có nội dung
            let hasEmpty = false;
            let emptyFields = [];
            
            $('.timeline-entry').each(function() {
                const entryId = $(this).attr('id');
                const title = $(this).find('[name^="day-"]').val();
                const textareaId = $(this).find('textarea').attr('id');
                
                if (!CKEDITOR.instances[textareaId]) {
                    console.error(`CKEDITOR not found for ${textareaId}`);
                    hasEmpty = true;
                    emptyFields.push(entryId);
                    return false;
                }
                
                const content = CKEDITOR.instances[textareaId].getData();
                
                if (!title || !content || content.trim() === '') {
                    hasEmpty = true;
                    emptyFields.push(entryId);
                }
            });
            
            if (hasEmpty) {
                console.error('Empty timeline fields:', emptyFields);
                toastr.error('Vui lòng điền đầy đủ thông tin cho tất cả các ngày!');
                return;
            }

            // ✅ Submit form
            if (form && form.checkValidity()) {
                console.log('✅ All validations passed. Submitting form...');
                $("#timeline-form").submit();
            } else {
                toastr.error("Vui lòng điền đầy đủ thông tin trong form!");
                if (form) form.reportValidity();
            }
        },
        error: function(xhr) {
            console.error('Check images error:', xhr);
            toastr.error('Có lỗi khi kiểm tra ảnh. Vui lòng thử lại!');
        }
    });
});

    /********************************************
     * ✅ EDIT TOUR                             *
     ********************************************/
    
    var timelineCounter_edit;
    var formDataEdit = {};
    var tourIdSendingImage;
    var dropzoneOldImages;

    $(document).on("click", ".edit-tour", function (e) {
        e.preventDefault();
        console.log("=== EDIT TOUR ===");
        
        var tourId = $(this).data("tourid");
        var urlEdit = $(this).data("urledit");
        tourIdSendingImage = tourId;

        init_SmartWizard_Edit_Tour();

        $.ajax({
            url: urlEdit,
            method: 'GET',
            data: { tourId: tourId },
            success: function (response) {
                console.log('Edit data:', response);
                
                if (!response.success) {
                    toastr.error(response.message);
                    return;
                }
                
                const tour = response.tour;
                const images = response.images;
                const timeline = response.timeline;

                loadOldImages(images);

                const startDate = moment(tour.startDate).format("DD/MM/YYYY");
                const endDate = moment(tour.endDate).format("DD/MM/YYYY");

                $("#edit-tour-modal input[name='name']").val(tour.title);
                $("#edit-tour-modal input[name='destination']").val(tour.destination);
                $("#edit-tour-modal select[name='domain']").val(tour.domain);
                $("#edit-tour-modal input[name='number']").val(tour.quantity);
                $("#edit-tour-modal input[name='price_adult']").val(tour.priceAdult);
                $("#edit-tour-modal input[name='price_child']").val(tour.priceChild);
                $("#edit-tour-modal #start_date").val(startDate);
                $("#edit-tour-modal #end_date").val(endDate);

                setTimeout(function() {
                    if (CKEDITOR.instances.description) {
                        CKEDITOR.instances.description.setData(tour.description);
                    }
                }, 500);
                
                timelineCounter_edit = 1;
                $("#edit-tour-modal #step-3").empty();
                timeline.forEach(item => editTimelineEntry(item));
            },
            error: function(xhr) {
                console.error('Load error:', xhr);
                toastr.error('Lỗi tải dữ liệu');
            }
        });
    });

    function init_SmartWizard_Edit_Tour() {
        if (typeof $.fn.smartWizard === "undefined") {
            console.error('SmartWizard not found');
            return;
        }

        if ($("#edit-tour-modal #description").length && !CKEDITOR.instances.description) {
            CKEDITOR.replace("description");
            console.log('✅ CKEDITOR init: description (edit)');
        }

        $("#edit-tour-modal #wizard").smartWizard({
            onLeaveStep: function (obj, context) {
                var stepIndex = context.fromStep;
                var finishStep1 = true;
                var finishStep2 = true;

                if (stepIndex === 1) {
                    $("#edit-tour-modal #form-step1 input, #edit-tour-modal #form-step1 select").each(function () {
                        if ($(this).prop("required") && $(this).val().trim() === "") {
                            finishStep1 = false;
                            $(this).addClass("is-invalid");
                            toastr.error("Vui lòng điền đầy đủ!");
                        } else {
                            $(this).removeClass("is-invalid");
                        }
                    });

                    var domain = $("#edit-tour-modal #domain").val();
                    if (!domain) {
                        finishStep1 = false;
                        toastr.error("Vui lòng chọn khu vực!");
                    }

                    var description = '';
                    if (CKEDITOR.instances.description) {
                        description = CKEDITOR.instances.description.getData();
                    } else {
                        finishStep1 = false;
                        toastr.error("Vui lòng chờ CKEDITOR!");
                    }
                    
                    if (!description) {
                        finishStep1 = false;
                        toastr.error("Vui lòng điền mô tả!");
                    }

                    formDataEdit = {
                        tourId: tourIdSendingImage,
                        name: $("#edit-tour-modal input[name='name']").val(),
                        destination: $("#edit-tour-modal input[name='destination']").val(),
                        domain: $("#edit-tour-modal #domain").val(),
                        number: $("#edit-tour-modal input[name='number']").val(),
                        price_adult: $("#edit-tour-modal input[name='price_adult']").val(),
                        price_child: $("#edit-tour-modal input[name='price_child']").val(),
                        start_date: $("#edit-tour-modal #start_date").val(),
                        end_date: $("#edit-tour-modal #end_date").val(),
                        description: description,
                        _token: $('input[name="_token"]').val(),
                        images: [],
                        timeline: [],
                    };

                    return finishStep1;
                }

                if (stepIndex === 2) {
                    var formDataImages = getFormDataImages();
                    if (formDataImages === false) {
                        return false;
                    }
                    formDataEdit.images = formDataImages;
                    return finishStep2;
                }
                
                return true;
            },
        });

        Dropzone.autoDiscover = false;
        if ($("#edit-tour-modal #myDropzone-listTour").length) {
            if (dropzoneOldImages) {
                dropzoneOldImages.destroy();
            }
            
            dropzoneOldImages = new Dropzone("#edit-tour-modal #myDropzone-listTour", {
                url: window.location.origin + "/admin/add-temp-images",
                method: "post",
                paramName: "image",
                acceptedFiles: "image/*",
                addRemoveLinks: true,
                dictRemoveFile: "Xóa",
                autoProcessQueue: true,
                maxFiles: 10,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                init: function () {
                    this.on("sending", function (file, xhr, formData) {
                        formData.append("tourId", tourIdSendingImage);
                    });
                }
            });
        }

        $(".buttonNext").addClass("btn btn-success");
        $(".buttonPrevious").addClass("btn btn-primary");
        $(".buttonFinish").addClass("btn btn-default");
    }

    function getFormDataImages() {
        var formDataImages = [];
        if (!dropzoneOldImages) return [];

        var oldImages = dropzoneOldImages.files.filter(function (file) {
            return file.status === "accepted" || file.status === "complete";
        });

        oldImages.forEach(function (file) {
            formDataImages.push(file.name);
        });

        dropzoneOldImages.getAcceptedFiles().forEach(function (file) {
            if (file.xhr && file.xhr.responseText) {
                var response = JSON.parse(file.xhr.responseText);
                if (response.success && response.data && response.data.filename) {
                    formDataImages.push(response.data.filename);
                }
            }
        });

        formDataImages = [...new Set(formDataImages)];

        if (formDataImages.length < 1) {
            toastr.error("Vui lòng tải lên ít nhất 1 ảnh.");
            return false;
        }

        return formDataImages;
    }

    function loadOldImages(images) {
        if (!dropzoneOldImages) return;
        
        images.forEach(function (image) {
            const imageUrl = window.location.origin + `/clients/assets/images/gallery-tours/${image.imageURL}`;
            const mockFile = {
                name: image.imageURL,
                url: imageUrl,
                status: "accepted"
            };
            
            dropzoneOldImages.emit("addedfile", mockFile);
            dropzoneOldImages.emit("thumbnail", mockFile, imageUrl);
            dropzoneOldImages.emit("complete", mockFile);
            dropzoneOldImages.files.push(mockFile);
        });
    }

    function editTimelineEntry(data) {
        const title = data ? data.title : `Ngày ${timelineCounter_edit}`;
        const description = data ? data.description : "";
        const editorId = `itinerary-edit-${timelineCounter_edit}`;

        const html = `
        <div class="timeline-entry" id="timeline-edit-${timelineCounter_edit}">
            <label for="day-${timelineCounter_edit}">Ngày ${timelineCounter_edit}</label>
            <input type="text" class="form-control" 
                   name="day-${timelineCounter_edit}" 
                   value="${title}" required>
            
            <label for="${editorId}">Lộ trình:</label>
            <textarea id="${editorId}" name="itinerary-${timelineCounter_edit}">${description}</textarea>
        </div>
        `;

        $("#edit-tour-modal #step-3").append(html);

        setTimeout(function() {
            if ($(`#${editorId}`).length && !CKEDITOR.instances[editorId]) {
                try {
                    CKEDITOR.replace(editorId);
                    console.log(`✅ CKEDITOR init: ${editorId}`);
                } catch (e) {
                    console.error('CKEDITOR error:', e);
                }
            }
        }, 100);

        timelineCounter_edit++;
    }

    $("#edit-tour-modal").on("shown.bs.modal", function () {
        $("#edit-tour-modal .buttonFinish").off("click").on("click", function () {
            console.log('=== EDIT FINISH ===');
            
            formDataEdit.timeline = [];
            
            $("#edit-tour-modal .timeline-entry").each(function () {
                const title = $(this).find('input[name^="day-"]').val();
                const textareaId = $(this).find("textarea").attr("id");
                
                if (!CKEDITOR.instances[textareaId]) {
                    toastr.error('Lỗi CKEDITOR');
                    return false;
                }
                
                const itinerary = CKEDITOR.instances[textareaId].getData();
                formDataEdit.timeline.push({ title, itinerary });
            });

            $.ajax({
                url: '/admin/edit-tour',
                method: 'POST',
                data: formDataEdit,
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $("#edit-tour-modal").modal("hide");
                        setTimeout(() => location.reload(), 1000);
                    }
                },
                error: function (xhr) {
                    console.error('Update error:', xhr);
                    toastr.error('Lỗi cập nhật');
                }
            });
        });
    });

    $("#edit-tour-modal").on("hidden.bs.modal", function () {
        console.log('=== MODAL CLOSED ===');
        
        for (let instance in CKEDITOR.instances) {
            if (instance.startsWith('itinerary-edit-')) {
                CKEDITOR.instances[instance].destroy();
            }
        }
        
        if (dropzoneOldImages) {
            dropzoneOldImages.destroy();
            dropzoneOldImages = null;
        }
    });

    /********************************************
     * BOOKING MANAGEMENT                       *
     ********************************************/
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

    /********************************************
     * CONTACT MANAGEMENT                       *
     ********************************************/
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

    /********************************************
     * LOGIN ADMIN                              *
     ********************************************/
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

    /********************************************
     * ADMIN MANAGEMENT                         *
     ********************************************/
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

}); // ✅ Đóng $(document).ready()