<div class="">
    <div class="row header">
        <div class="col-sm-1 text-center">
            <span>STT</span>
        </div>
        <div class="col-sm-3">
            <span>Tên khóa họp</span>
        </div>
        <div class="col-sm-2">
            <span>Ngày bắt đầu</span>
        </div>
        <div class="col-sm-2">
            <span>Ngày kết thúc</span>
        </div>
        <div class="col-sm-2">
            <span>Trạng thái</span>
        </div>
        <div class="col-sm-2">
            <span>Xử lý</span>
        </div>
    </div>
    @foreach ($listKhoaHop as $keyItem => $dataItem)
    <div class="row">
        <div class="col-sm-1 text-center">
            <span>{{$keyItem + 1}}</span>
        </div>
        <div class="col-sm-3">
            <span>{{$dataItem->ten_khoa_hop}}</span>
        </div>
        <div class="col-sm-2">
            <span>{{$dataItem->ngay_bat_dau}}</span>
        </div>
        <div class="col-sm-2">
            <span>{{$dataItem->ngay_ket_thuc}}</span>
        </div>
        <div class="col-sm-2">
            <span class="form-control {{App\Http\Controllers\Voyager\KhoaHopController::GetClassTrangThaiKhoaHop($dataItem->trang_thai)}}">{{App\Http\Controllers\Voyager\KhoaHopController::GetDisplayTTKhoaHop($dataItem->trang_thai)}}</span>
        </div>
        <div class="col-sm-2">
            @can('browse_daibieu-khoahop')
                <a href="javascript:void(0)" onclick="ShowPopUpDaiBieu({{$dataItem->id}})" title="Đại biểu"><i class="glyphicon glyphicon-user icon icon-vaitro-kyhop"></i></a>
            @endcan
            @can('edit_khoa-hop')
                <a href="javascript:void(0)" onclick="ChuyenTrangThai({{$dataItem->id}},{{$dataItem->trang_thai}})" title="Chuyển trạng thái"><i class="glyphicon glyphicon-refresh icon icon-chuyentt"></i></a>
                <a href="javascript:void(0)" onclick="showModalCapNhatKhoaHop({{$dataItem->id}},'{{$dataItem->ten_khoa_hop}}','{{$dataItem->ngay_bat_dau}}','{{$dataItem->ngay_ket_thuc}}')" title="Sửa"><i class="glyphicon glyphicon-option-horizontal icon icon-sua"></i></a>
            @endcan
            @can('delete_khoa-hop')
                <a href="javascript:void(0)" onclick="XoaKhoaHop({{$dataItem->id}})" title="Xóa"><i class="glyphicon glyphicon-trash icon icon-xoa"></i></a>
            @endcan
        </div>
    </div>
    @endforeach
</div>

