<div class="modal fade" id="suaLichHopModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <span class="modal-title" id="themmoiModalLabel">Thiết lập lịch họp</span>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <ul class="nav nav-tabs">
                @foreach ($thoigians as $key => $item)
                    <li><a data-toggle="tab" href="#ngayhop{{$key}}">{{$item->ngay_dienra}}</a></li>
                @endforeach
            </ul>

            <div class="tab-content">
                @foreach ($thoigians as $key => $item)
                    <div id="ngayhop{{$key}}" class="tab-pane fade in">
                        <div class="row">
                            @foreach ($item->lichhop as $buoihop)
                                <div class="col-sm-6">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <span>{{App\Http\Controllers\Voyager\KyHopController::GetDisplayBuoiHop($buoihop->buoi_hop)}}</span>
                                        </div>
                                        <div class="col-sm-6 text-right">
                                            @can('edit_lich-hop')
                                                <a href="javascript:void(0)" onclick="UpdateLichHop({{$buoihop->id}})" title="Cập nhật"><i class="glyphicon glyphicon-floppy-saved icon icon-sua"></i></a>
                                            @endcan
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-2">
                                            <span>Từ</span>
                                        </div>
                                        <div class="col-sm-2">
                                            @if ($buoihop->buoi_hop == App\Enums\BuoiHopEnum::BuoiSang)
                                                <select class="form-control" id="gio-batdau-{{$buoihop->id}}">
                                                    <option value="">---</option>
                                                    @foreach ($gioBuoiSang as $gio)
                                                        @if ($buoihop->thoigian_batdau != null && substr($buoihop->thoigian_batdau, 0, 2) == $gio)
                                                            <option selected value="{{$gio}}">{{$gio}}</option>
                                                        @else
                                                            <option value="{{$gio}}">{{$gio}}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            @else
                                                <select class="form-control" id="gio-batdau-{{$buoihop->id}}">
                                                    <option value="">---</option>
                                                    @foreach ($gioBuoiChieu as $gio)
                                                        @if ($buoihop->thoigian_batdau != null && substr($buoihop->thoigian_batdau, 0, 2) == $gio)
                                                            <option selected value="{{$gio}}">{{$gio}}</option>
                                                        @else
                                                            <option value="{{$gio}}">{{$gio}}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            @endif
                                        </div>
                                        <div class="col-sm-2">
                                            <select class="form-control" id="phut-batdau-{{$buoihop->id}}">
                                                <option value="">---</option>
                                                @foreach ($phuts as $phut)
                                                    @if ($buoihop->thoigian_batdau != null && substr($buoihop->thoigian_batdau, 2, 2) == $phut)
                                                        <option selected value="{{$phut}}">{{$phut}}</option>
                                                    @else
                                                        <option value="{{$phut}}">{{$phut}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-sm-2 text-center">
                                            <span>Đến</span>
                                        </div>
                                        <div class="col-sm-2">
                                            @if ($buoihop->buoi_hop == App\Enums\BuoiHopEnum::BuoiSang)
                                                <select class="form-control" id="gio-ketthuc-{{$buoihop->id}}">
                                                    <option value="">---</option>
                                                    @foreach ($gioBuoiSang as $gio)
                                                        @if ($buoihop->thoigian_ketthuc != null && substr($buoihop->thoigian_ketthuc, 0, 2) == $gio)
                                                            <option selected value="{{$gio}}">{{$gio}}</option>
                                                        @else
                                                            <option value="{{$gio}}">{{$gio}}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            @else
                                                <select class="form-control" id="gio-ketthuc-{{$buoihop->id}}">
                                                    <option value="">---</option>
                                                    @foreach ($gioBuoiChieu as $gio)
                                                        @if ($buoihop->thoigian_ketthuc != null && substr($buoihop->thoigian_ketthuc, 0, 2) == $gio)
                                                            <option selected value="{{$gio}}">{{$gio}}</option>
                                                        @else
                                                            <option value="{{$gio}}">{{$gio}}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            @endif
                                        </div>
                                        <div class="col-sm-2">
                                            <select class="form-control" id="phut-ketthuc-{{$buoihop->id}}">
                                                <option value="">---</option>
                                                @foreach ($phuts as $phut)
                                                    @if ($buoihop->thoigian_ketthuc != null && substr($buoihop->thoigian_ketthuc, 2, 2) == $phut)
                                                        <option selected value="{{$phut}}">{{$phut}}</option>
                                                    @else
                                                        <option value="{{$phut}}">{{$phut}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-2">
                                            <span>Phòng</span><span class="required">(*)</span>
                                        </div>
                                        <div class="col-sm-10">
                                            <select class="form-control" id="phong-hop-{{$buoihop->id}}">
                                                <option value="0">--Chọn phòng--</option>
                                                @foreach ($dmPhonghops as $phong)
                                                    @if ($phong->id == $buoihop->phonghop_id)
                                                        <option selected value="{{$phong->id}}">{{$phong->ten_phonghop}}</option>
                                                    @else
                                                        <option value="{{$phong->id}}">{{$phong->ten_phonghop}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-2">
                                            <span>Nội dung</span>
                                        </div>
                                        <div class="col-sm-10">
                                            <span class="buoihop-id hidden">{{$buoihop->id}}</span>
                                            <p id="document-content-{{$buoihop->id}}" class="hidden">{{$buoihop->noi_dung}}</p>
                                            <div class="document-editor-{{$buoihop->id}}">
                                                <div class="document-editor__toolbar-{{$buoihop->id}}"></div>
                                                <div class="document-editor__editable-container-{{$buoihop->id}}">
                                                    <div class="document-editor__editable-{{$buoihop->id}}">
                                                        <p></p>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- <textarea type="text"  id="noi-dung-{{$buoihop->id}}" class="form-control">{{$buoihop->noi_dung}}</textarea> --}}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
        </div>
      </div>
    </div>
</div>
