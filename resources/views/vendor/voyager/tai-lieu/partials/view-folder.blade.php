
<div class="row">
    <div class="col-sm-3">
        <span>Tên thư mục </span><span class="required">(*)</span>
    </div>
    <div class="col-sm-9">
        <textarea class="form-control" id="ten_folder">{{$tailieu->ten_tailieu}}</textarea>
    </div>
</div>
<div class="row">
    <div class="col-sm-3">
        <span>STT</span>
    </div>
    <div class="col-sm-9">
        <input type="text" class="form-control" id="stt_folder" value="{{$tailieu->stt}}">
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <span id="parentFolderId" class="hidden">{{$tailieu->parent_id}}</span>
        @switch($tailieu->loai_tailieu)
            @case(App\Enums\LoaiTaiLieuEnum::TaiLieu)
                    @can('edit_tai-lieu')
                        <button type="button" class="btn btn-primary" onclick="UpdateFolder({{$tailieu->id}})">Cập nhật</button>
                    @endcan
                @break
            @case(App\Enums\LoaiTaiLieuEnum::DuThaoNghiQuyet)
                    @can('edit_duthao-nghiquyet')
                        <button type="button" class="btn btn-primary" onclick="UpdateFolder({{$tailieu->id}})">Cập nhật</button>
                    @endcan
                @break
            @case(App\Enums\LoaiTaiLieuEnum::NghiQuyetThongQua)
                @can('edit_nghi-quyet')
                    <button type="button" class="btn btn-primary" onclick="UpdateFolder({{$tailieu->id}})">Cập nhật</button>
                @endcan
                @break
            @default
        @endswitch
    </div>
</div>
