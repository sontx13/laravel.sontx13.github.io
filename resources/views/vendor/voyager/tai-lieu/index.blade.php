<!DOCTYPE html>
@extends('voyager::master')

@section('page_title', 'Tài liệu')

@section('page_header')
    <div class="card">
        <h1>
            @if ($loai == \App\Enums\LoaiTaiLieuEnum::TaiLieu)
                Tài liệu
            @elseif ($loai == \App\Enums\LoaiTaiLieuEnum::DuThaoNghiQuyet)
                Dự Thảo Nghị Quyết
            @elseif ($loai == \App\Enums\LoaiTaiLieuEnum::NghiQuyetThongQua)
                Nghị Quyết
            @endif
        </h1>
        <div>
            <div class="row">
                <div class="col-sm-1">
                    <span>Khóa họp</span>
                </div>
                <div class="col-sm-3">
                    <select id="cbx-khoahop" class="select2" onchange="OnChangeKhoaHop()">
                        <option value="0">-- Chưa chọn --</option>
                        @foreach ($lsKhoahop as $keyItem => $dataItem)
                            @if ($dataItem->trang_thai == App\Enums\TrangThaiKhoaHopEnum::HoatDong)
                                <option selected value="{{$dataItem->id}}">{{$dataItem->ten_khoa_hop}}</option>
                             @else
                                <option value="{{$dataItem->id}}">{{$dataItem->ten_khoa_hop}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-1">
                    <span>Kỳ họp</span>
                </div>
                <div class="col-sm-3">
                    <select id="cbx-kyhop" class="select2 form-control" onchange="OnChangeKyHop()">
                        <option value="0" selected="selected">-- Chưa chọn --</option>
                    </select>
                </div>
                <div class="col-sm-4">
                    @switch($loai)
                        @case(App\Enums\LoaiTaiLieuEnum::TaiLieu)
                                @can('add_tai-lieu')
                                    <button class="btn btn-primary" onclick="ShowModalThemFile()">Thêm files</button>
                                    <button class="btn btn-success" onclick="ShowModalTaoThuMuc()">Tạo thư mục</button>
                                @endcan
                            @break
                        @case(App\Enums\LoaiTaiLieuEnum::DuThaoNghiQuyet)
                                @can('add_duthao-nghiquyet')
                                    <button class="btn btn-primary" onclick="ShowModalThemFile()">Thêm files</button>
                                    <button class="btn btn-success" onclick="ShowModalTaoThuMuc()">Tạo thư mục</button>
                                @endcan
                            @break
                        @case(App\Enums\LoaiTaiLieuEnum::NghiQuyetThongQua)
                            @can('add_nghi-quyet')
                                <button class="btn btn-primary" onclick="ShowModalThemFile()">Thêm files</button>
                                <button class="btn btn-success" onclick="ShowModalTaoThuMuc()">Tạo thư mục</button>
                            @endcan
                            @break
                        @default
                    @endswitch

                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="row document-content">
        <div class="col-sm-6">
            <div id="ds-tailieu">

            </div>
        </div>
        <div class="col-sm-6">
            <div class="row">
                <div id="edit-folder-div" class="col-sm-12 hidden">
                    <a href="javascript:void(0);" onclick="BackFolder()"><span class="glyphicon glyphicon-arrow-left"></span></a>
                    <a href="javascript:void(0);"><span class="glyphicon glyphicon-edit" data-toggle="collapse" data-target="#folder-info"></span></a>
                    <span id="current-folder"></span>
                    <div id="folder-info" class="collapse">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div id="file-info">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="themoiThuMucModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <span class="modal-title" id="themmoiModalLabel">Thêm thư mục</span>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-3">
                        <span>Tên thư mục </span><span class="required">(*)</span>
                    </div>
                    <div class="col-sm-9">
                        <textarea type="text" id="txt-tenthumuc" class="form-control"></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <span>STT</span>
                    </div>
                    <div class="col-sm-9">
                        <input type="number" min="0" id="txt-sttthumuc" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
              <button type="button" class="btn btn-primary btn-them" onclick="ThemThuMuc()">Thêm</button>
            </div>
          </div>
        </div>
    </div>

    <div class="modal fade" id="themoiFileModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <span class="modal-title" id="themmoiModalLabel">Thêm danh sách file</span>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-3">
                        <span>Chọn files</span><span class="required">(*)</span>
                    </div>
                    <div class="col-sm-9">
                        <input id="file-upload" type="file" class="form-control" multiple size="100"
                            accept="application/pdf,.csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel, application/msword,
                            application/vnd.openxmlformats-officedocument.wordprocessingml.document" >
                    </div>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
              <button type="submit" class="btn btn-primary" onclick="UploadFile()">Upload</button>
            </div>
          </div>
        </div>
    </div>

    <div class="modal fade" id="viewFileModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <embed id="pdf-viewer" src="" type="" width="100%" height="500px" />
                </div>
            </div>
        </div>
    </div>
@stop
@include('voyager::loading.spin')
<link rel="stylesheet" href="/css/style.css">
@section('javascript')
    <script src="/js/app.js"></script>
    <script src="/js/sweetalert.min.js"></script>
    <script>
        var currentFolder = 0; //Id của thư mục hiện tại
        var backFolder = 0;
        const loai = "<?php Print($loai); ?>"; // Loại: 0- tài liệu, 1 - dự thảo nghị quyết, 3 - nghị quyết thông qua
        // Khởi tạo dropdown list kỳ họp
        function initKyHopController() {
            var khoahopId = $('#cbx-khoahop').val();
            $('#cbx-kyhop').select2({
                placeholder: "Nhập để tìm kiếm",
                language: {
                    noResults: function() {
                        return "Không tìm thấy kết quả nào";
                    },
                    searching: function() {
                        return "Đang tìm kiếm ...";
                    }
                },
                ajax: {
                    url: '/admin/ky-hops',
                    data: function (params) {
                        var query = {
                            search: params.term,
                            khoahopid: khoahopId
                        }
                        return query;
                    },
                    processResults: function (response) {
                        return {
                            results: response.data
                        };
                    },
                    cache: true
                }
            });
        }
        // Event thay đổi khóa họp
        function OnChangeKhoaHop() {
            $("#cbx-kyhop").val(0).trigger('change');
            initKyHopController();
        }
        // Event thay đổi kỳ họp
        function OnChangeKyHop() {
            $("#file-info").empty();
            $("#folder-info").empty();
            currentFolder = 0;
            drawDsTaiLieu(currentFolder);
        }
        // Vẽ danh sách tài liệu theo id thư mục hiện tại
        // Sau khi vẽ xong có thể thực hiện hàm callback
        function drawDsTaiLieu(folderCurrentId, callback) {
            $("#ds-tailieu").empty();
            if (folderCurrentId == 0){
                $("#edit-folder-div").addClass("hidden");
            } else {
                $("#edit-folder-div").removeClass("hidden");
            }
            var kyhopId = $('#cbx-kyhop').val();
            if (kyhopId && kyhopId > 0) {
                showLoading();
                $.ajax({
                    type: 'post',
                    url: '/admin/tai-lieu/danh-sach',
                    data: {
                        kyhopid: kyhopId,
                        parentid: folderCurrentId,
                        loai: loai
                    },
                    success: function(result) {
                        $("#ds-tailieu").html(result);
                        if (typeof callback == "function") {
                            callback();
                        }
                        hideLoading();
                    },
                    error: function(err) {
                        console.log(err);
                        hideLoading();
                    }
                });
            }
        }
        // Hiển thị modal tạo thư mục
        function ShowModalTaoThuMuc() {
            var kyhopId = $('#cbx-kyhop').val();
            if (kyhopId && kyhopId > 0) {
                $('#txt-tenthumuc').val('');
                $('#txt-sttthumuc').val('');
                $('.btn-them').removeClass("hidden");
                $('.btn-luu').addClass("hidden");
                $('#themoiThuMucModal').modal('show');
            } else {
                toastr.warning('Chưa chọn kỳ họp');
            }
        }
        // Action nút thêm thư mục
        function ThemThuMuc() {
            var kyhopId = $('#cbx-kyhop').val();
            var tenthumuc = $('#txt-tenthumuc').val();
            var sttthumuc = $('#txt-sttthumuc').val();
            if (tenthumuc && tenthumuc != null && tenthumuc != '' & tenthumuc != "") {
                showLoading();
                $.ajax({
                    type: 'post',
                    url: '/admin/tai-lieu/tao-thu-muc',
                    data: {
                        kyhopid: kyhopId,
                        tenthumuc: tenthumuc,
                        stt: sttthumuc,
                        parentid: currentFolder,
                        loai: loai
                    },
                    success: function(response) {
                        hideLoading();
                        if (response.error_code == "0") {
                            toastr.success('Tạo thư mục thành công');
                            $('#themoiThuMucModal').modal('hide');
                            drawDsTaiLieu(currentFolder);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(err) {
                        hideLoading();
                        console.log(err);
                        toastr.error('Thêm mới thư mục không thành công');
                    }
                });
            } else {
                toastr.error('Tên thư mục không được để trống');
            }
        }
        // Action khi click chọn thư mục để xem
        function ViewThuMuc(id) {
            currentFolder = id;
            drawDsTaiLieu(currentFolder);
            ViewFolder(currentFolder);
            $("#file-info").empty();
        }
        // Lấy thông tin của thư mục từ server: tên thư mục, stt
        function ViewFolder(folderid) {
            $("#folder-info").empty();
            var kyhopId = $('#cbx-kyhop').val();
            if (kyhopId && kyhopId > 0) {
                showLoading();
                $.ajax({
                    type: 'post',
                    url: '/admin/tai-lieu/xem-thu-muc',
                    data: {
                        kyhopid: kyhopId,
                        folderid: folderid
                    },
                    success: function(result) {
                        $("#folder-info").html(result);
                        backFolder = $("#parentFolderId").text();
                        var tenfolder = $("#ten_folder").val();
                        tenfolder = tenfolder.length >= 45 ? tenfolder.substring(0, 45) + "..." : tenfolder;
                        $("#current-folder").text(tenfolder);
                        hideLoading();
                    },
                    error: function(err) {
                        console.log(err);
                        hideLoading();
                    }
                });
            }
        }
        // Action trở lại thư mục cha
        function BackFolder() {
            currentFolder = backFolder;
            drawDsTaiLieu(currentFolder);
            $("#file-info").empty();
            if (currentFolder == 0) {
                $("#folder-info").empty();
            } else {
                ViewFolder(currentFolder);
            }
        }
        // Action nút cập nhật thông tin thư mục
        function UpdateFolder(folderid) {
            var kyhopId = $('#cbx-kyhop').val();
            var tenthumuc = $('#ten_folder').val();
            var sttthumuc = $('#stt_folder').val();
            if (tenthumuc && tenthumuc != null && tenthumuc != '' & tenthumuc != "") {
                showLoading();
                $.ajax({
                    type: 'post',
                    url: '/admin/tai-lieu/cap-nhat-thu-muc',
                    data: {
                        id: folderid,
                        tenthumuc: tenthumuc,
                        stt: sttthumuc
                    },
                    success: function(response) {
                        hideLoading();
                        if (response.error_code == "0") {
                            toastr.success('Cập nhật thư mục thành công');
                            tenthumuc = tenthumuc.length >= 45 ? tenthumuc.substring(0, 45) + "..." : tenthumuc;
                            $("#current-folder").text(tenthumuc);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(err) {
                        hideLoading();
                        console.log(err);
                        toastr.error('Cập nhật thư mục không thành công');
                    }
                });
            } else {
                toastr.error('Tên thư mục không được để trống');
            }
        }
        // Hiển thị modal thêm danh sách files
        function ShowModalThemFile() {
            var kyhopId = $('#cbx-kyhop').val();
            if (kyhopId && kyhopId > 0) {
                $('#txt-tenthumuc').val('');
                $('#txt-sttthumuc').val('');
                $('.btn-them').removeClass("hidden");
                $('.btn-luu').addClass("hidden");
                $('#themoiFileModal').modal('show');
            } else {
                toastr.warning('Chưa chọn kỳ họp');
            }
        }

        // Action thực hiện nút upload danh sách files
        function UploadFile() {
            var files = $('#file-upload').prop('files');
            if (files.length == 0) {
                toastr.warning('Danh sách file trống');
                return;
            }
            var kyhopId = $('#cbx-kyhop').val();
            if (kyhopId && kyhopId > 0) {
                showLoading();
                var fd = new FormData();
                fd.append('kyhopid', kyhopId);
                fd.append('folderid', currentFolder);
                fd.append('loai', loai);
                for(var index = 0; index < files.length; index++) {
                    fd.append('file_' + index, files[index]);
                }
                $.ajax({
                    url: '/admin/tai-lieu/upload',
                    data: fd,
                    processData: false,
                    contentType: false,
                    type: 'POST',
                    success: function(response){
                        if (response.error_code == '0') {
                            toastr.success('Upload file thành công');
                        } else {
                            toastr.success(response.message);
                        }
                        hideLoading();
                        $('#themoiFileModal').modal('hide');
                        drawDsTaiLieu(currentFolder);
                    },
                    error: function(error) {
                        if (error.status == 403) {
                            toastr.error('Người dùng không có quyền thực hiện thao tác này');
                        } else {
                            toastr.error('Upload file không thành công');
                        }
                        hideLoading();
                    }
                });
            } else {
                toastr.warning('Chưa chọn kỳ họp');
            }
        }

        // Hiển thị thông tin của file lấy từ server: tên file, số quyết định, ngày, ...
        function ViewFile(fileid, event) {
            ActiveFileSelect(event);
            $("#file-info").empty();
            var kyhopId = $('#cbx-kyhop').val();
            if (kyhopId && kyhopId > 0) {
                showLoading();
                $.ajax({
                    type: 'post',
                    url: '/admin/tai-lieu/xem-file',
                    data: {
                        kyhopid: kyhopId,
                        fileid: fileid
                    },
                    success: function(result) {
                        $("#file-info").html(result);
                        hideLoading();
                    },
                    error: function(err) {
                        console.log(err);
                        hideLoading();
                    }
                });
            }
        }

        // Thực hiện set active khi click vào file
        function ActiveFileSelect(event) {
            if (event) {
                $("#ds-tailieu .hover-cursor").removeClass("active");
                $(event.target).closest(".hover-cursor").addClass("active");
            }
        }
        // Thực hiện set active lại file khi thực hiện cập nhật thông tin của file
        function SetFileActive(fileid) {
            $("#row-"+fileid).find('.hover-cursor').addClass("active");
        }
        // Action cập nhật thông tin của file
        function UpdateFile(fileid) {
            var kyhopId = $('#cbx-kyhop').val();
            var tenfile = $('#ten_file').val();
            var sokyhieu = $('#sokyhieu_file').val();
            var ngayvanban = $('#ngay_vanban_file').val();
            var trichyeu = $('#trichyeu_file').val();
            var phanloai = $('#loai_file').val();
            var doituong = $('#doituong_file').val();
            var sttfile = $('#stt_file').val();;
            if (tenfile && tenfile != null && tenfile != '' & tenfile != "") {
                showLoading();
                $.ajax({
                    type: 'post',
                    url: '/admin/tai-lieu/cap-nhat-file',
                    data: {
                        id: fileid,
                        tenfile: tenfile,
                        sokyhieu: sokyhieu,
                        ngayvanban: ngayvanban,
                        trichyeu: trichyeu,
                        phanloai: phanloai,
                        doituong: doituong,
                        stt: sttfile
                    },
                    success: function(response) {
                        hideLoading();
                        if (response.error_code == "0") {
                            toastr.success('Cập nhật file thành công');
                            drawDsTaiLieu(currentFolder, function() {
                                SetFileActive(fileid);
                            });
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(err) {
                        hideLoading();
                        console.log(err);
                        if (err.status == 403) {
                            toastr.error('Người dùng không có quyền thực hiện thao tác này');
                        } else {
                            toastr.error('Cập nhật file không thành công');
                        }
                    }
                });
            } else {
                toastr.error('Tên file không được để trống');
            }
        }
        $(document).ready(function () {
            initKyHopController();
        });
        // Mở file
        function OpenFile(url, filesize, fileExtension) {
            if (fileExtension == "pdf") {
                $('#pdf-viewer').attr('src', url);
                $('#pdf-viewer').attr('type', 'application/pdf');
                $('#viewFileModal').modal('show');
            }
            else {
                const MBtoKB = 1024;
                const KBtoB = 1024;
                if (filesize <= 25 * MBtoKB * KBtoB) {
                    var host = window.location.origin;
                    var urlViewer = "https://view.officeapps.live.com/op/view.aspx?src=" + host + url;
                    $('#pdf-viewer').attr('src', urlViewer);
                    $('#pdf-viewer').attr('type', 'text/html');
                    $('#viewFileModal').modal('show');
                } else {
                    toastr.warning('Dung lượng file lớn hơn 25MB, xin vui lòng tải về để xem');
                }
            }
        }
        // Action Xóa file đính kèm
        function DeleteAttachmentFile(fileid) {
            swal("Bạn có chắc chắn muốn xóa file đã đính kèm không?", {
                buttons: ["Không", "Có"],
                dangerMode: true
            })
            .then((value) => {
                if (value) {
                    $.ajax({
                        type: 'post',
                        url: '/admin/tai-lieu/xoa-file-dinhkem',
                        data: {
                            id: fileid
                        },
                        success: function(response) {
                            if (response.error_code == "0") {
                                toastr.success('Xóa file đính kèm thành công');
                                drawDsTaiLieu(currentFolder, function() {
                                    SetFileActive(fileid);
                                });
                                ViewFile(fileid);
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(err) {
                            console.log(err);
                            if (err.status == 403) {
                                toastr.error('Người dùng không có quyền thực hiện thao tác này');
                            } else {
                                toastr.error('Xóa file đính kèm không thành công');
                            }
                        }
                    });
                }
            })
        }
        // Action Upload file đính kèm
        function UploadFileAttachment(fileid) {
            var files = $('#file-attachment').prop('files');
            if (files.length == 0) {
                toastr.warning('Bạn xin vui lòng chọn file đính kèm');
                return;
            }
            showLoading();
            var fd = new FormData();
            fd.append('fileid', fileid);
            for(var index = 0; index < files.length; index++) {
                fd.append('file_' + index, files[index]);
            }
            $.ajax({
                url: '/admin/tai-lieu/upload-file-attachment',
                data: fd,
                processData: false,
                contentType: false,
                type: 'POST',
                success: function(response){
                    if (response.error_code == '0') {
                        toastr.success('Tải lên file đính kèm thành công');
                    } else {
                        toastr.success(response.message);
                    }
                    hideLoading();
                    drawDsTaiLieu(currentFolder, function() {
                        SetFileActive(fileid);
                    });
                    ViewFile(fileid);
                },
                error: function(error) {
                    if (error.status == 403) {
                        toastr.error('Người dùng không có quyền thực hiện thao tác này');
                    } else {
                        toastr.error('Tải lên file đính kèm không thành công');
                    }
                    hideLoading();
                }
            });
        }
        // Action xóa tài liệu
        function DeleteTaiLieu(tailieuid, isfolder) {
            var messageConfirm = isfolder ? "Bạn có chắc chắn muốn xóa thư mục này không?" : "Bạn có chắc chắn muốn xóa tài liệu này không?";
            var messageSuccess = isfolder ? "Xóa thư mục thành công" : "Xóa tài liệu thành công";
            var messageError = isfolder ? "Xóa thư mục không thành công" : "Xóa tài liệu không thành công";
            swal(messageConfirm, {
                buttons: ["Không", "Có"],
                dangerMode: true
            })
            .then((value) => {
                if (value) {
                    $.ajax({
                        type: 'post',
                        url: '/admin/tai-lieu/xoa-tailieu',
                        data: {
                            id: tailieuid
                        },
                        success: function(response) {
                            if (response.error_code == "0") {
                                toastr.success(messageSuccess);
                                drawDsTaiLieu(currentFolder);
                                $("#file-info").empty();
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(err) {
                            console.log(err);
                            if (err.status == 403) {
                                toastr.error('Người dùng không có quyền thực hiện thao tác này');
                            } else {
                                toastr.error(messageError);
                            }
                        }
                    });
                }
            });
        }
    </script>
@stop


