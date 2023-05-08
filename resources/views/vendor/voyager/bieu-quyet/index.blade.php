<!DOCTYPE html>
@extends('voyager::master')

@section('page_title', 'Biểu quyết')

@section('page_header')
    <div class="card">
        <h1>
            Biểu quyết
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
                    @can('add_bieu-quyet')
                        <button class="btn btn-primary" onclick="ShowModalThemMoiBieuQuyet()">Thêm mới</button>
                    @endcan
                    <button class="btn btn-success" onclick="ShowTrinhChieuBieuQuyet(0)">Trình chiếu</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div id="ds-bieuquyet" class="page-content browse container-fluid">
    </div>
    <div class="modal fade" id="themoiModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <span class="modal-title" id="themmoiModalLabel">Thêm biểu quyết</span>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-3">
                        <span>Biểu quyết </span><span class="required">(*)</span>
                    </div>
                    <div class="col-sm-9">
                        <input type="text" id="txt-idbieuquyet" class="form-control hidden">
                        <textarea type="text" id="txt-bieuquyet" class="form-control"></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <span>Thời gian </span><span class="required">(*)</span>
                    </div>
                    <div class="col-sm-9">
                        <input type="number" min="0" id="txt-tgbieuquyet" class="form-control">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <span>STT</span>
                    </div>
                    <div class="col-sm-9">
                        <input type="number" min="0" id="txt-sttbieuquyet" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary btn-them" onclick="ThemBieuQuyet()">Thêm</button>
                <button type="button" class="btn btn-primary btn-luu hidden" onclick="LuuBieuQuyet()">Lưu</button>
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
        function OnChangeKhoaHop() {
            $("#cbx-kyhop").val(0).trigger('change');
            initKyHopController();
        }

        function OnChangeKyHop() {
            drawDsBieuQuyet();
        }

        function drawDsBieuQuyet() {
            $("#ds-bieuquyet").empty();
            var kyhopId = $('#cbx-kyhop').val();
            if (kyhopId && kyhopId > 0) {
                showLoading();
                $.ajax({
                    type: 'post',
                    url: '/admin/bieu-quyet/danh-sach',
                    data: {
                        kyhopid: kyhopId
                    },
                    success: function(result) {
                        $("#ds-bieuquyet").html(result);
                        hideLoading();
                    },
                    error: function(err) {
                        console.log(err);
                        hideLoading();
                    }
                });
            }
        }

        function ShowModalThemMoiBieuQuyet() {
            var kyhopId = $('#cbx-kyhop').val();
            if (kyhopId && kyhopId > 0) {
                SetValueModal('Thêm mới biểu quyết', "", "", "", 0);
                $('.btn-them').removeClass("hidden");
                $('.btn-luu').addClass("hidden");
                $('#themoiModal').modal('show');
            } else {
                toastr.warning('Chưa chọn kỳ họp');
            }

        }

        function ShowTrinhChieuBieuQuyet(id) {
            var kyhopId = $('#cbx-kyhop').val();
            if (kyhopId && kyhopId > 0) {
                // Chuyển sang view trình chiếu biểu quyết
                var url = window.location.origin + '/admin/bieu-quyet/'+ kyhopId +'/trinhchieu/' + id;
                window.open(url, "_blank");
            } else {
                toastr.warning('Chưa chọn kỳ họp');
            }
        }

        function ThemBieuQuyet() {
            var kyhopId = $('#cbx-kyhop').val();
            var noidungbieuquyet = $('#txt-bieuquyet').val();
            var tgbieuquyet = $('#txt-tgbieuquyet').val();
            var sttbieuquyet = $('#txt-sttbieuquyet').val();
            if (ValidateBieuQuyet(noidungbieuquyet, tgbieuquyet)) {
                $.ajax({
                    type: 'post',
                    url: '/admin/bieu-quyet/them-moi',
                    data: {
                        kyhopid: kyhopId,
                        bieuquyet: noidungbieuquyet,
                        thoigian: tgbieuquyet,
                        stt: sttbieuquyet
                    },
                    success: function(response) {
                        if (response.error_code == "0") {
                            toastr.success('Thêm mới biểu quyết thành công');
                            $('#themoiModal').modal('hide');
                            drawDsBieuQuyet();
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(err) {
                        console.log(err);
                        if (err.status == 403) {
                            toastr.error('Người dùng không có quyền thực hiện thao tác này');
                        } else {
                            toastr.error('Thêm mới biểu quyết không thành công');
                        }
                    }
                });
            }
        }

        function LuuBieuQuyet() {
            var kyhopId = $('#cbx-kyhop').val();
            var idBieuQuyet = $("#txt-idbieuquyet").val();
            var noidungbieuquyet = $('#txt-bieuquyet').val();
            var tgbieuquyet = $('#txt-tgbieuquyet').val();
            var sttbieuquyet = $('#txt-sttbieuquyet').val();
            if (ValidateBieuQuyet(noidungbieuquyet, tgbieuquyet)) {
                $.ajax({
                    type: 'post',
                    url: '/admin/bieu-quyet/cap-nhat',
                    data: {
                        id: idBieuQuyet,
                        kyhopid: kyhopId,
                        bieuquyet: noidungbieuquyet,
                        thoigian: tgbieuquyet,
                        stt: sttbieuquyet
                    },
                    success: function(response) {
                        if (response.error_code == "0") {
                            toastr.success('Cập nhật biểu quyết thành công');
                            $('#themoiModal').modal('hide');
                            drawDsBieuQuyet();
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(err) {
                        console.log(err);
                        if (err.status == 403) {
                            toastr.error('Người dùng không có quyền thực hiện thao tác này');
                        } else {
                            toastr.error('Cập nhật biểu quyết không thành công');
                        }
                    }
                });
            }
        }

        function XoaBieuQuyet(id) {
            swal("Bạn có chắc chắn muốn xóa biểu quyết?", {
                buttons: ["Không", "Có"],
                dangerMode: true
            })
            .then((value) => {
                if (value) {
                    $.ajax({
                        type: 'post',
                        url: '/admin/bieu-quyet/xoa',
                        data: {
                            id: id
                        },
                        success: function(response) {
                            if (response.error_code == "0") {
                                toastr.success('Xóa biểu quyết thành công');
                                drawDsBieuQuyet();
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(err) {
                            console.log(err);
                            if (err.status == 403) {
                                toastr.error('Người dùng không có quyền thực hiện thao tác này');
                            } else {
                                toastr.error('Xóa biểu quyết không thành công');
                            }
                        }
                    });
                }
            })
        }

        function BatDauBieuQuyet(id) {
            swal("Bạn có chắc chắn muốn bắt đầu biểu quyết?", {
                buttons: ["Không", "Có"],
                dangerMode: false
            })
            .then((value) => {
                if (value) {
                    var kyhopId = $('#cbx-kyhop').val();
                    $.ajax({
                        type: 'post',
                        url: '/admin/bieu-quyet/batdau',
                        data: {
                            id: id,
                            kyhopid: kyhopId
                        },
                        success: function(response) {
                            if (response.error_code == "0") {
                                toastr.success('Bắt đầu biểu quyết thành công');
                                drawDsBieuQuyet();
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(err) {
                            console.log(err);
                            if (err.status == 403) {
                                toastr.error('Người dùng không có quyền thực hiện thao tác này');
                            } else {
                                toastr.error('Bắt đầu biểu quyết không thành công');
                            }
                        }
                    });
                }
            })
        }

        function KetThucBieuQuyet(id) {
            swal("Bạn có chắc chắn muốn kết thúc biểu quyết?", {
                buttons: ["Không", "Có"],
                dangerMode: false
            })
            .then((value) => {
                if (value) {
                    $.ajax({
                        type: 'post',
                        url: '/admin/bieu-quyet/ketthuc',
                        data: {
                            id: id
                        },
                        success: function(response) {
                            if (response.error_code == "0") {
                                toastr.success('Kết thúc biểu quyết thành công');
                                drawDsBieuQuyet();
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(err) {
                            console.log(err);
                            if (err.status == 403) {
                                toastr.error('Người dùng không có quyền thực hiện thao tác này');
                            } else {
                                toastr.error('Kết thúc biểu quyết không thành công');
                            }
                        }
                    });
                }
            })
        }
        function ValidateBieuQuyet(noidungbieuquyet, tgbieuquyet) {
            if (!noidungbieuquyet || noidungbieuquyet == "" || noidungbieuquyet == '') {
                toastr.warning('Nội dung biểu quyết không được để trống');
                return false;
            }
            if (!tgbieuquyet || tgbieuquyet == "" || tgbieuquyet == '') {
                toastr.warning('Thời gian biểu quyết không được để trống');
                return false;
            }
            return true;
        }

        function SuaBieuQuyet(id, tenbieuquyet, thoigian, stt) {
            console.log(id);
            SetValueModal('Sửa biểu quyết', tenbieuquyet, thoigian, stt, id);
            $('.btn-them').addClass("hidden");
            $('.btn-luu').removeClass("hidden");
            $('#themoiModal').modal('show');
        }

        function SetValueModal(title, bieuquyet, thoigian, stt, id) {
            $('#themmoiModalLabel').text(title);
            $('#txt-bieuquyet').val(bieuquyet);
            $('#txt-tgbieuquyet').val(thoigian);
            $('#txt-sttbieuquyet').val(stt);
            $('#txt-idbieuquyet').val(id);
        }
        $(document).ready(function () {
            initKyHopController();
        });
    </script>
@stop


