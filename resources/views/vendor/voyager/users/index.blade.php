<!DOCTYPE html>
@extends('voyager::master')

@section('page_title', 'Người dùng')
@section('page_header')
    <div class="container-fluid card">
        <h1 class="">
            Danh sách người dùng
        </h1>
        <div class="row">
            <div class="col-sm-1">
                <span>Đơn vị</span>
            </div>
            <div class="col-sm-4">
                <select id="cbx-donvi" class="select2" onchange="OnChangeDonVi()" >
                    <option value="0">-- Chưa chọn --</option>
                    @foreach ($donvis as $donvi)
                        <option value="{{$donvi->id}}">{{$donvi->ten_donvi}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2">
                <button class="btn btn-success btn-add-new" onclick="ShowPopupThemMoiUser()">Thêm mới</button>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        <div id="ds-user">

        </div>
    </div>
    <div id="modal-sua">

    </div>
    <div class="modal fade" id="themoiUserModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <span class="modal-title" id="themmoiModalLabel">Thêm mới người dùng</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="row">
                            <div class="col-sm-4">
                                <span>Họ tên </span><span class="required">(*)</span>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" id="txt-hoten" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <span>Tên Đ.nhập </span>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" id="txt-tendangnhap" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <span>Giới tính</span>
                            </div>
                            <div class="col-sm-8">
                                <select id="txt-gioitinh" class="form-control">
                                    <option value="1">Nam</option>
                                    <option value="0">Nữ</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <span>Tôn giáo</span>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" id="txt-tongiao" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <span>Tr.độ chính trị</span>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" id="txt-trinhdochinhtri" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <span>Tr.độ học vấn</span>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" id="txt-trinhdohocvan" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <span>Đơn vị</span>
                            </div>
                            <div class="col-sm-8">
                                <select id="txt-donvi" class="form-control">
                                    <option value="0">Chọn đơn vị</option>
                                    @foreach ($donvis as $donvi)
                                        <option value="{{$donvi->id}}">{{$donvi->ten_donvi}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="row">
                            <div class="col-sm-4">
                                <span>Ngày sinh</span>
                            </div>
                            <div class="col-sm-8">
                                <input type="date" id="txt-ngaysinh" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <span>Dân tộc</span>
                            </div>
                            <div class="col-sm-8">
                                <select id="txt-dantoc" class="form-control">
                                    <option value="0">Chọn dân tộc</option>
                                    @foreach ($dantocs as $dantoc)
                                        <option value="{{$dantoc->id}}">{{$dantoc->dan_toc}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <span>Điện thoại </span><span class="required">(*)</span>
                            </div>
                            <div class="col-sm-8">
                                <input type="number" id="txt-dienthoai" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <span>Tr.độ ch.môn</span>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" id="txt-trinhdochuyenmon" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <span>Chức vụ</span>
                            </div>
                            <div class="col-sm-8">
                                <select id="txt-chucvu" class="form-control">
                                    <option value="0">Chọn chức vụ</option>
                                    @foreach ($chucvus as $chucvu)
                                        <option value="{{$chucvu->id}}">{{$chucvu->chuc_vu}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <span>Vai trò </span><span class="required">(*)</span>
                            </div>
                            <div class="col-sm-8">
                                <select id="txt-vaitro" class="form-control">
                                    <option value="0">Chọn vai trò</option>
                                    @foreach ($roles as $role)
                                        <option value="{{$role->id}}">{{$role->display_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 text-center">
                        <img id="img-avatar"/>
                        <br/>
                        <a href="javascript:void(0)" onclick="document.getElementById('file-avatar').click()" title="Chọn ảnh đại diện"><i class="glyphicon glyphicon-picture"></i> Ảnh đại diện</a>
                        <a href="javascript:void(0)" onclick="RemoveAvatar()" title="Bỏ ảnh đại diện"><i class="glyphicon glyphicon-remove-sign icon-remove-avatar"></i></a>
                        <input type="file" id="file-avatar" style="display:none" accept="image/*" onchange="PreviewImage(event, 'img-avatar')">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="row">
                            <div class="col-sm-4">
                                <span>Quê quán</span>
                            </div>
                            <div class="col-sm-8">
                                <textarea id="txt-quequan" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <div class="row">
                            <div class="col-sm-2">
                                <span>Nghề nghiệp chức vụ</span>
                            </div>
                            <div class="col-sm-10">
                                <textarea id="txt-nghenghiep" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
              <button type="button" class="btn btn-primary btn-them" onclick="ThemMoiUser()">Thêm</button>
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
    <!-- DataTables -->
    {{-- @if(!$dataType->server_side && config('dashboard.data_tables.responsive'))
        <script src="{{ voyager_asset('lib/js/dataTables.responsive.min.js') }}"></script>

    @endif --}}
    <script>
        function OnChangeDonVi() {
            // var khoaHopId = $('#cbx-khoahop').val();
            drawDsUser();
        }
        function drawDsUser() {
            $("#ds-user").empty();
            var donviid = $('#cbx-donvi').val();
            showLoading();
            $.ajax({
                type: 'post',
                url: '/admin/user/danh-sach',
                data: {
                    donviid: donviid
                },
                success: function(result) {
                    $("#ds-user").html(result);
                    hideLoading();
                },
                error: function(err) {
                    console.log(err);
                    hideLoading();
                }
            });
        }

        function ShowPopupThemMoiUser() {
            ResetModalThemMoi();
            $("#themoiUserModal").modal('show');
        }

        function ThemMoiUser() {
            var hoten = $("#txt-hoten").val();
            var tendangnhap = $("#txt-tendangnhap").val();
            var ngaysinh = $("#txt-ngaysinh").val();
            var gioitinh = $("#txt-gioitinh").val();
            var dantoc = $("#txt-dantoc").val();
            var tongiao = $("#txt-tongiao").val();
            var dienthoai = $("#txt-dienthoai").val();
            var trinhdochinhtri = $("#txt-trinhdochinhtri").val();
            var trinhdochuyenmon = $("#txt-trinhdochuyenmon").val();
            var trinhdohocvan = $("#txt-trinhdohocvan").val();
            var chucvu = $("#txt-chucvu").val();
            var donvi = $("#txt-donvi").val();
            var vaitro = $("#txt-vaitro").val();
            var quequan = $("#txt-quequan").val();
            var nghenghiepchucvu = $("#txt-nghenghiep").val();
            var avatar = $('#img-avatar').attr('src');
            if (ValidateUser(hoten, dienthoai, vaitro)) {
                $.ajax({
                    type: 'post',
                    url: '/admin/user/them-moi',
                    data: {
                        hoten: hoten,
                        tendangnhap: tendangnhap,
                        ngaysinh: ngaysinh,
                        gioitinh: gioitinh,
                        dantoc: dantoc,
                        tongiao: tongiao,
                        dienthoai: dienthoai,
                        trinhdochinhtri: trinhdochinhtri,
                        trinhdochuyenmon: trinhdochuyenmon,
                        trinhdohocvan: trinhdohocvan,
                        chucvu: chucvu,
                        donvi: donvi,
                        vaitro: vaitro,
                        quequan: quequan,
                        nghenghiepchucvu: nghenghiepchucvu,
                        avatar: avatar
                    },
                    success: function(response) {
                        if (response.error_code == 0) {
                            toastr.success('Thêm mới người dùng thành công');
                            $('#themoiUserModal').modal('hide');
                            drawDsUser();
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(err) {
                        console.log(err);
                        toastr.error('Thêm mới người dùng không thành công');
                    }
                });
            }
        }
        function ValidateUser(hoten, dienthoai, vaitro) {
            if (!hoten || hoten == "" || hoten == '' || hoten == null) {
                toastr.warning('Họ tên người dùng không được để trống');
                return false;
            }
            if (!dienthoai || dienthoai == "" || dienthoai == '' || dienthoai == null) {
                toastr.warning('Điện thoại không được để trống');
                return false;
            }
            if (!vaitro || vaitro == "" || vaitro == '' || vaitro == null || vaitro == 0) {
                toastr.warning('Vai trò không được để trống');
                return false;
            }
            return true;
        }
        function ResetModalThemMoi() {
            $("#txt-hoten").val('');
            $("#txt-ngaysinh").val('');
            $("#txt-gioitinh").val('1');
            $("#txt-dantoc").val('0');
            $("#txt-tongiao").val('');
            $("#txt-dienthoai").val('');
            $("#txt-trinhdochinhtri").val('');
            $("#txt-trinhdochuyenmon").val('');
            $("#txt-trinhdohocvan").val('');
            $("#txt-chucvu").val('0');
            $("#txt-donvi").val('0');
            $("#txt-vaitro").val('0');
            $("#txt-quequan").val('');
            $("#txt-nghenghiep").val('');
            $("#file-avatar").val('');
            $("#img-avatar").attr("src","");
        }

        function ShowPopupSuaUser(userid) {
            $.ajax({
                    type: 'post',
                    url: '/admin/user/sua-modal',
                    data: {
                        userid: userid
                    },
                    success: function(response) {
                        $("#modal-sua").html(response);
                        $('#suaUserModal').modal('show');
                    },
                    error: function(err) {
                        console.log(err);
                        toastr.error('Hiển thị modal sửa người dùng không thành công');
                    }
                });
        }

        function ShowPopupXoaUser(userid) {
            swal("Bạn có chắc chắn muốn xóa người dùng?", {
                buttons: ["Không", "Có"],
                dangerMode: true
            })
            .then((value) => {
                if (value) {
                    $.ajax({
                        type: 'post',
                        url: '/admin/user/xoa',
                        data: {
                            userid: userid
                        },
                        success: function(response) {
                            if (response.error_code == 0) {
                                toastr.success('Xóa người dùng thành công');
                                drawDsUser();
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(err) {
                            console.log(err);
                            toastr.error('Xóa người dùng không thành công');
                        }
                    });
                }
            })
        }

        function UpdateUser(userid) {
            var hoten = $("#txt-hoten").val();
            var tendangnhap = $("#txt-tendangnhap").val();
            var ngaysinh = $("#txt-ngaysinh").val();
            var gioitinh = $("#txt-gioitinh").val();
            var dantoc = $("#txt-dantoc").val();
            var tongiao = $("#txt-tongiao").val();
            var dienthoai = $("#txt-dienthoai").val();
            var trinhdochinhtri = $("#txt-trinhdochinhtri").val();
            var trinhdochuyenmon = $("#txt-trinhdochuyenmon").val();
            var trinhdohocvan = $("#txt-trinhdohocvan").val();
            var chucvu = $("#txt-chucvu").val();
            var donvi = $("#txt-donvi").val();
            var vaitro = $("#txt-vaitro").val();
            var quequan = $("#txt-quequan").val();
            var nghenghiepchucvu = $("#txt-nghenghiep").val();
            var avatar = $('#edit-img-avatar').attr('src');
            if (ValidateUser(hoten, dienthoai, vaitro)) {
                $.ajax({
                    type: 'post',
                    url: '/admin/user/cap-nhat',
                    data: {
                        userid: userid,
                        hoten: hoten,
                        tendangnhap: tendangnhap,
                        ngaysinh: ngaysinh,
                        gioitinh: gioitinh,
                        dantoc: dantoc,
                        tongiao: tongiao,
                        dienthoai: dienthoai,
                        trinhdochinhtri: trinhdochinhtri,
                        trinhdochuyenmon: trinhdochuyenmon,
                        trinhdohocvan: trinhdohocvan,
                        chucvu: chucvu,
                        donvi: donvi,
                        vaitro: vaitro,
                        quequan: quequan,
                        nghenghiepchucvu: nghenghiepchucvu,
                        avatar: avatar
                    },
                    success: function(response) {
                        if (response.error_code == 0) {
                            toastr.success('Cập nhật người dùng thành công');
                            $('#suaUserModal').modal('hide');
                            drawDsUser();
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(err) {
                        console.log(err);
                        toastr.error('Cập nhật người dùng không thành công');
                    }
                });
            }
        }

        function ChuyenTrangThaiNguoiDung(userid) {
            $.ajax({
                    type: 'post',
                    url: '/admin/user/chuyen-trangthai',
                    data: {
                        userid: userid
                    },
                    success: function(response) {
                        if (response.error_code == 0) {
                            toastr.success('Chuyển trạng thái người dùng thành công');
                            drawDsUser();
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(err) {
                        console.log(err);
                        toastr.error('Chuyển trạng thái người dùng không thành công');
                    }
                });
        }

        function PreviewImage(event, selector) {
            var reader = new FileReader();
            reader.onload = function(){
                var output = document.getElementById(selector);
                output.src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }
        function RemoveEditAvatar() {
            $('#edit-file-avatar').val('');
            $('#edit-img-avatar').attr("src"," ");
        }
        function RemoveAvatar() {
            $('#file-avatar').val('');
            $('#img-avatar').attr("src"," ");
        }
        function ResetPassword(userid) {
            swal("Bạn có chắc chắn muốn đặt lại mật khẩu mặc định cho người dùng?", {
                buttons: ["Không", "Có"],
                dangerMode: true
            })
            .then((value) => {
                if (value) {
                    $.ajax({
                        type: 'post',
                        url: '/admin/user/resetpassword',
                        data: {
                            userid: userid
                        },
                        success: function(response) {
                            if (response.error_code == 0) {
                                toastr.success('Đặt lại mật khẩu mặc định thành công');
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(err) {
                            console.log(err);
                            toastr.error('Đặt lại mật khẩu mặc định không thành công');
                        }
                    });
                }
            })
        }
        $(document).ready(function () {
            drawDsUser();
        });
    </script>
@stop
