<div class="">
    <div class="row header">
        <div class="col-sm-1">
            <span>STT</span>
        </div>
        <div class="col-sm-3">
            <span>Biểu quyết</span>
        </div>
        <div class="col-sm-1">
            <span>Thời gian</span>
        </div>
        <div class="col-sm-1">
            <span>Sắp xếp</span>
        </div>
        <div class="col-sm-2">
            <span>Trạng thái</span>
        </div>
        <div class="col-sm-2">
            <span>Kết quả</span>
        </div>
        <div class="col-sm-2">
            <span>Xử lý</span>
        </div>
    </div>
    @foreach ($dsBieuQuyet as $keyItem => $dataItem)
    <div class="row">
        <div class="col-sm-1 text-center">
            <span>{{$keyItem + 1}}</span>
        </div>
        <div class="col-sm-3">
            <span>{{$dataItem->ten_bieuquyet}}</span>
        </div>
        <div class="col-sm-1">
            <span>{{$dataItem->thoigian_bieuquyet}} phút</span>
        </div>
        <div class="col-sm-1 text-center">
            <span>{{$dataItem->stt}}</span>
        </div>
        <div class="col-sm-2">
            @if ($dataItem->trang_thai == App\Enums\TrangThaiBieuQuyetEnum::ChuaBieuQuyet)
                <span class="tt-chuabieuquyet form-control">{{App\Http\Controllers\Voyager\BieuQuyetController::GetDisplayTTBieuQuyet($dataItem->trang_thai)}}</span>
            @elseif ($dataItem->trang_thai == App\Enums\TrangThaiBieuQuyetEnum::DangBieuQuyet)
                <span class="tt-dangbieuquyet form-control">{{App\Http\Controllers\Voyager\BieuQuyetController::GetDisplayTTBieuQuyet($dataItem->trang_thai)}}</span>
            @else
                <span class="tt-dabieuquyet form-control">{{App\Http\Controllers\Voyager\BieuQuyetController::GetDisplayTTBieuQuyet($dataItem->trang_thai)}}</span>
            @endif
        </div>
        <div class="col-sm-2">
            @if ($dataItem->trang_thai == App\Enums\TrangThaiBieuQuyetEnum::DaBieuQuyet)
                <div>
                    <span>Đồng ý {{$dataItem->ketqua->dongy}} / {{$dataItem->ketqua->tongso_daibieu}}</span>
                </div>
                <div>
                    <span>Không đồng ý {{$dataItem->ketqua->khong_dongy}} / {{$dataItem->ketqua->tongso_daibieu}}</span>
                </div>
                <div>
                    <span>Không biểu quyết {{$dataItem->ketqua->khong_bieuquyet}} / {{$dataItem->ketqua->tongso_daibieu}}</span>
                </div>
            @endif
        </div>
        <div class="col-sm-2">
            @if ($dataItem->trang_thai == App\Enums\TrangThaiBieuQuyetEnum::ChuaBieuQuyet)
                @can('edit_bieu-quyet')
                    <a href="javascript:void(0)" onclick="BatDauBieuQuyet({{$dataItem->id}})" title="Bắt đầu"><i class="glyphicon glyphicon-play-circle icon icon-batdau"></i></a>
                    <a href="javascript:void(0)" onclick="SuaBieuQuyet({{$dataItem->id}}, '{{$dataItem->ten_bieuquyet}}', {{$dataItem->thoigian_bieuquyet}},{{$dataItem->stt}})" title="Sửa"><i class="glyphicon glyphicon-option-horizontal icon icon-sua"></i></a>
                @endcan
            @elseif ($dataItem->trang_thai == App\Enums\TrangThaiBieuQuyetEnum::DangBieuQuyet)
                @can('edit_bieu-quyet')
                    <a href="javascript:void(0)" onclick="KetThucBieuQuyet({{$dataItem->id}})" title="Kết thúc"><i class="glyphicon glyphicon-off icon icon-ketthuc"></i></a>
                @endcan
            @else
                <a href="javascript:void(0)" onclick="ShowTrinhChieuBieuQuyet({{$dataItem->id}})" title="Xem"><i class="glyphicon glyphicon-fullscreen icon icon-xemkq"></i></a>
            @endif
            @if ($dataItem->trang_thai == App\Enums\TrangThaiBieuQuyetEnum::ChuaBieuQuyet || $dataItem->trang_thai == App\Enums\TrangThaiBieuQuyetEnum::DaBieuQuyet)
                @can('delete_bieu-quyet')
                    <a href="javascript:void(0)" onclick="XoaBieuQuyet({{$dataItem->id}})" title="Xóa"><i class="glyphicon glyphicon-trash icon icon-xoa"></i></a>
                @endcan
            @endif
            <span style="font-size: 28px;">&nbsp;</span>
        </div>
    </div>
    @endforeach
</div>
