@include('admin.blocks.header')
<div class="container body">
    <div class="main_container">
        @include('admin.blocks.sidebar')

        <div class="right_col" role="main">
            <div class="page-title">
                <div class="title_left">
                    <h3>Thông tin Admin</h3>
                </div>
            </div>
            <div class="clearfix"></div>

            <div class="row profile-container">
                <div class="col-md-3 profile-left text-center">
                    <div class="profile-img mb-3">
                        <img id="avatarAdminPreview" class="rounded-circle"
                             src="{{ asset('clients/assets/images/user-profile/avt_admin.jpg') }}"
                             alt="Avatar" width="150" height="150">
                        <input type="file" id="avatarAdmin" style="display:none" accept="image/*">
                        <label for="avatarAdmin" class="btn btn-primary mt-2">
                            <i class="fa fa-edit"></i> Tải ảnh
                        </label>
                    </div>
                    <h4 id="nameAdmin">{{ $admin->fullName }}</h4>
                    <p class="text-muted mb-2"><i class="fa fa-envelope"></i> {{ $admin->email }}</p>
                    <p class="text-muted"><i class="fa fa-map-marker"></i> {{ $admin->address }}</p>
                </div>

                <div class="col-md-9 profile-right">
                    <form action="{{ route('admin.update-admin') }}" method="POST" id="formProfileAdmin">
                        @csrf
                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label">Tên admin</label>
                            <div class="col-sm-9">
                                <input type="text" name="fullName" class="form-control"
                                       value="{{ $admin->fullName }}" placeholder="Nhập tên admin" required>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label">Mật khẩu</label>
                            <div class="col-sm-9">
                                <input type="password" name="password" class="form-control"
                                       value="{{ $admin->password }}" placeholder="Nhập mật khẩu" required>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label">Email</label>
                            <div class="col-sm-9">
                                <input type="email" name="email" class="form-control"
                                       value="{{ $admin->email }}" placeholder="Nhập email" required>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label">Địa chỉ</label>
                            <div class="col-sm-9">
                                <input type="text" name="address" class="form-control"
                                       value="{{ $admin->address }}" placeholder="Nhập địa chỉ" required>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-success">Cập nhật</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@include('admin.blocks.footer')
