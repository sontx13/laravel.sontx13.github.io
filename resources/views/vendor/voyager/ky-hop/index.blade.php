<!DOCTYPE html>
@extends('voyager::master')

@section('page_title', 'Kỳ họp')
@section('page_header')
    <div class="container-fluid card">
        <h1 class="">
            Danh sách kỳ họp
        </h1>
        <div class="row">
            <div class="col-sm-1">
                <span>Khóa họp</span>
            </div>
            <div class="col-sm-4">
                <select id="cbx-khoahop" class="select2" onchange="OnChangeKhoaHop()" >
                    <option value="0">Chưa chọn</option>
                    @foreach ($lsKhoahop as $keyItem => $dataItem)
                        @if ($dataItem->trang_thai == App\Enums\TrangThaiKhoaHopEnum::HoatDong)
                            <option selected value="{{$dataItem->id}}">{{$dataItem->ten_khoa_hop}}</option>
                        @else
                            <option value="{{$dataItem->id}}">{{$dataItem->ten_khoa_hop}}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2">
                @can('add_ky-hop')
                    <button class="btn btn-success btn-add-new" onclick="ShowPopupThemMoiKyHop()"><i class="voyager-plus"></i><span>Thêm mới</span></button>
                @endcan
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        <div id="ds-kyhop">

        </div>
    </div>
    <div id="modal-sua">

    </div>
    <div id="modal-lichhop">

    </div>
    <div id="modal-vaitro-kyhop">

    </div>
    <div class="modal fade" id="themoiKyHopModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <span class="modal-title" id="themmoiModalLabel">Thêm mới kỳ họp</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-3">
                        <span>Tên kỳ họp </span><span class="required">(*)</span>
                    </div>
                    <div class="col-sm-9">
                        <textarea type="text" id="txt-tenkyhop" class="form-control"></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <span>Ngày họp <span class="required">(*)</span></span>
                    </div>
                    <div class="col-sm-9">
                        <input type="text" id="ngay-hop" class="form-control">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <span>Địa điểm </span>
                    </div>
                    <div class="col-sm-9">
                        <textarea type="text" id="txt-diadiem" class="form-control"></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <span>Trạng thái</span>
                    </div>
                    <div class="col-sm-9">
                        <select id="trangthai-kyhop" class="form-control">
                            <option value="0">Không sử dụng</option>
                            <option value="1">Sắp diễn ra</option>
                            <option value="2">Đã diễn ra</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
              <button type="button" class="btn btn-primary btn-them" onclick="ThemMoiKyHop()">Thêm</button>
            </div>
          </div>
        </div>
    </div>
@stop

@section('css')
    {{-- <link rel="stylesheet" href="{{ voyager_asset('lib/css/responsive.dataTables.min.css') }}"> --}}
{{-- @if(!$dataType->server_side && config('dashboard.data_tables.responsive'))

@endif --}}
@stop
@include('voyager::loading.spin')
<link rel="stylesheet" href="/css/style.css">
@section('javascript')
    <script src="/js/app.js"></script>
    <script src="/js/sweetalert.min.js"></script>
    <script src="/js/ckeditor.js"></script>
    <!-- DataTables -->
    {{-- @if(!$dataType->server_side && config('dashboard.data_tables.responsive'))
        <script src="{{ voyager_asset('lib/js/dataTables.responsive.min.js') }}"></script>

    @endif --}}
    <script>
        function OnChangeKhoaHop() {
            var khoaHopId = $('#cbx-khoahop').val();
            drawDsKyHop();
        }
        function drawDsKyHop() {
            $("#ds-kyhop").empty();
            var khoahopId = $('#cbx-khoahop').val();
            if (khoahopId && khoahopId > 0) {
                showLoading();
                $.ajax({
                    type: 'post',
                    url: '/admin/ky-hop/danh-sach',
                    data: {
                        khoahopid: khoahopId
                    },
                    success: function(result) {
                        $("#ds-kyhop").html(result);
                        hideLoading();
                    },
                    error: function(err) {
                        console.log(err);
                        hideLoading();
                    }
                });
            }
        }

        function ShowPopupThemMoiKyHop() {
            var khoaHopId = $('#cbx-khoahop').val();
            if (khoaHopId != '0') {
                ResetModalThemMoi();
                $("#themoiKyHopModal").modal('show');
            } else {
                toastr.warning('Xin vui lòng chọn khóa họp');
            }
        }

        function ThemMoiKyHop() {
            var khoaHopId = $('#cbx-khoahop').val();
            var tenkyhop = $("#txt-tenkyhop").val();
            var diadiem = $("#txt-diadiem").val();
            var ngayhop = $("#ngay-hop").val();
            var trangthai = $("#trangthai-kyhop").val();
            if (ValidateKyHop(tenkyhop, ngayhop)) {
                $.ajax({
                    type: 'post',
                    url: '/admin/ky-hop/them-moi',
                    data: {
                        khoahopid: khoaHopId,
                        tenkyhop: tenkyhop,
                        diadiem: diadiem,
                        ngayhop: ngayhop,
                        trangthai: trangthai
                    },
                    success: function(response) {
                        if (response.error_code == "0") {
                            toastr.success('Thêm mới kỳ họp thành công');
                            $('#themoiKyHopModal').modal('hide');
                            drawDsKyHop();
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(err) {
                        console.log(err);
                        if (err.status == 403) {
                            toastr.error('Người dùng không có quyền thực hiện thao tác này');
                        } else {
                            toastr.error('Thêm mới kỳ họp không thành công');
                        }
                    }
                });
            }
        }
        function ValidateKyHop(tenkyhop, ngayhop) {
            if (!tenkyhop || tenkyhop == "" || tenkyhop == '' || tenkyhop == null) {
                toastr.warning('Tên kỳ họp không được để trống');
                return false;
            }
            if (!ngayhop || ngayhop == "" || ngayhop == '' || ngayhop == null) {
                toastr.warning('Ngày họp không được để trống');
                return false;
            }
            return true;
        }
        function ResetModalThemMoi() {
            $("#txt-tenkyhop").val('');
            $("#txt-diadiem").val('');
            $("#ngay-hop").val('');
            $("#trangthai-kyhop").val('0');
        }

        function ShowPopupSuaKyHop(kyhopid) {
            $.ajax({
                    type: 'post',
                    url: '/admin/ky-hop/modal-sua',
                    data: {
                        kyhopid: kyhopid
                    },
                    success: function(response) {
                        $("#modal-sua").html(response);
                        $('#suaKyHopModal').modal('show');
                        $("#sua-ngay-hop").datepicker({
                            multidate: true,
                            format: "dd/mm/yyyy",
                            locale: "vi"
                        });
                    },
                    error: function(err) {
                        console.log(err);
                        toastr.error('Hiển thị modal sửa kỳ họp không thành công');
                    }
                });
        }

        function ShowPopupXoaKyHop(kyhopid) {
            swal("Bạn có chắc chắn muốn xóa kỳ họp?", {
                buttons: ["Không", "Có"],
                dangerMode: true
            })
            .then((value) => {
                if (value) {
                    $.ajax({
                        type: 'post',
                        url: '/admin/ky-hop/xoa',
                        data: {
                            kyhopid: kyhopid
                        },
                        success: function(response) {
                            if (response.error_code == "0") {
                                toastr.success('Xóa kỳ họp thành công');
                                drawDsKyHop();
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(err) {
                            console.log(err);
                            if (err.status == 403) {
                                toastr.error('Người dùng không có quyền thực hiện thao tác này');
                            } else {
                                toastr.error('Xóa kỳ họp không thành công');
                            }
                        }
                    });
                }
            })
        }
        function UpdateKyHop(kyhopid) {
            var tenkyhop = $("#txt-sua-tenkyhop").val();
            var diadiem = $("#txt-sua-diadiem").val();
            var ngayhop = $("#sua-ngay-hop").val();
            var trangthai = $("#sua-trangthai-kyhop").val();
            if (ValidateKyHop(tenkyhop, ngayhop)) {
                $.ajax({
                    type: 'post',
                    url: '/admin/ky-hop/cap-nhat',
                    data: {
                        kyhopid: kyhopid,
                        tenkyhop: tenkyhop,
                        diadiem: diadiem,
                        ngayhop: ngayhop,
                        trangthai: trangthai
                    },
                    success: function(response) {
                        if (response.error_code == "0") {
                            toastr.success('Cập nhật kỳ họp thành công');
                            $('#suaKyHopModal').modal('hide');
                            drawDsKyHop();
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(err) {
                        console.log(err);
                        if (err.status == 403) {
                            toastr.error('Người dùng không có quyền thực hiện thao tác này');
                        } else {
                            toastr.error('Cập nhật kỳ họp không thành công');
                        }
                    }
                });
            }
        }

        function ShowPopupSetLichHop(kyhopid) {
            $.ajax({
                    type: 'post',
                    url: '/admin/ky-hop/lich-hop',
                    data: {
                        kyhopid: kyhopid
                    },
                    success: function(response) {
                        $("#modal-lichhop").html(response);
                        $('#suaLichHopModal').modal('show');
                        $('.buoihop-id').each(function() {
                            var buoihopid = $(this).text();
                            DecoupledEditor
                                .create( document.querySelector( '.document-editor__editable-' + buoihopid ), {
                                    cloudServices: {
                                    }
                                } )
                                .then( editor => {
                                    const toolbarContainer = document.querySelector( '.document-editor__toolbar-'+ buoihopid );
                                    toolbarContainer.appendChild( editor.ui.view.toolbar.element );
                                    editor.setData($('#document-content-' + buoihopid).text());
                                    window['editor' + buoihopid] = editor;
                                } )
                                .catch( err => {
                                    console.error( err );
                                } );
                        });

                    },
                    error: function(err) {
                        console.log(err);
                        toastr.error('Hiển thị popup lịch họp không thành công');
                    }
                });
        }

        function UpdateLichHop(lichhopid) {
            var giobatdau = $('#gio-batdau-' + lichhopid).val();
            var phutbatdau = $('#phut-batdau-' + lichhopid).val();
            var gioketthuc = $('#gio-ketthuc-' + lichhopid).val();
            var phutketthuc = $('#phut-ketthuc-' + lichhopid).val();
            var phonghop = $('#phong-hop-' + lichhopid).val();
            var noidung = window['editor' + lichhopid].getData();
            if (!phonghop || phonghop == null) {
                toastr.warning('Phòng họp không được để trống');
                return;
            }
            $.ajax({
                type: 'post',
                url: '/admin/lich-hop/cap-nhat',
                data: {
                    lichhopid: lichhopid,
                    giobatdau: giobatdau,
                    phutbatdau: phutbatdau,
                    gioketthuc: gioketthuc,
                    phutketthuc: phutketthuc,
                    phonghop: phonghop,
                    noidung: noidung
                },
                success: function(response) {
                    if (response.error_code == "0") {
                        toastr.success('Cập nhật lịch họp thành công');
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(err) {
                    console.log(err);
                    if (err.status == 403) {
                        toastr.error('Người dùng không có quyền thực hiện thao tác này');
                    } else {
                        toastr.error('Cập nhật lịch họp không thành công');
                    }
                }
            });
        }

        function ShowPopupSuaVaiTroKyHop(kyhopid) {
            $.ajax({
                    type: 'post',
                    url: '/admin/ky-hop/vai-tro',
                    data: {
                        kyhopid: kyhopid
                    },
                    success: function(response) {
                        $("#modal-vaitro-kyhop").html(response);
                        $('#suaVaiTroKyHopModal').modal('show');
                        $('#cbx-chutoa').select2();
                        DrawDanhSachDaiBieuKyHops(kyhopid);
                    },
                    error: function(err) {
                        console.log(err);
                        toastr.error('Hiển thị popup lịch họp không thành công');
                    }
                });
        }
        function DrawDanhSachDaiBieuKyHops(kyhopid) {
            showLoading();
            $.ajax({
                    type: 'post',
                    url: '/admin/ky-hop/dai-bieu',
                    data: {
                        kyhopid: kyhopid
                    },
                    success: function(response) {
                        $("#danhsach-daibieu-kyhop").html(response);
                        hideLoading();
                    },
                    error: function(err) {
                        console.log(err);
                        toastr.error('Hiển thị danh sách đại biểu không thành công');
                        hideLoading();
                    }
                });
        }
        function ThemTatCaDaiBieuKhoaHop(kyhopid) {
            swal("Bạn có chắc chắn muốn thêm tất cả đại biểu của khóa họp không?", {
                buttons: ["Không", "Có"],
            })
            .then((value) => {
                if (value) {
                    $.ajax({
                        type: 'post',
                        url: '/admin/ky-hop/them-tatca-daibieu',
                        data: {
                            kyhopid: kyhopid
                        },
                        success: function(response) {
                            if (response.error_code == "0") {
                                toastr.success('Thêm đại biểu thành công');
                                DrawDanhSachDaiBieuKyHops(kyhopid);
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(err) {
                            console.log(err);
                            if (err.status == 403) {
                                toastr.error('Người dùng không có quyền thực hiện thao tác này');
                            } else {
                                toastr.error('Thêm đại biểu không thành công');
                            }
                        }
                    });
                }
            })
        }
        function OnChangeChuToa(kyhopid) {
            var chutoas =  $('#cbx-chutoa').val();
            $.ajax({
                    type: 'post',
                    url: '/admin/ky-hop/capnhat-chutoa',
                    data: {
                        kyhopid: kyhopid,
                        chutoas: chutoas
                    },
                    success: function(response) {
                        if (response.error_code == "0") {
                            toastr.success('Cập nhật chủ tọa thành công');
                            DrawDanhSachDaiBieuKyHops(kyhopid);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(err) {
                        console.log(err);
                        if (err.status == 403) {
                            toastr.error('Người dùng không có quyền thực hiện thao tác này');
                        } else {
                            toastr.error('Cập nhật chủ tọa không thành công');
                        }
                    }
                });
        }


        function ShowPopUpXoaVaiTroDaiBieu(kyhopid, idvaitro) {
            swal("Bạn có chắc chắn muốn xóa đại biểu/khách mời không?", {
                buttons: ["Không", "Có"],
                dangerMode: true
            })
            .then((value) => {
                if (value) {
                    $.ajax({
                        type: 'post',
                        url: '/admin/ky-hop/xoa-vaitro-daibieu',
                        data: {
                            kyhopid: kyhopid,
                            idvaitro: idvaitro
                        },
                        success: function(response) {
                            if (response.error_code == "0") {
                                toastr.success('Xóa đại biểu/khách mời thành công');
                                DrawDanhSachDaiBieuKyHops(kyhopid);
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(err) {
                            console.log(err);
                            if (err.status == 403) {
                                toastr.error('Người dùng không có quyền thực hiện thao tác này');
                            } else {
                                toastr.error('Xóa đại biểu/khách mời không thành công');
                            }
                        }
                    });
                }
            })
        }

        function ShowComboBoxThemMotDaiBieuKhoaHop(kyhopid) {
            $("#div-cbx-add-daibieu-khachmoi").empty();
            showLoading();
            $.ajax({
                    type: 'post',
                    url: '/admin/ky-hop/cbx-daibieu',
                    data: {
                        kyhopid: kyhopid
                    },
                    success: function(response) {
                        hideLoading();
                        $("#div-cbx-add-daibieu-khachmoi").html(response);
                        $('#cbx-daibieu').select2();
                    },
                    error: function(err) {
                        hideLoading();
                        console.log(err);
                        toastr.error('Hiển thị combobox đại biểu không thành công');
                    }
                });
        }

        function OnChangeComBoBoxDaiBieu(kyhopid) {
            var daibieu =  $('#cbx-daibieu').val();
            if (daibieu != 0) {
                $.ajax({
                    type: 'post',
                    url: '/admin/ky-hop/them-daibieu',
                    data: {
                        kyhopid: kyhopid,
                        daibieu: daibieu
                    },
                    success: function(response) {
                        if (response.error_code == "0") {
                            toastr.success('Thêm đại biểu thành công');
                            DrawDanhSachDaiBieuKyHops(kyhopid);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(err) {
                        console.log(err);
                        if (err.status == 403) {
                            toastr.error('Người dùng không có quyền thực hiện thao tác này');
                        } else {
                            toastr.error('Thêm đại biểu không thành công');
                        }
                    }
                });
            }
        }

        function ShowComboBoxThemKhachMoiKyHop(kyhopid) {
            $("#div-cbx-add-daibieu-khachmoi").empty();
            showLoading();
            $.ajax({
                    type: 'post',
                    url: '/admin/ky-hop/cbx-khachmoi',
                    data: {
                        kyhopid: kyhopid
                    },
                    success: function(response) {
                        hideLoading();
                        $("#div-cbx-add-daibieu-khachmoi").html(response);
                        $('#cbx-khachmoi').select2();
                    },
                    error: function(err) {
                        hideLoading();
                        console.log(err);
                        toastr.error('Hiển thị combobox khách mời không thành công');
                    }
                });
        }
        function OnChangeComBoBoxKhachMoi(kyhopid) {
            var khachmoi =  $('#cbx-khachmoi').val();
            if (khachmoi != 0) {
                $.ajax({
                    type: 'post',
                    url: '/admin/ky-hop/them-khachmoi',
                    data: {
                        kyhopid: kyhopid,
                        khachmoi: khachmoi
                    },
                    success: function(response) {
                        if (response.error_code == "0") {
                            toastr.success('Thêm khách mời thành công');
                            DrawDanhSachDaiBieuKyHops(kyhopid);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(err) {
                        console.log(err);
                        if (err.status == 403) {
                            toastr.error('Người dùng không có quyền thực hiện thao tác này');
                        } else {
                            toastr.error('Thêm khách mời không thành công');
                        }
                    }
                });
            }
        }
        function CapnhatSTTKhoahop(kyhopid) {
            $.ajax({
                type: 'post',
                url: '/admin/ky-hop/capnhat-stt',
                data: {
                    kyhopid: kyhopid
                },
                success: function(response) {
                    if (response.error_code == "0") {
                        toastr.success('Cập nhật số thứ tự thành công');
                        DrawDanhSachDaiBieuKyHops(kyhopid);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(err) {
                    console.log(err);
                    if (err.status == 403) {
                        toastr.error('Người dùng không có quyền thực hiện thao tác này');
                    } else {
                        toastr.error('Cập nhật số thứ tự thành công');
                    }
                }
            });
        }
        $(document).ready(function () {
            $('#table_id').DataTable({
                paging: false,
                info: false,
                scrollY: 600
            });
            drawDsKyHop();
            $("#ngay-hop").datepicker({
                multidate: true,
                format: "dd/mm/yyyy",
                locale: "vi"
            });
        });

    </script>
@stop
