<!DOCTYPE html>
@extends('voyager::master')

@section('page_title', 'Khóa họp')

@section('page_header')
    <div class="container-fluid card">
        <h1 class="">
            Danh sách khóa họp
        </h1>
        <div class="row">
            <div class="col-sm-4">
            {{-- <input type="text" placeholder="Tên khóa họp ..." id="ten-khoahop" class="form-control" onchange="OnChangeKhoaHop()"> --}}
            </div>

            <div class="col-sm-2">
                <select id="cbx-trangthai" class="select2" onchange="OnChangeKhoaHop()" >
                    <option value="-1" selected>-- Trạng thái --</option>
                    <option value="1">Hoạt động</option>
                    <option value="0">Không hoạt động</option>
                </select>
            </div>
            <div class="col-sm-2">
                @can('add_khoa-hop')
                    <button class="btn btn-success btn-add-new" onclick="showModalThemMoiKhoaHop()"><span>Thêm mới</span></button>
                @endcan
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="row page-content browse container-fluid">
        <div id="dskhoahop">

        </div>
    </div>

    <div class="modal fade" id="themoiModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="themmoiModalLabel">Thêm mới khóa họp</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-3">
                        <label>Tên khóa họp </label><label class="required">(*)</label>
                    </div>
                    <div class="col-sm-9">
                        <input type="text" id="txt-idkhoahop" class="form-control hidden">
                        <input type="text" id="txt-tenkhoahop" class="form-control">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <label>Ngày bắt đầu </label><label class="required">(*)</label>
                    </div>
                    <div class="col-sm-9">
                        <input type="date" id="txt-tgbatdau" class="form-control">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <label>Ngày kết thúc</label><label class="required">(*)</label>
                    </div>
                    <div class="col-sm-9">
                        <input type="date" id="txt-tgketthuc" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
              <button type="button" class="btn btn-primary btn-them" onclick="ThemKhoaHop()">Thêm</button>
              <button type="button" class="btn btn-primary btn-luu hidden" onclick="LuuKhoaHop()">Lưu</button>
            </div>
          </div>
        </div>
    </div>

    <div id="divDaiBieu"></div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ voyager_asset('lib/css/responsive.dataTables.min.css') }}">
{{-- @if(!$dataType->server_side && config('dashboard.data_tables.responsive'))

@endif --}}
@stop
@include('voyager::loading.spin')
<link rel="stylesheet" href="/css/style.css">
@section('javascript')
    <script src="/js/app.js"></script>
    <script src="/js/sweetalert.min.js"></script>
    <script src="/js/sort-list.js"></script>
    <!-- DataTables -->
    {{-- @if(!$dataType->server_side && config('dashboard.data_tables.responsive'))
        <script src="{{ voyager_asset('lib/js/dataTables.responsive.min.js') }}"></script>
    @endif --}}
    <script>
        var khoahopId = 0;
        function OnChangeKhoaHop() {
            var trangthai = $('#cbx-trangthai').val();
            var tenkhoahop = $('#ten-khoahop').val();
            $('#dskhoahop').empty();
            $.ajax({
                type: 'post',
                url: '{{route("voyager.khoa-hop.getlistkhoahop")}}',
                data: {
                    trangthai: trangthai,
                    tenkhoahop: tenkhoahop
                },
                success: function(result) {
                    $('#dskhoahop').html(result);
                },
                error: function(err) {
                    console.log(err);
                }
            });
        }
        function showModalThemMoiKhoaHop() {
            var today = new Date();
            // Lấy ngày hiện tại
            var dateCu = today.getFullYear()+"-"+ ("0"+(today.getMonth()+1)).slice(-2) +  "-" + ("0" + today.getDate()).slice(-2);

            console.log(dateCu);

            SetValueModal('Thêm mới khóa họp', 0,"",dateCu,dateCu);
            $('.btn-them').removeClass("hidden");
            $('.btn-luu').addClass("hidden");
            $('#themoiModal').modal('show');

        }
        function showModalCapNhatKhoaHop(id,ten,ngaybatdau,ngayketthuc) {
            console.log(ngayketthuc);
            console.log(ngaybatdau);
            SetValueModal('Cập nhật khóa họp', id, ten, ngaybatdau, ngayketthuc);
            $('.btn-them').addClass("hidden");
            $('.btn-luu').removeClass("hidden");
            $('#themoiModal').modal('show');
        }
        function SetValueModal(title, id, ten, ngaybatdau, ngayketthuc) {
            $('#themmoiModalLabel').text(title);
            $('#txt-idkhoahop').val(id);
            $('#txt-tenkhoahop').val(ten);
            $('#txt-tgbatdau').val(ngaybatdau);
            $('#txt-tgketthuc').val(ngayketthuc);
        }
        function ShowPopUpDaiBieu(khoahopid) {
            khoahopId = khoahopid;
            $('#divDaiBieu').empty();
            $.ajax({
                    type: 'post',
                    url: 'khoa-hop/modal-dai-bieu',
                    data: {
                        khoahopid: khoahopid
                    },
                    success: function(response) {
                        $('#divDaiBieu').html(response);
                        InitNguoiDungComboBox(khoahopid);
                        DrawDsDaiBieuKhoaHop(khoahopid);
                        $('#suaDaiBieuKhoaHopModal').modal('show');
                    },
                    error: function(err) {
                        console.log(err);
                        toastr.error('Hiển thị modal đại biểu không thành công');
                    }
                });
        }
        function DrawDsDaiBieuKhoaHop(khoahopid) {
            showLoading();
            $.ajax({
                    type: 'post',
                    url: 'khoa-hop/ds-daibieu',
                    data: {
                        khoahopid: khoahopid
                    },
                    success: function(response) {
                        $('#danhsach-daibieu-khoahop').html(response);
                        slist(document.getElementById("sortlist"), UpdateSTT);
                        hideLoading();
                    },
                    error: function(err) {
                        console.log(err);
                        toastr.error('Hiển thị danh sách đại biểu không thành công');
                        hideLoading();
                    }
                });
        }

        function InitNguoiDungComboBox(khoahopid) {
            var donviid = $('#cbx-donvi').val();
            $('#cbx-nguoidung').select2({
                minimumResultsForSearch: Infinity,
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
                    url: '/admin/nguoi-dung',
                    data: function (params) {
                        var query = {
                            search: params.term,
                            donviid: donviid,
                            khoahopid: khoahopid
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

        function OnChangeCbxDonVi(khoahopid) {
            $("#cbx-nguoidung").val(0).trigger('change');
            InitNguoiDungComboBox(khoahopid);
        }

        function AddDaiBieuKhoaHop(khoahopid) {
            var donviid =  $("#cbx-donvi").val();
            var nguoidungid =  $("#cbx-nguoidung").val();
            if (donviid == 0 && nguoidungid == 0) {
                swal("Bạn có chắc chắn muốn thêm tất cả đại biểu khóa họp?", {
                    buttons: ["Không", "Có"],
                    dangerMode: true
                })
                .then((value) => {
                    if (value) {
                        DoAddDaiBieuKhoaHop(khoahopid, donviid, nguoidungid);
                    }
                })
            } else {
                DoAddDaiBieuKhoaHop(khoahopid, donviid, nguoidungid);
            }
        }
        function DoAddDaiBieuKhoaHop(khoahopid, donviid, nguoidungid) {
            $.ajax({
                    type: 'post',
                    url: '/admin/khoa-hop/add-daibieu',
                    data: {
                        khoahopid: khoahopid,
                        donviid: donviid,
                        nguoidungid: nguoidungid
                    },
                    success: function(response) {
                        if (response.error_code == "0") {
                            toastr.success('Thêm đại biểu thành công');
                            DrawDsDaiBieuKhoaHop(khoahopid);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(err) {
                        console.log(err);
                        if (err.status == 403) {
                            toastr.error('Người dùng không có quyền thực hiện thao tác');
                        } else {
                            toastr.error('Thêm đại biểu không thành công');
                        }
                    }
                });
        }
        function ShowPopUpXoaDaiBieuKhoaHop(khoahopid, daibieuid) {
            swal("Bạn có chắc chắn muốn xóa đại biểu khóa họp?", {
                    buttons: ["Không", "Có"],
                    dangerMode: true
                })
            .then((value) => {
                if (value) {
                    $.ajax({
                        type: 'post',
                        url: '/admin/khoa-hop/xoa-daibieu',
                        data: {
                            khoahopid: khoahopid,
                            daibieuid: daibieuid
                        },
                        success: function(response) {
                            if (response.error_code == "0") {
                                toastr.success('Xóa đại biểu khóa họp thành công');
                                DrawDsDaiBieuKhoaHop(khoahopid);
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(err) {
                            console.log(err);
                            toastr.error('Xóa đại biểu khóa họp không thành công');
                        }
                    });
                }
            })
        }
        function ThemKhoaHop(){
            var tenKhoaHop = $('#txt-tenkhoahop').val();
            var tgBatDau = $('#txt-tgbatdau').val();
            var tgKetThuc = $('#txt-tgketthuc').val();

            if (ValidateKhoaHop(tenKhoaHop,tgBatDau, tgKetThuc)) {
                $.ajax({
                    type: 'post',
                    url: 'khoa-hop/them-moi',
                    data: {
                        tenkhoahop: tenKhoaHop,
                        tgbatdau: tgBatDau,
                        tgketthuc: tgKetThuc
                    },
                    success: function(response) {
                        if (response.error_code == "0") {
                            toastr.success('Thêm mới khóa họp thành công');
                            $('#themoiModal').modal('hide');
                            OnChangeKhoaHop();
                        } else {
                            toastr.error(response.message);
                        }
                        console.log(result);
                    },
                    error: function(err) {
                        console.log(err);
                        if (err.status == 403) {
                            toastr.error('Người dùng không có quyền thực hiện thao tác');
                        } else {
                            toastr.error('Thêm mới khóa họp không thành công');
                        }
                    }
                });
            }
        }
        function LuuKhoaHop(){
            var tenKhoaHop = $('#txt-tenkhoahop').val();
            var tgBatDau = $('#txt-tgbatdau').val();
            var tgKetThuc = $('#txt-tgketthuc').val();
            var iD = $('#txt-idkhoahop').val();

            if (ValidateKhoaHop(tenKhoaHop,tgBatDau, tgKetThuc)) {
                $.ajax({
                    type: 'post',
                    url: 'khoa-hop/cap-nhat',
                    data: {
                        tenkhoahop: tenKhoaHop,
                        tgbatdau: tgBatDau,
                        tgketthuc: tgKetThuc,
                        id: iD
                    },
                    success: function(response) {
                        if (response.error_code == "0") {
                            toastr.success('Cập nhật khóa họp thành công');
                            $('#themoiModal').modal('hide');
                            OnChangeKhoaHop();
                        } else {
                            toastr.error(response.message);
                        }
                        console.log(result);
                    },
                    error: function(err) {
                        console.log(err);
                        if (err.status == 403) {
                            toastr.error('Người dùng không có quyền thực hiện thao tác');
                        } else {
                            toastr.error('Cập nhật họp không thành công');
                        }
                    }
                });
            }
        }
        function XoaKhoaHop(id) {
            swal("Bạn có chắc chắn muốn xóa khóa họp?", {
                buttons: ["Không", "Có"],
                dangerMode: true
            })
            .then((value) => {
                if (value) {
                    $.ajax({
                        type: 'post',
                        url: '/admin/khoa-hop/xoa',
                        data: {
                            id: id
                        },
                        success: function(response) {
                            if (response.error_code == "0") {
                                toastr.success('Xóa khóa họp thành công');
                                OnChangeKhoaHop();
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(err) {
                            console.log(err);
                            toastr.error('Xóa khóa họp không thành công');
                        }
                    });
                }
            })
        }
        function ChuyenTrangThai(id,trangthai) {
            swal("Bạn có chắc chắn muốn chuyển trạng thái khóa họp?", {
                buttons: ["Không", "Có"],
                dangerMode: true
            })
            .then((value) => {
                if (value) {
                    $.ajax({
                        type: 'post',
                        url: '/admin/khoa-hop/chuyentrangthai',
                        data: {
                            id: id,
                            trangthai: trangthai
                        },
                        success: function(response) {
                            if (response.error_code == "0") {
                                toastr.success('Chuyển trạng thái thành công!');
                                OnChangeKhoaHop();
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(err) {
                            console.log(err);
                            toastr.error('Chuyển trạng thái không thành công!');
                        }
                    });
                }
            })
        }
        function ValidateKhoaHop(tenKhoaHop,tgBatDau, tgKetThuc) {
            if (!tenKhoaHop || tenKhoaHop == "" || tenKhoaHop == '') {
                toastr.warning('Tên khóa họp không được để trống');
                return false;
            }
            if (!tgBatDau || tgBatDau == "" || tgBatDau == '') {
                toastr.warning('Thời gian bắt đầu không được để trống');
                return false;
            }
            if (!tgKetThuc || tgKetThuc == "" || tgKetThuc == '') {
                toastr.warning('Thời gian kết thúc không được để trống');
                return false;
            }
            return true;
        }
        function UpdateSTT() {
            var lsOrders = [];
            $("#sortlist").find('li').each(function( index ) {
                var id = $(this).find('.daibieuid').text();
                lsOrders.push({
                    "id": id,
                    "stt": index
                })
            });
            showLoading();
            $.ajax({
                type: 'post',
                url: '/admin/khoa-hop/sapxep-daibieu',
                data: {
                    khoahopid: khoahopId,
                    lsOrders: lsOrders
                },
                success: function(response) {
                    hideLoading();
                    if (response.error_code == "0") {
                        toastr.success('Cập nhật số thứ tự đại biểu thành công');
                        DrawDsDaiBieuKhoaHop(khoahopId);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(err) {
                    console.log(err);
                    if (err.status == 403) {
                        toastr.error('Người dùng không có quyền thực hiện thao tác này');
                    } else {
                        toastr.error('Cập nhật số thứ tự đại biểu không thành công');
                    }
                }
            });
        }

        $(document).ready(function () {
            $('#table-khoahop').DataTable({
                paging: false,
                info: false,
                scrollY: 600
            });
            // $("#txt-tgbatdau").datepicker({
            //     multidate: false,
            //     locale: "vi"
            // });
            // $("#txt-tgketthuc").datepicker({
            //     multidate: false,
            //     locale: "vi"
            // });
            OnChangeKhoaHop();
        });
    </script>
@stop
