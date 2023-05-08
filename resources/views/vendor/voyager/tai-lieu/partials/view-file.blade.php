<div class="row">
    <div class="col-sm-2">
        <span>Tên tài liệu </span><span class="required">(*)</span>
    </div>
    <div class="col-sm-10">
        <textarea class="form-control" id="ten_file">{{$tailieu->ten_tailieu}}</textarea>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <div class="row">
            <div class="col-sm-4">
                <span>Số ký hiệu</span>
            </div>
            <div class="col-sm-8">
                <input type="text" class="form-control" id="sokyhieu_file" value="{{$tailieu->tailieuchitiet->so_kyhieu}}">
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="row">
            <div class="col-sm-4">
                <span>Ngày</span>
            </div>
            <div class="col-sm-8">
                <input type="date" id="ngay_vanban_file" class="form-control" value="{{$tailieu->tailieuchitiet->ngay_vanban}}">
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-2">
        <span>Trích yếu</span>
    </div>
    <div class="col-sm-10">
        <textarea class="form-control" id="trichyeu_file">{{$tailieu->tailieuchitiet->trich_yeu}}</textarea>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <div class="row">
            <div class="col-sm-4">
                <span>Phân loại</span>
            </div>
            <div class="col-sm-8">
                <select class="form-control" id="loai_file">
                    <option value="0">Chưa phân loại</option>
                    @foreach ($dmLoaiTaiLieu as $loai)
                        @if ($tailieu->tailieuchitiet->loai_tailieu == $loai->id)
                            <option selected value="{{$loai->id}}">{{$loai->loai_tailieu}}</option>
                        @else
                            <option value="{{$loai->id}}">{{$loai->loai_tailieu}}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="row">
            <div class="col-sm-4">
                <span>Đối tượng</span>
            </div>
            <div class="col-sm-8">
                <select class="form-control" id="doituong_file">
                    @if ($tailieu->tailieuchitiet->doituong_tailieu == "1")
                        <option value="0">Tất cả</option>
                        <option selected value="1">Đại biểu</option>
                        <option value="2">Khách mời</option>
                    @elseif ($tailieu->tailieuchitiet->doituong_tailieu == "2")
                        <option value="0">Tất cả</option>
                        <option value="1">Đại biểu</option>
                        <option selected value="2">Khách mời</option>
                    @else
                        <option selected value="0">Tất cả</option>
                        <option value="1">Đại biểu</option>
                        <option value="2">Khách mời</option>
                    @endif
                </select>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <div class="row">
            <div class="col-sm-4">
                <span>STT</span>
            </div>
            <div class="col-sm-8">
                <input type="text" id="stt_file" class="form-control" value="{{$tailieu->stt}}">
            </div>
        </div>
    </div>
    {{-- <div class="col-sm-6">
        <div class="row">
            <div class="col-sm-4">
                <label>Đối tượng</label>
            </div>
            <div class="col-sm-8">
                <select class="form-control" id="doituong_file">
                    <option value="0">Tất cả</option>
                    <option value="1">Đại biểu</option>
                    <option value="2">Khách mời</option>
                </select>
            </div>
        </div>
    </div> --}}
</div>
<div class="row">
    <div class="col-sm-2">
        File
    </div>
    <div class="col-sm-10">
        @if ($tailieu->tailieuchitiet->url != null)
            @switch($tailieu->loai_tailieu)
                @case(App\Enums\LoaiTaiLieuEnum::TaiLieu)
                        @can('read_tai-lieu')
                            <a href="javascript:void(0);" title="Xem trước" onclick="OpenFile('{{$tailieu->tailieuchitiet->url}}', {{$tailieu->tailieuchitiet->file_size}}, '{{$tailieu->tailieuchitiet->file_extension}}')">{{$tailieu->tailieuchitiet->file_name}} </a>
                            <a href="{{$tailieu->tailieuchitiet->url}}" title="Tải xuống"><span class="glyphicon glyphicon-download-alt"></span></a>
                        @endcan
                        @can('delete_tai-lieu')
                            <a href="javascript:void(0)" title="Xóa" onclick="DeleteAttachmentFile({{$tailieu->id}})"><span class="glyphicon glyphicon-remove-circle"></span></a>
                        @endcan
                    @break
                @case(App\Enums\LoaiTaiLieuEnum::DuThaoNghiQuyet)
                    @can('read_duthao-nghiquyet')
                        <a href="javascript:void(0);" title="Xem trước" onclick="OpenFile('{{$tailieu->tailieuchitiet->url}}', {{$tailieu->tailieuchitiet->file_size}}, '{{$tailieu->tailieuchitiet->file_extension}}')">{{$tailieu->tailieuchitiet->file_name}} </a>
                        <a href="{{$tailieu->tailieuchitiet->url}}" title="Tải xuống"><span class="glyphicon glyphicon-download-alt"></span></a>
                    @endcan
                    @can('delete_duthao-nghiquyet')
                        <a href="javascript:void(0)" title="Xóa" onclick="DeleteAttachmentFile({{$tailieu->id}})"><span class="glyphicon glyphicon-remove-circle"></span></a>
                    @endcan
                    @break
                @case(App\Enums\LoaiTaiLieuEnum::NghiQuyetThongQua)
                    @can('read_nghi-quyet')
                        <a href="javascript:void(0);" title="Xem trước" onclick="OpenFile('{{$tailieu->tailieuchitiet->url}}', {{$tailieu->tailieuchitiet->file_size}}, '{{$tailieu->tailieuchitiet->file_extension}}')">{{$tailieu->tailieuchitiet->file_name}} </a>
                        <a href="{{$tailieu->tailieuchitiet->url}}" title="Tải xuống"><span class="glyphicon glyphicon-download-alt"></span></a>
                    @endcan
                    @can('delete_nghi-quyet')
                        <a href="javascript:void(0)" title="Xóa" onclick="DeleteAttachmentFile({{$tailieu->id}})"><span class="glyphicon glyphicon-remove-circle"></span></a>
                    @endcan
                    @break
                @default
            @endswitch
        @else
            <div class="row">
                <div class="col-sm-11">
                    <input id="file-attachment" type="file" class="form-control" size="100"
                    accept="application/pdf,.csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel, application/msword,
                    application/vnd.openxmlformats-officedocument.wordprocessingml.document" >
                </div>
                <div class="col-sm-1 text-right">
                    <a href="javascript:void(0)" title="Tải lên" onclick="UploadFileAttachment({{$tailieu->id}})"><span class="glyphicon glyphicon-open"></span></a>
                </div>
            </div>
        @endif

    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        @switch($tailieu->loai_tailieu)
            @case(App\Enums\LoaiTaiLieuEnum::TaiLieu)
                    @can('edit_tai-lieu')
                        <button type="button" class="btn btn-primary" onclick="UpdateFile({{$tailieu->id}})">Cập nhật</button>
                    @endcan
                @break
            @case(App\Enums\LoaiTaiLieuEnum::DuThaoNghiQuyet)
                    @can('edit_duthao-nghiquyet')
                        <button type="button" class="btn btn-primary" onclick="UpdateFile({{$tailieu->id}})">Cập nhật</button>
                    @endcan
                @break
            @case(App\Enums\LoaiTaiLieuEnum::NghiQuyetThongQua)
                @can('edit_nghi-quyet')
                    <button type="button" class="btn btn-primary" onclick="UpdateFile({{$tailieu->id}})">Cập nhật</button>
                @endcan
                @break
            @default
        @endswitch
    </div>
</div>
