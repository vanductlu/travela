
@include('clients.blocks.header')
@section('title', 'Quên mật khẩu')

<div class="login-template" style="margin-bottom: 50px; margin-top: 50px;">
<div class="container mt-5">
    <h3>Quên mật khẩu</h3>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if(session('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <form action="{{ route('password.send') }}" method="POST">
        @csrf
        <label>Email khôi phục:</label>
        <input type="email" name="email" class="form-control" required>

        <button class="btn btn-primary mt-3">Gửi liên kết đặt lại</button>
    </form>
</div>
</div>
@include('clients.blocks.footer')