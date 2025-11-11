@include('admin.blocks.header')
<div class="container body">
    <div class="main_container">
        @include('admin.blocks.sidebar')

        <!-- page content -->
        <div class="right_col" role="main">
            <div class="page-title d-flex justify-content-between align-items-center mb-3">
                <h3>Quản lý người dùng</h3>
                <div class="col-md-5 col-sm-5 top_search">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search for...">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button">Go!</button>
                        </span>
                    </div>
                </div>
            </div>

            <div class="clearfix"></div>

            <div class="row user-cards">
                @foreach ($users as $user)
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                        <div class="card user-card shadow-sm">
                            <div class="card-body d-flex flex-column align-items-center text-center">
                                <div class="user-avatar mb-3">
                                    <img src="{{ asset('clients/assets/images/user-profile/' . $user->avatar) }}" 
                                         alt="{{ $user->fullName }}" class="rounded-circle">
                                </div>
                                <h5 class="card-title">{{ $user->fullName }}</h5>
                                <p class="text-muted mb-1"><strong>Username:</strong> {{ $user->username }}</p>
                                <p class="text-muted mb-1"><strong>Email:</strong> {{ $user->email ?? 'Chưa cập nhật' }}</p>
                                <p class="text-muted mb-1"><strong>Phone:</strong> {{ $user->phoneNumber ?? 'Chưa cập nhật' }}</p>
                                <p class="text-muted mb-3"><strong>Address:</strong> {{ $user->address ?? 'Chưa cập nhật' }}</p>
                                <p class="status mb-3">
                                    <span class="badge {{ $user->isActive == 'Chưa kích hoạt' ? 'bg-warning' : 'bg-success' }}">
                                        {{ $user->isActive }}
                                    </span>
                                </p>
                                <div class="d-flex flex-wrap justify-content-center gap-2">
                                    @if ($user->isActive == 'Chưa kích hoạt')
                                        <button class="btn btn-sm btn-primary btn-action" 
                                            data-attr='{"userId": "{{ $user->userId }}", "action": "{{ route('admin.active-user') }}"}'>
                                            <i class="fa fa-check"></i> Kích hoạt
                                        </button>
                                    @endif

                                    @if($user->status !== 'b')
                                        <button class="btn btn-sm btn-warning btn-action" 
                                            data-attr='{"userId": "{{ $user->userId }}", "action": "{{ route('admin.status-user') }}", "status": "b"}'>
                                            <i class="fa fa-ban"></i> Chặn
                                        </button>
                                    @else
                                        <button class="btn btn-sm btn-success btn-action" 
                                            data-attr='{"userId": "{{ $user->userId }}", "action": "{{ route('admin.status-user') }}", "status": ""}'>
                                            <i class="fa fa-check"></i> Bỏ chặn
                                        </button>
                                    @endif

                                    @if($user->status !== 'd')
                                        <button class="btn btn-sm btn-danger btn-action" 
                                            data-attr='{"userId": "{{ $user->userId }}", "action": "{{ route('admin.status-user') }}", "status": "d"}'>
                                            <i class="fa fa-trash"></i> Xóa
                                        </button>
                                    @else
                                        <button class="btn btn-sm btn-success btn-action" 
                                            data-attr='{"userId": "{{ $user->userId }}", "action": "{{ route('admin.status-user') }}", "status": ""}'>
                                            <i class="fa fa-undo"></i> Khôi phục
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
        <!-- /page content -->
    </div>
</div>
@include('admin.blocks.footer')
