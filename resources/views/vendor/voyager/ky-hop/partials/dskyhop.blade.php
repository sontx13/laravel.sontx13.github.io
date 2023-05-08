
<table id="table_id" class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>STT</th>
            <th>Tên kỳ họp</th>
            <th>Trạng thái</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($listKyHop as $keyItem => $dataItem)
            <tr>
                <td> {{$keyItem + 1}}</td>
                <td>{{$dataItem->ten_ky_hop}}</td>
                @if ($dataItem->trang_thai == 1)
                    <td>Hoạt động</td>
                @else
                <td>Không hoạt động</td>
                @endif
                <td>
                    <button class="btn btn-sm btn-primary">Sửa</button>
                    <button class="btn btn-sm btn-danger">Xóa</button>
                    <button class="btn btn-sm btn-success">Quản lý đại biểu</button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
