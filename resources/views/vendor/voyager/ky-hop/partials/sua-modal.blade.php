<div class="modal fade" id="suaKyHopModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <span class="modal-title" id="themmoiModalLabel">Sửa kỳ họp</span>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-sm-3">
                    <span>Tên kỳ họp </span><span class="required">(*)</span>
                </div>
                <div class="col-sm-9">
                    <textarea type="text" id="txt-sua-tenkyhop" class="form-control">{{$kyhop->ten_ky_hop}}</textarea>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3">
                    <span>Ngày họp <span class="required">(*)</span></span>
                </div>
                <div class="col-sm-9">
                    <input type="text" id="sua-ngay-hop" class="form-control" value="{{$ngayhop}}">
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3">
                    <span>Địa điểm </span>
                </div>
                <div class="col-sm-9">
                    <textarea type="text" id="txt-sua-diadiem" class="form-control">{{$kyhop->dia_diem}}</textarea>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3">
                    <span>Trạng thái</span>
                </div>
                <div class="col-sm-9">
                    <select id="sua-trangthai-kyhop" class="form-control">
                        @if ($kyhop->trang_thai == App\Enums\TrangThaiKyHopEnum::KhongSuDung)
                            <option selected value="0">Không sử dụng</option>
                        @else
                            <option value="0">Không sử dụng</option>
                        @endif
                        @if ($kyhop->trang_thai == App\Enums\TrangThaiKyHopEnum::DangDienRa)
                            <option selected value="1">Sắp diễn ra</option>
                        @else
                            <option value="1">Sắp diễn ra</option>
                        @endif
                        @if ($kyhop->trang_thai == App\Enums\TrangThaiKyHopEnum::DaDienRa)
                            <option selected value="2">Đã diễn ra</option>
                        @else
                            <option value="2">Đã diễn ra</option>
                        @endif

                    </select>
                </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
          <button type="button" class="btn btn-primary btn-sua" onclick="UpdateKyHop({{$kyhop->id}})">Cập nhật</button>
        </div>
      </div>
    </div>
</div>
