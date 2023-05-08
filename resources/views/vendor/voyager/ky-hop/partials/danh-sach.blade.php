<div class="">
    <div class="row header">
        <div class="col-sm-1 text-center">
            <span>STT</span>
        </div>
        <div class="col-sm-5">
            <span>Tên kỳ họp</span>
        </div>
        <div class="col-sm-2">
            <span>Địa điểm</span>
        </div>
        <div class="col-sm-2">
            <span>Trạng thái</span>
        </div>
        <div class="col-sm-2">
            <span>Xử lý</span>
        </div>
    </div>
    @foreach ($lsKyHops as $index => $kyhop)
    <div class="row">
        <div class="col-sm-1 text-center">
            <span>{{$index + 1}}</span>
        </div>
        <div class="col-sm-5">
            <span>{{$kyhop->ten_ky_hop}}</span>
        </div>
        <div class="col-sm-2">
            <span>{{$kyhop->dia_diem}}</span>
        </div>
        <div class="col-sm-2">
            <span class="form-control {{App\Http\Controllers\Voyager\KyHopController::GetClassTrangThaiKyHop($kyhop->trang_thai)}}">{{App\Http\Controllers\Voyager\KyHopController::GetDisplayTTKyHop($kyhop->trang_thai)}}</span>
        </div>
        <div class="col-sm-2">
            @can('edit_ky-hop')
                <a href="javascript:void(0)" onclick="ShowPopupSuaKyHop({{$kyhop->id}})" title="Sửa"><i class="glyphicon glyphicon-option-horizontal icon icon-sua"></i></a>
            @endcan
            @can('delete_ky-hop')
                <a href="javascript:void(0)" onclick="ShowPopupXoaKyHop({{$kyhop->id}})" title="Xóa"><i class="glyphicon glyphicon-trash icon icon-xoa"></i></a>
            @endcan
            @can('browse_lich-hop')
                <a href="javascript:void(0)" onclick="ShowPopupSetLichHop({{$kyhop->id}})" title="Lịch họp"><i class="glyphicon glyphicon-calendar icon icon-lichhop"></i></a>
            @endcan
            @can('browse_daibieu-kyhop')
                <a href="javascript:void(0)" onclick="ShowPopupSuaVaiTroKyHop({{$kyhop->id}})" title="Đại biểu/Khách mời"><i class="glyphicon glyphicon-user icon icon-vaitro-kyhop"></i></a>
            @endcan
        </div>
    </div>
    @endforeach
</div>
