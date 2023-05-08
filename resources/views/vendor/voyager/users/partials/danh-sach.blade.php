<div class="">
    <div class="row header">
        <div class="col-sm-1 text-center">
            <span>STT</span>
        </div>
        <div class="col-sm-1">
            Ảnh đại diện
        </div>
        <div class="col-sm-4">
            <span>Họ và tên</span>
        </div>
        <div class="col-sm-2">
            <span>Đơn vị</span>
        </div>
        <div class="col-sm-2">
            <span>Trạng thái</span>
        </div>
        <div class="col-sm-2">
            <span>Xử lý</span>
        </div>
    </div>
    @foreach ($lsUsers as $index => $user)
    <div class="row">
        <div class="col-sm-1 text-center">
            <span>{{$index + 1}}</span>
        </div>
        <div class="col-sm-1">
            @if ($user->avatar != null && $user->avatar != "")
                <img src="/storage/{{$user->avatar}}" class="img-avatar">
            @else
                <img src="/storage/users/default.png" class="img-avatar">
            @endif
        </div>
        <div class="col-sm-4">
            <span>{{$user->name}}</span>
        </div>
        <div class="col-sm-2">
            @if ($user->donvi != null)
                <span>{{$user->donvi->ten_donvi}}</span>
            @else
                <span></span>
            @endif
        </div>
        <div class="col-sm-2">
            <span class="form-control {{App\Http\Controllers\Voyager\UserController::GetClassTTNguoiDung($user->is_active)}}">{{App\Http\Controllers\Voyager\UserController::GetDisplayTTNguoiDung($user->is_active)}}</span>
        </div>
        <div class="col-sm-2">
            <a href="javascript:void(0)" onclick="ChuyenTrangThaiNguoiDung({{$user->id}})" title="Chuyển trạng thái"><i class="glyphicon glyphicon-refresh icon icon-chuyentt"></i></a>
            <a href="javascript:void(0)" onclick="ShowPopupSuaUser({{$user->id}})" title="Sửa"><i class="glyphicon glyphicon-option-horizontal icon icon-sua"></i></a>
            <a href="javascript:void(0)" onclick="ShowPopupXoaUser({{$user->id}})" title="Xóa"><i class="glyphicon glyphicon-trash icon icon-xoa"></i></a>
            <a href="javascript:void(0)" onclick="ResetPassword({{$user->id}})" title="Đặt về mật khẩu mặc định"><i class="glyphicon glyphicon-repeat icon icon-reset-mk"></i></a>
        </div>
    </div>
    @endforeach
</div>
