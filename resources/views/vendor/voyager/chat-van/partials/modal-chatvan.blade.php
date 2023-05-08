<div class="modal fade" id="modalChatVan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <span class="modal-title" id="themmoiChatVanModalLabel">{{$title}}</span>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-sm-3">
                    <span>Đại biểu </span><span class="required">(*)</span>
                </div>
                <div class="col-sm-9">
                    <select class="select2 form-control" id="cbx-daibieu">
                        @foreach ($lsDaiBieus as $data)
                            @if ($chatvanid > 0 && $data->id == $chatvan->nguoi_chatvan)
                                <option selected value="{{$data->id}}">{{$data->text}}</option>
                            @else
                                <option value="{{$data->id}}">{{$data->text}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3">
                    <span>Nội dung </span>
                </div>
                <div class="col-sm-9">
                    @if ($chatvanid > 0)
                        <textarea type="text" id="txt-noidung" class="form-control">{{$chatvan->noidung_chatvan}}</textarea>
                    @else
                        <textarea type="text" id="txt-noidung" class="form-control"></textarea>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3">
                    <span>Thời gian </span><span class="required">(*)</span>
                </div>
                <div class="col-sm-9">
                    @if ($chatvanid > 0)
                        <input type="number" min="0" id="txt-tgchatvan" class="form-control" value="{{$chatvan->thoigian_dangky}}">
                    @else
                        <input type="number" min="0" id="txt-tgchatvan" class="form-control">
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3">
                    <span>STT</span>
                </div>
                <div class="col-sm-9">
                    @if ($chatvanid > 0)
                        <input type="number" min="0" id="txt-sttchatvan" class="form-control" value="{{$chatvan->stt}}">
                    @else
                        <input type="number" min="0" id="txt-sttchatvan" class="form-control">
                    @endif
                </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
            @if ($chatvanid > 0)
                <button type="button" class="btn btn-primary btn-luu-chatvan" onclick="LuuChatVan({{$chatvanid}})">Lưu</button>
            @else
            <button type="button" class="btn btn-primary btn-them-chatvan" onclick="ThemMoiChatVan({{$phienchatvanid}})">Thêm</button>
            @endif
        </div>
      </div>
    </div>
</div>
