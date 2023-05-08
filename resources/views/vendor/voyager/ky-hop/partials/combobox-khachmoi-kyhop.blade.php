<select id="cbx-khachmoi" class="form-control" onchange="OnChangeComBoBoxKhachMoi({{$kyhop->id}})">
    <option value="0">-- Thêm Khách Mời --</option>
    @foreach ($khachmois as $khachmoi)
        <option value="{{$khachmoi->user_id}}">{{$khachmoi->name}}</option>
    @endforeach
</select>
