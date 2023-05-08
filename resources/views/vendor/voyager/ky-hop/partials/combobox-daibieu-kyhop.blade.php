<select id="cbx-daibieu" class="form-control" onchange="OnChangeComBoBoxDaiBieu({{$kyhop->id}})">
    <option value="0">-- Thêm đại biểu --</option>
    @foreach ($daibieus as $daibieu)
        <option value="{{$daibieu->user_id}}">{{$daibieu->name}}</option>
    @endforeach
</select>
