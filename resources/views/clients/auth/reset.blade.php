@include('clients.blocks.header')

<style>
    .reset-password-wrapper {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 20px;
    }

    .reset-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        overflow: hidden;
        max-width: 480px;
        width: 100%;
    }

    .reset-card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 40px 30px;
        text-align: center;
    }

    .reset-card-header h3 {
        color: white;
        margin: 0;
        font-size: 28px;
        font-weight: 600;
    }

    .reset-card-header p {
        color: rgba(255, 255, 255, 0.9);
        margin: 10px 0 0;
        font-size: 14px;
    }

    .reset-card-body {
        padding: 40px 30px;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #333;
        font-weight: 500;
        font-size: 14px;
    }

    .form-group label .text-danger {
        color: #e74c3c;
    }

    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        font-size: 15px;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-control.is-invalid {
        border-color: #e74c3c;
    }

    .form-text {
        display: block;
        margin-top: 5px;
        font-size: 13px;
        color: #666;
    }

    .invalid-feedback {
        display: block;
        margin-top: 5px;
        font-size: 13px;
        color: #e74c3c;
    }

    .alert {
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 25px;
        border: none;
    }

    .alert-danger {
        background-color: #fee;
        color: #c33;
    }

    .alert-danger ul {
        margin: 0;
        padding-left: 20px;
    }

    .btn-reset {
        width: 100%;
        padding: 14px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-bottom: 15px;
    }

    .btn-reset:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
    }

    .btn-back {
        width: 100%;
        padding: 14px;
        background: white;
        color: #667eea;
        border: 2px solid #667eea;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
        text-align: center;
    }

    .btn-back:hover {
        background: #667eea;
        color: white;
        text-decoration: none;
    }

    .icon-lock {
        width: 60px;
        height: 60px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
    }

    .icon-lock svg {
        width: 30px;
        height: 30px;
        fill: white;
    }

    @media (max-width: 576px) {
        .reset-card-header {
            padding: 30px 20px;
        }

        .reset-card-body {
            padding: 30px 20px;
        }

        .reset-card-header h3 {
            font-size: 24px;
        }
    }
</style>

<div class="reset-password-wrapper">
    <div class="reset-card">
        <div class="reset-card-header">
            <div class="icon-lock">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M12 1C8.676 1 6 3.676 6 7v2H5c-1.103 0-2 .897-2 2v10c0 1.103.897 2 2 2h14c1.103 0 2-.897 2-2V11c0-1.103-.897-2-2-2h-1V7c0-3.324-2.676-6-6-6zm0 2c2.276 0 4 1.724 4 4v2H8V7c0-2.276 1.724-4 4-4zm-1 10v3h2v-3h-2z"/>
                </svg>
            </div>
            <h3>Đặt lại mật khẩu</h3>
            <p>Nhập username và mật khẩu mới của bạn</p>
        </div>

        <div class="reset-card-body">
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('password.reset') }}" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group">
                    <label for="username">
                        Username <span class="text-danger">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="username" 
                        id="username"
                        class="form-control @error('username') is-invalid @enderror" 
                        placeholder="Nhập username của bạn"
                        value="{{ old('username') }}"
                        required
                        autocomplete="username">
                    <small class="form-text">
                        Nhập username của tài khoản cần đặt lại mật khẩu
                    </small>
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">
                        Mật khẩu mới <span class="text-danger">*</span>
                    </label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password"
                        class="form-control @error('password') is-invalid @enderror" 
                        placeholder="Nhập mật khẩu mới (tối thiểu 6 ký tự)"
                        required
                        autocomplete="new-password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">
                        Xác nhận mật khẩu <span class="text-danger">*</span>
                    </label>
                    <input 
                        type="password" 
                        name="password_confirmation" 
                        id="password_confirmation"
                        class="form-control" 
                        placeholder="Nhập lại mật khẩu mới"
                        required
                        autocomplete="new-password">
                </div>

                <button type="submit" class="btn-reset">
                    Đặt lại mật khẩu
                </button>
                
                <a href="/login" class="btn-back">
                    Quay lại đăng nhập
                </a>
            </form>
        </div>
    </div>
</div>

@include('clients.blocks.footer')