<!DOCTYPE html>
@extends('voyager::master')

@section('page_title', 'Điểm danh')

@section('page_header')
    <div class="card">
        <h1>
            Điểm danh
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
                    @can('edit_diem-danh')
                        <button class="btn btn-primary" onclick="LuuDiemDanhThuCong()">Điểm Danh</button>
                    @endcan
                    <button class="btn btn-success" onclick="ShowDiemDanh()">Trình Chiếu</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div id="ds-buoihop" class="page-content browse">
    </div>
    <div id="buoihop-diemdanh">
    </div>
@stop
@include('voyager::loading.spin')
<link rel="stylesheet" href="/css/style.css">
@section('javascript')
    <script src="/js/app.js"></script>
    <script src="/js/firebase-config.js"></script>
    <script src="/js/firebase-app.js"></script>
    <script src="/js/firebase-database.js"></script>
    <script>
        var tenantId = "<?php Print($tenantId); ?>";
        var useFirebase = "<?php echo setting('site.usefirebase'); ?>";
        function OnChangeKhoaHop() {
            $("#cbx-kyhop").val(0).trigger('change');
            initKyHopController();
        }

        function OnChangeKyHop() {
            drawDsBuoiHop();
        }

        function drawDsBuoiHop() {
            $("#ds-buoihop").empty();
            $("#buoihop-diemdanh").empty();
            var kyhopId = $('#cbx-kyhop').val();
            if (kyhopId && kyhopId > 0) {
                $.ajax({
                    type: 'post',
                    url: '/admin/diem-danh/buoi-hop',
                    data: {
                        kyhopid: kyhopId
                    },
                    success: function(result) {
                        $("#ds-buoihop").html(result);
                        $('#ds-buoihop').find("li.active a").click();
                        console.log(result);
                    },
                    error: function(err) {
                        console.log(err);
                    }
                });
            }
        }

        function drawDiemDanhBuoiHop(lichhopId, kyhopId, isValidDiemDanhTime, event) {
            showLoading();
            if (isValidDiemDanhTime) {
                if (useFirebase == "1") {
                    if (!firebase.apps.length) {
                        firebase.initializeApp(firebaseConfig);
                    }
                    var database = firebase.database();
                    const fetchDiemDanhs = database.ref(tenantId + "/diemdanhs/" + lichhopId);
                    fetchDiemDanhs.on("child_added", function (snapshot) {
                        const messages = snapshot.val();
                        HandleEventDiemDanh(messages);
                        console.log("child_added" + JSON.stringify(messages));
                    });
                    fetchDiemDanhs.on("child_changed", function (snapshot) {
                        const messages = snapshot.val();
                        HandleEventDiemDanh(messages);
                        console.log("child_changed" + JSON.stringify(messages));
                    });
                }
            }
            $("#buoihop-diemdanh").empty();
            toogleActiveTab(event);
            $.ajax({
                    type: 'post',
                    url: '/admin/diem-danh/diemdanh-buoihop',
                    data: {
                        lichhopid: lichhopId,
                        kyhopid: kyhopId
                    },
                    success: function(result) {
                        $("#buoihop-diemdanh").html(result);
                        hideLoading();
                    },
                    error: function(err) {
                        console.log(err);
                        hideLoading();
                    }
                });
        }

        function toogleActiveTab(event) {
            $('#ds-buoihop').find("li").removeClass("active")
            $(event.target).closest("li").addClass("active");
        }

        function toggleDiemDanh(event) {
            var rowParent = $(event.target).closest(".daibieu-diemdanh");
            $(rowParent).find(".daibieu-id").val();
            $(rowParent).find(".btn-diemdanh").toggleClass("hidden");
            $(rowParent).find(".span-thucong").removeClass("hidden");
            $(rowParent).find(".span-tudong").addClass("hidden");
        }
        function HandleEventDiemDanh(message) {
            var userId = message.userid;
            var trangthai = message.trangthai;
            // Điểm danh bằng App
            if (trangthai == 2) {
                var rowDaibieu = $('#dabieu-id-' + userId);
                if (rowDaibieu && rowDaibieu.length > 0) {
                    $(rowDaibieu).find(".btn-diemdanh.co-mat").removeClass("hidden");
                    var btnVangMat = $(rowDaibieu).find(".btn-diemdanh.vang-mat");
                    if (!$(btnVangMat).hasClass('hidden')) {
                        $(btnVangMat).addClass("hidden");
                    }
                    var spanThuCong = $(rowDaibieu).find(".span-thucong");
                    if (!$(spanThuCong).hasClass('hidden')) {
                        $(spanThuCong).addClass("hidden");
                    }
                    $(rowDaibieu).find(".span-tudong").removeClass("hidden");
                }
            }
        }
        function LuuDiemDanhThuCong() {
            //lấy thông tin buổi họp
            var tabActive = $('#ds-buoihop li.active');
            var ngayStr = $(tabActive).find('.ngay-dien-ra').text();
            var buoiHop = $(tabActive).find('.buoi-hop').text();
            var lichhopId = $(tabActive).find('.lichhop-id').text();
            if (validateThoiGianDiemDanh(buoiHop, ngayStr)) {
                var dsDaiBieuThuCongs = $('#buoihop-diemdanh .daibieu-diemdanh .span-thucong:not(.hidden)');
                if ($(dsDaiBieuThuCongs).length > 0) {
                    var lsDaiBieus = [];
                    $(dsDaiBieuThuCongs).each(function() {
                        $(this).addClass( "foo" );
                        var userId = $(this).closest(".daibieu-diemdanh").find(".daibieu-id").text();
                        var btnDiemDanh = $(this).closest(".daibieu-diemdanh").find(".btn-diemdanh:not(.hidden)");
                        var trangthai = $(btnDiemDanh).hasClass("co-mat") ? 3 : 1;
                        lsDaiBieus.push({
                            userId: userId,
                            trangThai: trangthai
                        })
                    });
                    $.ajax({
                        type: 'post',
                        url: '/admin/diem-danh/luu-thu-cong',
                        data: {
                            lichhopid: lichhopId,
                            lsdaibieus: JSON.stringify(lsDaiBieus)
                        },
                        success: function(response) {
                            if (response.error_code == '0') {
                                toastr.success('Thực hiện điểm danh thành công');
                                console.log(response);
                            } else {
                                toastr.error('Thực hiện điểm danh không thành công');
                            }
                        },
                        error: function(err) {
                            if (err.status == 403) {
                                toastr.error('Người dùng không có quyền thực hiện thao tác này');
                            } else {
                                toastr.error('Thực hiện điểm danh không thành công');
                            }
                            console.log(err);
                        }
                    });
                } else {
                    toastr.warning('Không có đại biểu điểm danh thủ công');
                }
                console.log(dsDaiBieuThuCongs);
            }
        }

        function validateThoiGianDiemDanh(buoiHop, ngayDienRa) {
            var isValid = false;
            var ngay = new Date(ngayDienRa);
            var now = new Date();
            if (ngay.getDate() == now.getDate()
                && ngay.getMonth() == now.getMonth()
                && ngay.getYear() == now.getYear()) {
                // Sáng
                if (buoiHop == "0") {
                    var gio = now.getHours();
                    if (gio >= 6 && gio <= 12) {
                        isValid = true;
                    } else {
                        toastr.warning('Giờ hiện tại không phải buổi họp sáng');
                    }
                }
                // Chiều
                else {
                    var gio = now.getHours();
                    if (gio >= 12 && gio <= 18) {
                        isValid = true;
                    } else {
                        toastr.warning('Giờ hiện tại không phải buổi họp chiều');
                    }
                }
            }
            else {
                toastr.warning('Ngày hiện tại khác ngày họp');
            }
            return isValid;
        }

        function ShowDiemDanh() {
            var kyhopId = $('#cbx-kyhop').val();
            if (kyhopId > 0) {
                // Chuyển sang view trình chiếu điểm danh
                var url = window.location.origin + '/admin/diem-danh/trinhchieu/' + kyhopId;
                window.open(url, "_blank");
            } else {
                toastr.warning('Xin vui lòng chọn kỳ họp');
            }
        }

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
        $(document).ready(function () {
            initKyHopController();
        });
    </script>
@stop
