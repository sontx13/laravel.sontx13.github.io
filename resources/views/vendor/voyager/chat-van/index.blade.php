<!DOCTYPE html>
@extends('voyager::master')

@section('page_title', 'Chất vấn')

@section('page_header')
    <div class="container-fluid card">
        <h1>
            Chất vấn
        </h1>
        <div class="">
            <div class="row">
                <div class="col-sm-4">
                    <div class="row">
                        <div class="col-sm-4">
                            <span>Khóa họp</span>
                        </div>
                        <div class="col-sm-8">
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
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="row">
                        <div class="col-sm-4">
                            <span>Kỳ họp</span>
                        </div>
                        <div class="col-sm-8">
                            <select id="cbx-kyhop" class="select2 form-control" onchange="OnChangeKyHop()">
                                <option value="0" selected="selected">-- Chưa chọn --</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <button class="btn btn-success" onclick="ShowChatVan(0)">Trình Chiếu</button>
                </div>
            </div>
        </div>
    </div>

    {{-- modal thêm người trả lời --}}
    <div class="modal fade" id="themoiPhienChatVanModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <span class="modal-title" id="themmoiPhienChatVanModalLabel">Thêm mới phiên chất vấn</span>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-3">
                        <span>Người trả lời</span><span class="required">(*)</span>
                    </div>
                    <div class="col-sm-9">
                        <input type="text" id="txt-idphienchatvan" class="form-control hidden">
                        <input type="text" id="txt-idtgphienchatvan" class="form-control hidden">
                        <textarea type="text" id="txt-nguoitraloi" class="form-control"></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <span>Thời gian </span><span class="required">(*)</span>
                    </div>
                    <div class="col-sm-9">
                        <input type="number" min="0" id="txt-tgtraloi" class="form-control">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <span>STT</span>
                    </div>
                    <div class="col-sm-9">
                        <input type="number" min="0" id="txt-sttnguoitraloi" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
              <button type="button" class="btn btn-primary btn-them-phienchatvan" onclick="ThemPhienChatVan()">Thêm</button>
              <button type="button" class="btn btn-primary btn-luu-phienchatvan hidden" onclick="LuuPhienChatVan()">Lưu</button>
            </div>
          </div>
        </div>
    </div>

    <div id="divModalChatVan">
    </div>
@stop

@section('content')
    <div id="ds-ngayhop" class="card-body">
    </div>
    <div id="ngayhop-chatvan" class="card-body">
    </div>
@stop
@include('voyager::loading.spin')
<link rel="stylesheet" href="/css/style.css">
@section('javascript')
    <script src="/js/sweetalert.min.js"></script>
    <script src="/js/app.js"></script>
    <script>
        // event khi thay đổi khóa họp
        function OnChangeKhoaHop() {
            $("#cbx-kyhop").val(0).trigger('change');
            $("#ngayhop-chatvan").empty();
            initKyHopController();
        }
        // event khi thay đổi kỳ họp
        function OnChangeKyHop() {
            //drawDsNgayHop();
            var kyhopId = $('#cbx-kyhop').val();
            if (kyhopId && kyhopId > 0) {
                drawChatVanNgayHop(0, kyhopId);
            }
        }
        // vẽ tab danh sách ngày họp
        function drawDsNgayHop() {
            $("#ds-ngayhop").empty();
            var kyhopId = $('#cbx-kyhop').val();
            if (kyhopId && kyhopId > 0) {
                $.ajax({
                    type: 'post',
                    url: '/admin/chat-van/ngay-hop',
                    data: {
                        kyhopid: kyhopId
                    },
                    success: function(result) {
                        $("#ds-ngayhop").html(result);
                        console.log(result);
                    },
                    error: function(err) {
                        console.log(err);
                    }
                });
            }
        }
        // vẽ nội dung từng tab theo khi click vào tab
        function drawChatVanNgayHop(thoigianId, kyhopId) {
            showLoading();
            $("#ngayhop-chatvan").empty();
            // toogleActiveTab(thoigianId);
            $.ajax({
                    type: 'post',
                    url: '/admin/chat-van/chatvan-ngayhop',
                    data: {
                        thoigianid: thoigianId,
                        kyhopid: kyhopId
                    },
                    success: function(result) {
                        $("#ngayhop-chatvan").html(result);
                        hideLoading();
                    },
                    error: function(err) {
                        console.log(err);
                        hideLoading();
                    }
                });
        }
        // active tab được chọn
        function toogleActiveTab(thoigianId) {
            $('#ds-ngayhop').find("li").removeClass("active")
            $("#tab-thoigian-"+ thoigianId).closest("li").addClass("active");
        }
        /////////////////////////////////////////
        //REGION ------Phiên chất vấn-----------
        // Thêm mới phiên chất vấn - mở modal thêm phiên chất vấn
        function ThemMoiPhienChatVan(kyhopid, thoigianid) {
            var kyhopId = $('#cbx-kyhop').val();
            if (kyhopId && kyhopId > 0) {
                SetValuePhienChatVanModal('Thêm mới phiên chất vấn', "", "", "", 0, thoigianid);
                $('.btn-them-phienchatvan').removeClass("hidden");
                $('.btn-luu-phienchatvan').addClass("hidden");
                $('#themoiPhienChatVanModal').modal('show');
            } else {
                toastr.warning('Chưa chọn kỳ họp');
            }
        }
        // Sửa giá trị của của các thành phần trong modal
        function SetValuePhienChatVanModal(title, nguoitraloi, thoigian, stt, id, idthoigian) {
            $('#themmoiPhienChatVanModalLabel').text(title);
            $('#txt-nguoitraloi').val(nguoitraloi);
            $('#txt-tgtraloi').val(thoigian);
            $('#txt-sttnguoitraloi').val(stt);
            $('#txt-idphienchatvan').val(id);
            $('#txt-idtgphienchatvan').val(idthoigian)
        }
        // Action thêm phiên chất vấn
        function ThemPhienChatVan() {
            var kyhopId = $('#cbx-kyhop').val();
            var thoigianId = 0;//$('#txt-idtgphienchatvan').val();
            var nguoitraloi = $('#txt-nguoitraloi').val();
            var tgphienchatvan = $('#txt-tgtraloi').val();
            var sttphienchatvan = $('#txt-sttnguoitraloi').val();
            if (ValidatePhienChatVan(nguoitraloi, tgphienchatvan)) {
                $.ajax({
                    type: 'post',
                    url: '/admin/phien-chat-van/them-moi',
                    data: {
                        kyhopid: kyhopId,
                        nguoitraloi: nguoitraloi,
                        thoigian: tgphienchatvan,
                        stt: sttphienchatvan,
                        thoigianid: thoigianId
                    },
                    success: function(response) {
                        if (response.error_code == "0") {
                            toastr.success('Thêm mới phiên chất vấn thành công');
                            $('#themoiPhienChatVanModal').modal('hide');
                            drawChatVanNgayHop(thoigianId, kyhopId);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(err) {
                        console.log(err);
                        if (err.status == 403) {
                            toastr.error('Người dùng không có quyền thực hiện thao tác này');
                        } else {
                            toastr.error('Thêm mới phiên chất vấn không thành công');
                        }
                    }
                });
            }
        }
        // Validate phiên chất vấn
        function ValidatePhienChatVan(nguoitraloi, tgphienchatvan) {
            if (!nguoitraloi || nguoitraloi == "" || nguoitraloi == '') {
                toastr.warning('Người trả lời không được để trống');
                return false;
            }
            if (!tgphienchatvan || tgphienchatvan == "" || tgphienchatvan == '') {
                toastr.warning('Thời gian phiên chất vấn không được để trống');
                return false;
            }
            return true;
        }
        // Action sửa phiên chất vấn
        function SuaPhienChatVan(id, nguoitraloi, thoigianphien, stt, idthoigian) {
            SetValuePhienChatVanModal("Sửa phiên chất vấn", nguoitraloi, thoigianphien, stt, id, idthoigian);
            $('.btn-them-phienchatvan').addClass("hidden");
            $('.btn-luu-phienchatvan').removeClass("hidden");
            $('#themoiPhienChatVanModal').modal('show');
        }
        // Action lưu phiên chất vấn
        function LuuPhienChatVan() {
            var kyhopId = $('#cbx-kyhop').val();
            var thoigianId = 0;//$('#txt-idtgphienchatvan').val();
            var idPhienChatVan = $('#txt-idphienchatvan').val();
            var nguoitraloi = $('#txt-nguoitraloi').val();
            var tgphienchatvan = $('#txt-tgtraloi').val();
            var sttphienchatvan = $('#txt-sttnguoitraloi').val();
            if (ValidatePhienChatVan(nguoitraloi, tgphienchatvan)) {
                $.ajax({
                    type: 'post',
                    url: '/admin/phien-chat-van/cap-nhat',
                    data: {
                        id: idPhienChatVan,
                        kyhopid: kyhopId,
                        nguoitraloi: nguoitraloi,
                        thoigian: tgphienchatvan,
                        stt: sttphienchatvan,
                        thoigianid: thoigianId
                    },
                    success: function(response) {
                        if (response.error_code == "0") {
                            toastr.success('Cập nhật phiên chất vấn thành công');
                            $('#themoiPhienChatVanModal').modal('hide');
                            drawChatVanNgayHop(thoigianId, kyhopId);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(err) {
                        console.log(err);
                        if (err.status == 403) {
                            toastr.error('Người dùng không có quyền thực hiện thao tác này');
                        } else  {
                            toastr.error('Cập nhật phiên chất vấn không thành công');
                        }
                    }
                });
            }
        }
        // Action xóa phiên chất vấn
        function XoaPhienChatVan(id) {
            swal("Bạn có chắc chắn muốn xóa người trả lời?", {
                buttons: ["Không", "Có"],
                dangerMode: true
            })
            .then((value) => {
                if (value) {
                    $.ajax({
                        type: 'post',
                        url: '/admin/phien-chat-van/xoa',
                        data: {
                            id: id
                        },
                        success: function(response) {
                            if (response.error_code == "0") {
                                toastr.success('Xóa người trả lời thành công');
                                var kyhopId = $('#cbx-kyhop').val();
                                var thoigianId = 0;//$('#ds-ngayhop li.active .thoigian-id').text();
                                drawChatVanNgayHop(thoigianId, kyhopId);
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(err) {
                            console.log(err);
                            if (err.status == 403) {
                                toastr.error('Người dùng không có quyền thực hiện thao tác này');
                            } else {
                                toastr.error('Xóa người trả lời không thành công');
                            }
                        }
                    });
                }
            })
        }
        // Action bắt đầu phiên chất vấn
        function BatDauPhienChatVan(id) {
            swal("Bạn có chắc chắn muốn bắt đầu phiên chất vấn?", {
                buttons: ["Không", "Có"],
                dangerMode: false
            })
            .then((value) => {
                if (value) {
                    var kyhopId = $('#cbx-kyhop').val();
                    $.ajax({
                        type: 'post',
                        url: '/admin/phien-chat-van/batdau',
                        data: {
                            id: id,
                            kyhopid: kyhopId
                        },
                        success: function(response) {
                            if (response.error_code == "0") {
                                toastr.success('Bắt đầu phiên chất vấn thành công');
                                var thoigianId = 0;//$('#ds-ngayhop li.active .thoigian-id').text();
                                drawChatVanNgayHop(thoigianId, kyhopId);
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(err) {
                            console.log(err);
                            if (err.status == 403) {
                                toastr.error('Người dùng không có quyền thực hiện thao tác này');
                            } else {
                                toastr.error('Bắt đầu phiên chất vấn không thành công');
                            }
                        }
                    });
                }
            })
        }
        // Action kết thúc phiên chất vấn
        function KetThucPhienChatVan(id) {
            swal("Bạn có chắc chắn muốn kết thúc phiên chất vấn?", {
                buttons: ["Không", "Có"],
                dangerMode: false
            })
            .then((value) => {
                if (value) {
                    $.ajax({
                        type: 'post',
                        url: '/admin/phien-chat-van/ketthuc',
                        data: {
                            id: id
                        },
                        success: function(response) {
                            if (response.error_code == "0") {
                                toastr.success('Kết thúc phiên chất vấn thành công');
                                var thoigianId = 0;//$('#ds-ngayhop li.active .thoigian-id').text();
                                var kyhopId = $('#cbx-kyhop').val();
                                drawChatVanNgayHop(thoigianId, kyhopId);
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(err) {
                            console.log(err);
                            if (err.status == 403) {
                                toastr.error('Người dùng không có quyền thực hiện thao tác này');
                            } else {
                                toastr.error('Kết thúc phiên chất vấn không thành công');
                            }
                        }
                    });
                }
            })
        }
        /////////////////////////////////////////
        //END REGION ------Phiên chất vấn-----------


        /////////////////////////////////////////
        //REGION ------Đại biểu chất vấn-----------
        // Thêm mới đại biểu chất vấn - mở modal thêm mới đại biểu chất vấn
        function ThemMoiDaiBieuChatVan(kyhopid, thoigianid, idphienchatvan) {
            var kyhopId = $('#cbx-kyhop').val();
            if (kyhopId && kyhopId > 0) {
                $.ajax({
                    type: 'post',
                    url: '/admin/chat-van/modal-themmoi',
                    data: {
                        id: 0,
                        kyhopid: kyhopId,
                        phienchatvanid: idphienchatvan
                    },
                    success: function(response) {
                        $('#divModalChatVan').html(response);
                        initComboboxDaiBieu();
                        $('#modalChatVan').modal('show');
                    },
                    error: function(err) {
                        console.log(err);
                        toastr.error('Mở modal thêm/sửa đại biểu chất vấn không thành công');
                    }
                });
            } else {
                toastr.warning('Chưa chọn kỳ họp');
            }
        }
        // Action thêm mới đại biểu chất vấn
        function ThemMoiChatVan(phienchatvanid) {
            var kyhopId = $('#cbx-kyhop').val();
            var daibieuId = $('#cbx-daibieu').val();
            var noidung = $('#txt-noidung').val();
            var thoigian = $('#txt-tgchatvan').val();
            var sttchatvan =  $('#txt-sttchatvan').val();
            if (ValidateChatVan(daibieuId, thoigian)) {
                $.ajax({
                    type: 'post',
                    url: '/admin/chat-van/them-moi',
                    data: {
                        kyhopid: kyhopId,
                        daibieuid: daibieuId,
                        noidung: noidung,
                        thoigian: thoigian,
                        stt: sttchatvan,
                        phienchatvanId: phienchatvanid
                    },
                    success: function(response) {
                        if (response.error_code == "0") {
                            toastr.success('Thêm mới đại biểu chất vấn thành công');
                            $('#modalChatVan').modal('hide');
                            var thoigianId = 0;//$('#ds-ngayhop li.active .thoigian-id').text();
                            drawChatVanNgayHop(thoigianId, kyhopId);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(err) {
                        console.log(err);
                        if (err.status == 403) {
                            toastr.error('Người dùng không có quyền thực hiện thao tác này');
                        } else {
                            toastr.error('Thêm mới đại biểu chất vấn không thành công');
                        }
                    }
                });
            }
        }
        // Validate dữ liệu thêm mới đại biểu chất vấn
        function ValidateChatVan(daibieu, thoigian) {
            if (!daibieu || daibieu == "" || daibieu == '') {
                toastr.warning('Đại biểu chất vấn không được để trống');
                return false;
            }
            if (!thoigian || thoigian == "" || thoigian == '') {
                toastr.warning('Thời gian chất vấn không được để trống');
                return false;
            }
            return true;
        }
        //Action sửa chất vấn
        function SuaChatVan(id) {
            $.ajax({
                    type: 'post',
                    url: '/admin/chat-van/modal-themmoi',
                    data: {
                        id: id,
                        kyhopid: $('#cbx-kyhop').val()
                    },
                    success: function(response) {
                        $('#divModalChatVan').html(response);
                        initComboboxDaiBieu();
                        $('#modalChatVan').modal('show');
                    },
                    error: function(err) {
                        console.log(err);
                        toastr.error('Mở modal thêm/sửa đại biểu chất vấn không thành công');
                    }
                });
        }
        // Action lưu chất vấn
        function LuuChatVan(chatvanid) {
            var daibieuId = $('#cbx-daibieu').val();
            var noidung = $('#txt-noidung').val();
            var thoigian = $('#txt-tgchatvan').val();
            var sttchatvan =  $('#txt-sttchatvan').val();
            if (ValidateChatVan(daibieuId, thoigian)) {
                $.ajax({
                    type: 'post',
                    url: '/admin/chat-van/cap-nhat',
                    data: {
                        id: chatvanid,
                        daibieuid: daibieuId,
                        noidung: noidung,
                        thoigian: thoigian,
                        stt: sttchatvan
                    },
                    success: function(response) {
                        if (response.error_code == "0") {
                            toastr.success('Cập nhật đại biểu chất vấn thành công');
                            $('#modalChatVan').modal('hide');
                            var thoigianId = 0;//$('#ds-ngayhop li.active .thoigian-id').text();
                            var kyhopId = $('#cbx-kyhop').val();
                            drawChatVanNgayHop(thoigianId, kyhopId);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(err) {
                        console.log(err);
                        if (err.status == 403) {
                            toastr.error('Người dùng không có quyền thực hiện thao tác này');
                        } else {
                            toastr.error('Cập nhật đại biểu chất vấn không thành công');
                        }
                    }
                });
            }
        }
        // Action duyệt đại biểu chất vấn
        function DuyetChatVan(id) {
            swal("Bạn có chắc chắn muốn duyệt đại biểu chất vấn?", {
                buttons: ["Không", "Có"],
                dangerMode: false
            })
            .then((value) => {
                if (value) {
                    $.ajax({
                        type: 'post',
                        url: '/admin/chat-van/duyet',
                        data: {
                            id: id
                        },
                        success: function(response) {
                            if (response.error_code == "0") {
                                toastr.success('Duyệt chất vấn thành công');
                                var thoigianId = 0;// $('#ds-ngayhop li.active .thoigian-id').text();
                                var kyhopId = $('#cbx-kyhop').val();
                                drawChatVanNgayHop(thoigianId, kyhopId);
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(err) {
                            console.log(err);
                            if (err.status == 403) {
                                toastr.error('Người dùng không có quyền thực hiện thao tác này');
                            } else {
                                toastr.error('Duyệt chất vấn không thành công');
                            }
                        }
                    });
                }
            })
        }
        // Action bắt đầu đại biểu chất vấn
        function BatDauChatVan(id) {
            swal("Bạn có chắc chắn muốn bắt đầu chất vấn?", {
                buttons: ["Không", "Có"],
                dangerMode: false
            })
            .then((value) => {
                if (value) {
                    $.ajax({
                        type: 'post',
                        url: '/admin/chat-van/bat-dau',
                        data: {
                            id: id
                        },
                        success: function(response) {
                            if (response.error_code == "0") {
                                toastr.success('Bắt đầu chất vấn thành công');
                                var thoigianId = 0;//$('#ds-ngayhop li.active .thoigian-id').text();
                                var kyhopId = $('#cbx-kyhop').val();
                                drawChatVanNgayHop(thoigianId, kyhopId);
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(err) {
                            console.log(err);
                            if (err.status == 403) {
                                toastr.error('Người dùng không có quyền thực hiện thao tác này');
                            } else {
                                toastr.error('Bắt đầu chất vấn không thành công');
                            }
                        }
                    });
                }
            })
        }
        // Action kết thúc đại biểu chất vấn
        function KetThucChatVan(id) {
            swal("Bạn có chắc chắn muốn kết thúc chất vấn?", {
                buttons: ["Không", "Có"],
                dangerMode: true
            })
            .then((value) => {
                if (value) {
                    $.ajax({
                        type: 'post',
                        url: '/admin/chat-van/ket-thuc',
                        data: {
                            id: id
                        },
                        success: function(response) {
                            if (response.error_code == "0") {
                                toastr.success('Kết thúc chất vấn thành công');
                                var thoigianId = 0;//$('#ds-ngayhop li.active .thoigian-id').text();
                                var kyhopId = $('#cbx-kyhop').val();
                                drawChatVanNgayHop(thoigianId, kyhopId);
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(err) {
                            console.log(err);
                            if (err.status == 403) {
                                toastr.error('Người dùng không có quyền thực hiện thao tác này');
                            } else {
                                toastr.error('Kết thúc chất vấn không thành công');
                            }
                        }
                    });
                }
            })
        }
        // Action xóa đại biểu chất vấn
        function XoaChatVan(id) {
            swal("Bạn có chắc chắn muốn xóa chất vấn?", {
                buttons: ["Không", "Có"],
                dangerMode: true
            })
            .then((value) => {
                if (value) {
                    $.ajax({
                        type: 'post',
                        url: '/admin/chat-van/xoa',
                        data: {
                            id: id
                        },
                        success: function(response) {
                            if (response.error_code == "0") {
                                toastr.success('Xóa chất vấn thành công');
                                var kyhopId = $('#cbx-kyhop').val();
                                var thoigianId = 0;
                                drawChatVanNgayHop(thoigianId, kyhopId);
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(err) {
                            console.log(err);
                            if (err.status == 403) {
                                toastr.error('Người dùng không có quyền thực hiện thao tác này');
                            } else {
                                toastr.error('Xóa chất vấn không thành công');
                            }
                        }
                    });
                }
            })
        }
        function ShowChatVan(id) {
            var kyhopId = $('#cbx-kyhop').val();
            if (kyhopId > 0) {
                // Chuyển sang view trình chiếu chất vấn
                var url = window.location.origin + '/admin/chat-van/' +kyhopId+ '/trinhchieu/' + id;
                window.open(url, "_blank");
            } else {
                toastr.warning('Xin vui lòng chọn kỳ họp');
            }
        }
        // Khởi tạo select2 kỳ họp theo khóa họp được chọn
        function initKyHopController() {
            var khoahopId = $('#cbx-khoahop').val();
            $('#cbx-kyhop').select2({
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
                        }
                }
            });
        }
        // Khởi tạo select2 danh sách đại biểu theo kỳ họp được chọn
        function initComboboxDaiBieu() {
            $('#cbx-daibieu').select2();
        }
        $(document).ready(function () {
            initKyHopController();
        });
    </script>
@stop
