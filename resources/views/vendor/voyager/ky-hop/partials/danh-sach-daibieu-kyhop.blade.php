<div class="">
    <div class="row header">
        <div class="col-sm-1 text-center">
            <span>STT</span>
        </div>
        <div class="col-sm-7">
            <span>Họ và tên</span>
        </div>
        <div class="col-sm-2">
            <span>Vai trò</span>
        </div>
        <div class="col-sm-2">
            @can('edit_daibieu-kyhop')
                <a href="javascript:void(0)" onclick="ThemTatCaDaiBieuKhoaHop({{$kyhop->id}})" title="Thêm tất cả đại biểu khóa họp"><i class="glyphicon glyphicon-th icon icon-add-all-daibieu"></i></a>
                <a href="javascript:void(0)" onclick="ShowComboBoxThemMotDaiBieuKhoaHop({{$kyhop->id}})" title="Thêm một đại biểu khóa họp"><i class="glyphicon glyphicon-plus icon icon-add-daibieu"></i></a>
                <a href="javascript:void(0)" onclick="ShowComboBoxThemKhachMoiKyHop({{$kyhop->id}})" title="Thêm khách mời kỳ họp"><i class="glyphicon glyphicon-plus-sign icon icon-add-khachmoi"></i></a>
                <a href="javascript:void(0)" onclick="CapnhatSTTKhoahop({{$kyhop->id}})" title="Cập nhật số thứ tự"><i class="glyphicon glyphicon-sort icon icon-reload-stt"></i></a>

            @endcan
            <div id="div-cbx-add-daibieu-khachmoi">
            </div>
        </div>
    </div>
    @php
        $index = 1;
    @endphp
    @foreach ($daibieus as $daibieu)
        <div class="row">
            <div class="col-sm-1 text-center">
                <span>{{$index}}</span>
            </div>
            <div class="col-sm-7">
                @if ($daibieu->user != null)
                    <span>{{$daibieu->user->name}}</span>
                @endif
            </div>
            <div class="col-sm-2">
                <span class="form-control {{App\Http\Controllers\Voyager\KyHopController::GetDisplayClassVaiTroDaiBieu($daibieu->vai_tro)}}">{{App\Http\Controllers\Voyager\KyHopController::GetDisplayVaiTroDaiBieu($daibieu->vai_tro)}}</span>
            </div>
            <div class="col-sm-2">
                @can('delete_daibieu-kyhop')
                    <a href="javascript:void(0)" onclick="ShowPopUpXoaVaiTroDaiBieu({{$kyhop->id}}, {{$daibieu->id}})" title="Xóa"><i class="glyphicon glyphicon-trash icon icon-xoa"></i></a>
                @endcan
            </div>
        </div>
        @php
            $index++;
        @endphp
    @endforeach
    @foreach ($khachmois as $khachmoi)
        <div class="row">
            <div class="col-sm-1 text-center">
                <span>{{$index}}</span>
            </div>
            <div class="col-sm-7">
                @if ($khachmoi->user != null)
                    <span>{{$khachmoi->user->name}}</span>
                @endif
            </div>
            <div class="col-sm-2">
                <span class="form-control {{App\Http\Controllers\Voyager\KyHopController::GetDisplayClassVaiTroDaiBieu($khachmoi->vai_tro)}}">{{App\Http\Controllers\Voyager\KyHopController::GetDisplayVaiTroDaiBieu($khachmoi->vai_tro)}}</span>
            </div>
            <div class="col-sm-2">
                @can('delete_daibieu-kyhop')
                    <a href="javascript:void(0)" onclick="ShowPopUpXoaVaiTroDaiBieu({{$kyhop->id}}, {{$khachmoi->id}})" title="Xóa"><i class="glyphicon glyphicon-trash icon icon-xoa"></i></a>
                @endcan
            </div>
        </div>
        @php
            $index++;
        @endphp
    @endforeach
</div>
