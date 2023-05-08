<div class="container">
    @if (count($dsTaiLieu) > 0)
        @foreach ($dsTaiLieu as $tailieu)
            <div class="row" id="row-{{$tailieu->id}}">
                <div class="col-sm-12">
                    <div class="row">
                        @if ($tailieu->type == App\Enums\DangTaiLieuEnum::ThuMuc)
                            <div class="col-sm-11">
                                <div class="hover-cursor" onclick="ViewThuMuc({{$tailieu->id}})">
                                    <span class="icon-thumuc glyphicon glyphicon-folder-open" aria-hidden="true"></span> <span>{{$tailieu->ten_tailieu}}</span>
                                </div>
                            </div>
                        @elseif ($tailieu->tailieuchitiet->file_extension == App\Enums\FileExtensionEnum::Pdf)
                            <div class="col-sm-11">
                                <div class="hover-cursor" onclick="ViewFile({{$tailieu->id}}, event)">
                                    <img src="/images/pdf.gif"/></span> <span>{{$tailieu->ten_tailieu}}</span>
                                </div>
                            </div>
                        @elseif ($tailieu->tailieuchitiet->file_extension == App\Enums\FileExtensionEnum::Xls ||
                                $tailieu->tailieuchitiet->file_extension == App\Enums\FileExtensionEnum::Xlsx
                            )
                            <div class="col-sm-11">
                                <div class="hover-cursor" onclick="ViewFile({{$tailieu->id}}, event)">
                                    <img src="/images/xls.gif"/></span> <span>{{$tailieu->ten_tailieu}}</span>
                                </div>
                            </div>
                        @elseif ($tailieu->tailieuchitiet->file_extension == App\Enums\FileExtensionEnum::Doc ||
                            $tailieu->tailieuchitiet->file_extension == App\Enums\FileExtensionEnum::DocX
                        )
                                <div class="col-sm-11">
                                    <div class="hover-cursor" onclick="ViewFile({{$tailieu->id}}, event)">
                                        <img src="/images/doc.gif"/></span> <span>{{$tailieu->ten_tailieu}}</span>
                                    </div>
                                </div>
                        @else
                                <div class="col-sm-11">
                                    <div class="hover-cursor" onclick="ViewFile({{$tailieu->id}}, event)">
                                        <span class="icon-file glyphicon glyphicon-file" aria-hidden="true"></span> <span>{{$tailieu->ten_tailieu}}</span>
                                    </div>
                                </div>
                        @endif
                        <div class="col-sm-1">
                            @switch($tailieu->loai_tailieu)
                                @case(App\Enums\LoaiTaiLieuEnum::TaiLieu)
                                        @can('delete_tai-lieu')
                                            <a href="javascript:void(0);" onclick="DeleteTaiLieu({{$tailieu->id}}, true)"><span class="glyphicon glyphicon-trash"></span></a>
                                        @endcan
                                    @break
                                @case(App\Enums\LoaiTaiLieuEnum::DuThaoNghiQuyet)
                                        @can('delete_duthao-nghiquyet')
                                            <a href="javascript:void(0);" onclick="DeleteTaiLieu({{$tailieu->id}}, true)"><span class="glyphicon glyphicon-trash"></span></a>
                                        @endcan
                                    @break
                                @case(App\Enums\LoaiTaiLieuEnum::NghiQuyetThongQua)
                                    @can('delete_nghi-quyet')
                                        <a href="javascript:void(0);" onclick="DeleteTaiLieu({{$tailieu->id}}, true)"><span class="glyphicon glyphicon-trash"></span></a>
                                    @endcan
                                @break
                                @default
                            @endswitch
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="folder-empty">
            <img src="/images/folder-empty2.gif" />
            <br>
            <span>Thư mục trống</span>
        </div>
    @endif

</div>
