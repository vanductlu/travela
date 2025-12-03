@include('admin.blocks.header')
<div class="container body">
    <div class="main_container">
        @include('admin.blocks.sidebar')

        <div class="right_col" role="main">
            <div class="">
                <div class="page-title">
                    <div class="title_left">
                        <h3>Quản lý <small>Tours</small></h3>
                    </div>
                </div>

                <div class="clearfix"></div>

                <div class="row">
                    <div class="col-md-12 col-sm-12 ">
                        <div class="x_panel">
                            <div class="x_title">
                                <h2>Tours</h2>
                                <ul class="nav navbar-right panel_toolbox">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                    </li>
                                    <li><a class="close-link"><i class="fa fa-close"></i></a>
                                    </li>
                                </ul>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="card-box table-responsive">
                                            <p class="text-muted font-13 m-b-30">
                                                Chào mừng bạn đến với trang quản lý tour. Tại đây, bạn có thể thêm mới,
                                                chỉnh sửa, và quản lý tất cả các tour hiện có.
                                            </p>
                                            <table id="datatable-listTours" class="table table-striped table-bordered"
                                                style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th>Tên</th>
                                                        <th>Thời gian</th>
                                                        <th>Mô tả</th>
                                                        <th>Số lượng</th>
                                                        <th>Giá người lớn</th>
                                                        <th>Giá trẻ em</th>
                                                        <th>Điểm đến</th>
                                                        <th>Khả dụng</th>
                                                        <th>Ngày bắt đầu</th>
                                                        <th>Ngày kết thúc</th>
                                                        <th>Sửa</th>
                                                        <th>Xóa</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tbody-listTours">
                                                    @include('admin.partials.list-tours')
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
<div class="modal fade" id="edit-tour-modal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Chỉnh sửa Tour</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">×</button>
      </div>

      <div class="modal-body">
        <div id="wizard" class="form_wizard wizard_horizontal">

          <ul class="wizard_steps">
            <li><a href="#step-1"><span class="step_no">1</span><span class="step_descr">Thông tin</span></a></li>
            <li><a href="#step-2"><span class="step_no">2</span><span class="step_descr">Hình ảnh</span></a></li>
            <li><a href="#step-3"><span class="step_no">3</span><span class="step_descr">Lộ trình</span></a></li>
          </ul>

          <!-- STEP 1 -->
          <div id="step-1">
            <form id="form-step1">
              @csrf
              <input type="hidden" class="hiddenTourId" name="tourId" value="">
              <div class="form-group">
                <label>Tên tour</label>
                <input type="text" name="name" class="form-control" required>
              </div>
              <div class="form-group">
                <label>Điểm đến</label>
                <input type="text" name="destination" class="form-control" required>
              </div>
              <div class="form-group">
                <label>Khu vực</label>
                <select class="form-control" name="domain" id="domain">
                  <option value="">Chọn khu vực</option>
                  <option value="b">Miền Bắc</option>
                  <option value="t">Miền Trung</option>
                  <option value="n">Miền Nam</option>
                </select>
              </div>
              <div class="form-group">
                <label>Số lượng</label>
                <input type="number" name="number" class="form-control" required>
              </div>
              <div class="form-group">
                <label>Giá người lớn</label>
                <input type="number" name="price_adult" class="form-control" required>
              </div>
              <div class="form-group">
                <label>Giá trẻ em</label>
                <input type="number" name="price_child" class="form-control" required>
              </div>
              <div class="form-group">
                <label>Ngày khởi hành</label>
                <input type="text" id="start_date" name="start_date" class="form-control datetimepicker" disabled>
              </div>
              <div class="form-group">
                <label>Ngày kết thúc</label>
                <input type="text" id="end_date" name="end_date" class="form-control datetimepicker" disabled>
              </div>
              <div class="form-group">
                <label>Mô tả</label>
                <textarea name="description" id="description" rows="6"></textarea>
              </div>
            </form>
          </div>

          <!-- STEP 2 -->
          <div id="step-2">
            <!-- Dropzone area -->
            <form action="/admin/add-images-tours" class="dropzone" id="myDropzone-editTour">@csrf</form>
            <p class="small text-muted mt-2">Bạn có thể xóa ảnh cũ hoặc upload thêm ảnh mới.</p>
          </div>

          <!-- STEP 3 -->
          <div id="step-3"></div>

        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
        <button type="button" class="btn btn-info buttonFinishEdit">Lưu</button>
      </div>
    </div>
  </div>
</div>

                        <form class="dropzone" id="myDropzone-editTour">@csrf</form>
                    </div>


                    <!-- STEP 3 -->
                    <div id="step-3"></div>
                    <div class="modal-footer mt-3">
                        <button type="button" class="btn btn-info buttonFinishEdit">Lưu</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('admin.blocks.footer')
