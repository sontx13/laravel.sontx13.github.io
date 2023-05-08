<div class="modal fade" id="suaDaiBieuKhoaHopModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <span class="modal-title" id="themmoiModalLabel">Thiết lập đại biểu</span>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            @can('add_daibieu-khoahop')
                <div class="row">
                    <div class="col-sm-5">
                        <div class="row">
                            <div class="col-sm-3">
                                <span>Đơn vị</span>
                            </div>
                            <div class="col-sm-9">
                                <select class="form-control select2" id="cbx-donvi" onchange="OnChangeCbxDonVi({{$khoahopid}})">
                                    <option value="0">-- Tất cả --</option>
                                    @foreach ($lsDonvis as $donvi)
                                        <option value="{{$donvi->id}}">{{$donvi->ten_donvi}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-5">
                        <div class="row">
                            <div class="col-sm-3">
                                <span>Người dùng</span>
                            </div>
                            <div class="col-sm-9">
                                <select class="form-control" id="cbx-nguoidung">
                                    <option value="0">-- Thêm đại biểu --</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <a href="javascript:void(0)" onclick="AddDaiBieuKhoaHop({{$khoahopid}})" title="Thêm đại biểu"><i class="glyphicon glyphicon-plus icon icon-add-daibieu"></i></a>
                    </div>
                </div>
            @endcan
            <div id="danhsach-daibieu-khoahop">

            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
        </div>
      </div>
    </div>
</div>
