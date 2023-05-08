<div>
    <table class="table table-striped table-border">
        <thead>
            <tr>
                <th colspan="5" class="text-center">
                    <span>Phiên chất vấn</span>
                    @can('add_traloi-chatvan')
                        <a href="javascript:void(0)" onclick="ThemMoiPhienChatVan({{$kyhopid}}, {{$thoigianid}})" title="Thêm mới"><i class="glyphicon glyphicon-plus icon-themoi"></i></a>
                    @endcan
                </th>
                <th colspan="5" class="text-center">
                    <span>Đại biểu chất vấn</span>
                </th>
            </tr>
            <tr>
                <th>STT</th>
                <th>Người trả lời</th>
                <th>Thời gian</th>
                <th>Trạng thái</th>
                <th>Xử lý</th>
                <th>STT</th>
                <th>Đại biểu</th>
                <th>Thời gian</th>
                <th>Trạng thái</th>
                <th>Xử lý</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($lsChatVans as $keyItem => $dataItem)
                <tr>
                    <td class="text-center with-10px" rowspan="{{count($dataItem->chatvans) + 1}}">
                        <span>{{$dataItem->stt}}</span>
                    </td>
                    <td rowspan="{{count($dataItem->chatvans) + 1}}">
                        <span>{{$dataItem->phien_chatvan}}</span>
                    </td>
                    <td class="text-center with-40px" rowspan="{{count($dataItem->chatvans) + 1}}">
                        <span>
                            {{$dataItem->thoigian_phien}} phút
                        </span>
                    </td>
                    <td class="trang-thai" rowspan="{{count($dataItem->chatvans) + 1}}">
                        @if ($dataItem->trang_thai == App\Enums\TrangThaiPhienChatVanEnum::ChuaBatDau)
                            <span class="tt-phien-chuabatdau form-control">{{App\Http\Controllers\Voyager\ChatVanController::GetDisplayTTPhienChatVan($dataItem->trang_thai)}}</span>
                        @elseif ($dataItem->trang_thai == App\Enums\TrangThaiPhienChatVanEnum::DangDienRa)
                            <span class="tt-phien-dangdienra form-control">{{App\Http\Controllers\Voyager\ChatVanController::GetDisplayTTPhienChatVan($dataItem->trang_thai)}}</span>
                        @else
                            <span class="tt-phien-dahoanthanh form-control">{{App\Http\Controllers\Voyager\ChatVanController::GetDisplayTTPhienChatVan($dataItem->trang_thai)}}</span>
                        @endif
                    </td>
                    <td class="xy-ly" rowspan="{{count($dataItem->chatvans) + 1}}">
                        @if ($dataItem->trang_thai == App\Enums\TrangThaiPhienChatVanEnum::ChuaBatDau)
                            @can('edit_traloi-chatvan')
                                <a href="javascript:void(0)" onclick="BatDauPhienChatVan({{$dataItem->id}})" title="Bắt đầu"><i class="glyphicon glyphicon-play-circle icon icon-batdau"></i></a>
                                <a href="javascript:void(0)" onclick="SuaPhienChatVan({{$dataItem->id}}, '{{$dataItem->phien_chatvan}}', {{$dataItem->thoigian_phien}},{{$dataItem->stt}}, {{$dataItem->thoigian_kyhop_id}})" title="Sửa"><i class="glyphicon glyphicon-option-horizontal icon icon-sua"></i></a>
                            @endcan
                        @elseif ($dataItem->trang_thai == App\Enums\TrangThaiPhienChatVanEnum::DangDienRa)
                            @can('edit_traloi-chatvan')
                                <a href="javascript:void(0)" onclick="KetThucPhienChatVan({{$dataItem->id}})" title="Kết thúc"><i class="glyphicon glyphicon-off icon icon-ketthuc"></i></a>
                            @endcan
                        @endif
                        @if ($dataItem->trang_thai == App\Enums\TrangThaiPhienChatVanEnum::DangDienRa || $dataItem->trang_thai == App\Enums\TrangThaiPhienChatVanEnum::DaHoanThanh)
                            <a href="javascript:void(0)" onclick="ShowChatVan({{$dataItem->id}})" title="Xem"><i class="glyphicon glyphicon-fullscreen icon icon-xemkq"></i></a>
                        @endif
                        @if ($dataItem->trang_thai == App\Enums\TrangThaiPhienChatVanEnum::ChuaBatDau || $dataItem->trang_thai == App\Enums\TrangThaiPhienChatVanEnum::DaHoanThanh)
                            @can('delete_traloi-chatvan')
                                <a href="javascript:void(0)" onclick="XoaPhienChatVan({{$dataItem->id}})" title="Xóa"><i class="glyphicon glyphicon-trash icon icon-xoa"></i></a>
                            @endcan
                        @endif
                        @if ($dataItem->trang_thai == App\Enums\TrangThaiPhienChatVanEnum::ChuaBatDau || $dataItem->trang_thai == App\Enums\TrangThaiPhienChatVanEnum::DangDienRa)
                            @can('add_chat-van')
                                <a href="javascript:void(0)" onclick="ThemMoiDaiBieuChatVan({{$kyhopid}}, {{$thoigianid}}, {{$dataItem->id}})" title="Thêm mới đại biểu"><i class="glyphicon glyphicon-plus icon icon-themoi"></i></a>
                            @endcan
                        @endif
                        <span style="font-size: 28px;">&nbsp;</span>
                    </td>
                </tr>
                @foreach ($dataItem->chatvans as $keydaibieu => $daibieu)
                    <tr>
                        <td class="text-center with-10px">
                            <span>{{$daibieu->stt}}</span>
                        </td>
                        <td>
                            {{$daibieu->user->name}}
                        </td>
                        <td class="text-center with-40px">
                            {{$daibieu->thoigian_dangky}} phút
                        </td>
                        <td class="trang-thai">
                            @if ($daibieu->trang_thai == App\Enums\TrangThaiChatVanEnum::ChoDuyet)
                                <span class="tt-chatvan-choduyet form-control">{{App\Http\Controllers\Voyager\ChatVanController::GetDisplayTTChatVan($daibieu->trang_thai)}}</span>
                            @elseif ($daibieu->trang_thai == App\Enums\TrangThaiChatVanEnum::ChoChatVan)
                                <span class="tt-chatvan-chochatvan form-control">{{App\Http\Controllers\Voyager\ChatVanController::GetDisplayTTChatVan($daibieu->trang_thai)}}</span>
                            @elseif ($daibieu->trang_thai == App\Enums\TrangThaiChatVanEnum::DangChatVan)
                                <span class="tt-chatvan-dangchatvan form-control">{{App\Http\Controllers\Voyager\ChatVanController::GetDisplayTTChatVan($daibieu->trang_thai)}}</span>
                            @else
                                <span class="tt-chatvan-dachatvan form-control">{{App\Http\Controllers\Voyager\ChatVanController::GetDisplayTTChatVan($daibieu->trang_thai)}}</span>
                            @endif
                        </td>
                        <td class="xy-ly">
                            @if ($daibieu->trang_thai == App\Enums\TrangThaiChatVanEnum::ChoDuyet)
                                @can('edit_chat-van')
                                    <a href="javascript:void(0)" onclick="DuyetChatVan({{$daibieu->id}})" title="Duyệt"><i class="glyphicon glyphicon-ok icon icon-duyet"></i></a>
                                @endcan
                            @elseif ($daibieu->trang_thai == App\Enums\TrangThaiChatVanEnum::ChoChatVan)
                                @can('edit_chat-van')
                                    <a href="javascript:void(0)" onclick="BatDauChatVan({{$daibieu->id}})" title="Bắt đầu"><i class="glyphicon glyphicon-play-circle icon icon-batdau"></i></a>
                                @endcan
                            @endif
                            @if ($daibieu->trang_thai == App\Enums\TrangThaiChatVanEnum::DangChatVan)
                                @can('edit_chat-van')
                                    <a href="javascript:void(0)" onclick="KetThucChatVan({{$daibieu->id}})" title="Kết thúc"><i class="glyphicon glyphicon-off icon icon-ketthuc"></i></a>
                                @endcan
                            @endif
                            @if ($daibieu->trang_thai == App\Enums\TrangThaiChatVanEnum::ChoDuyet || $daibieu->trang_thai == App\Enums\TrangThaiChatVanEnum::ChoChatVan)
                                @can('edit_chat-van')
                                    <a href="javascript:void(0)" onclick="SuaChatVan({{$daibieu->id}})" title="Sửa"><i class="glyphicon glyphicon-option-horizontal icon icon-sua"></i></a>
                                @endcan
                            @endif
                            @if ($daibieu->trang_thai != App\Enums\TrangThaiChatVanEnum::DangChatVan)
                                @can('delete_chat-van')
                                    <a href="javascript:void(0)" onclick="XoaChatVan({{$daibieu->id}})" title="Xóa"><i class="glyphicon glyphicon-trash icon icon-xoa"></i></a>
                                @endcan
                            @endif
                            <span style="font-size: 28px;">&nbsp;</span>
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</div>
