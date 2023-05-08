<div class="modal fade" id="suaUserModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <span class="modal-title" id="themmoiModalLabel">Sửa người dùng</h5>
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
                            <input type="text" id="txt-hoten" class="form-control" value="{{$user->name}}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <span>Tên Đ.nhập </span>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" id="txt-tendangnhap" class="form-control" value="{{$user->username}}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <span>Giới tính</span>
                        </div>
                        <div class="col-sm-8">
                            <select id="txt-gioitinh" class="form-control">
                                @if ($user->gioi_tinh == 1)
                                    <option selected value="1">Nam</option>
                                    <option value="0">Nữ</option>
                                @else
                                    <option value="1">Nam</option>
                                    <option selected value="0">Nữ</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <span>Tôn giáo</span>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" id="txt-tongiao" class="form-control" value="{{$user->tongiao}}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <span>Tr.độ chính trị</span>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" id="txt-trinhdochinhtri" class="form-control" value="{{$user->trinhdo_chinhtri}}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <span>Tr.độ học vấn</span>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" id="txt-trinhdohocvan" class="form-control" value="{{$user->trinhdo_hocvan}}">
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
                                    @if ($user->donvi_id == $donvi->id)
                                        <option selected value="{{$donvi->id}}">{{$donvi->ten_donvi}}</option>
                                    @else
                                        <option value="{{$donvi->id}}">{{$donvi->ten_donvi}}</option>
                                    @endif
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
                            @if ($user->ngay_sinh != null)
                                <input type="date" id="txt-ngaysinh" class="form-control" value="{{$user->ngay_sinh}}">
                            @else
                                <input type="date" id="txt-ngaysinh" class="form-control" value="">
                            @endif
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
                                    @if ($user->dantoc_id == $dantoc->id)
                                        <option selected value="{{$dantoc->id}}">{{$dantoc->dan_toc}}</option>
                                    @else
                                        <option value="{{$dantoc->id}}">{{$dantoc->dan_toc}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <span>Điện thoại </span><span class="required">(*)</span>
                        </div>
                        <div class="col-sm-8">
                            <input type="number" id="txt-dienthoai" class="form-control" value="{{$user->phone}}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <span>Tr.độ ch.môn</span>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" id="txt-trinhdochuyenmon" class="form-control" value="{{$user->trinhdo_chuyenmon}}">
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
                                    @if ($user->chucvu_id == $chucvu->id)
                                        <option selected value="{{$chucvu->id}}">{{$chucvu->chuc_vu}}</option>
                                    @else
                                        <option value="{{$chucvu->id}}">{{$chucvu->chuc_vu}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <span>Vai trò</span>
                        </div>
                        <div class="col-sm-8">
                            <select id="txt-vaitro" class="form-control">
                                <option value="0">Chọn vai trò</option>
                                @foreach ($roles as $role)
                                    @if ($user->role_id == $role->id)
                                        <option selected value="{{$role->id}}">{{$role->display_name}}</option>
                                    @else
                                        <option value="{{$role->id}}">{{$role->display_name}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 text-center">
                    <img id="edit-img-avatar" src="/storage/{{$user->avatar}}"/>
                    <br/>
                    <a href="javascript:void(0)" onclick="document.getElementById('edit-file-avatar').click()" title="Chọn ảnh đại diện"><i class="glyphicon glyphicon-picture"></i> Ảnh đại diện</a>
                    <a href="javascript:void(0)" onclick="RemoveEditAvatar()" title="Bỏ ảnh đại diện"><i class="glyphicon glyphicon-remove-sign icon-remove-avatar"></i></a>
                    <input type="file" id="edit-file-avatar" style="display:none" accept="image/*" onchange="PreviewImage(event, 'edit-img-avatar')">
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <div class="row">
                        <div class="col-sm-4">
                            <span>Quê quán</span>
                        </div>
                        <div class="col-sm-8">
                            <textarea id="txt-quequan" class="form-control">{{$user->que_quan}}</textarea>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="row">
                        <div class="col-sm-2">
                            <span>Nghề nghiệp chức vụ</span>
                        </div>
                        <div class="col-sm-10">
                            <textarea id="txt-nghenghiep" class="form-control">{{$user->nghenghiep_chucvu}}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
          <button type="button" class="btn btn-primary btn-them" onclick="UpdateUser({{$user->id}})">Lưu</button>
        </div>
      </div>
    </div>
</div>
