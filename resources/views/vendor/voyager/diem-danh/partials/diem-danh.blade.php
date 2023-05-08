<div class="">
    <div class="row text-center header">
        <div class="col-sm-1">
            <span>STT</span>
        </div>
        <div class="col-sm-7">
            <span>Đại biểu</span>
        </div>
        <div class="col-sm-2">
            <span>Trạng thái</span>
        </div>
        <div class="col-sm-2">
            <span>&nbsp;</span>
        </div>
    </div>
    @foreach ($lsDiemDanhs as $keyItem => $dataItem)
        <div class="row daibieu-diemdanh" id="dabieu-id-{{$dataItem->id}}">
            <div class="col-sm-1 text-center">
                <span>{{$keyItem+1}}</span>
            </div>
            <div class="col-sm-7">
                <span class="daibieu-id hidden">{{$dataItem->id}}</span>
                <span class="daibieu-name">{{$dataItem->name}}</span>
            </div>
            <div class="col-sm-2 text-center">
                @if ($dataItem->trang_thai == 2 || $dataItem->trang_thai == 3)
                    <button class="btn btn-sm btn-success btn-diemdanh co-mat" onclick="toggleDiemDanh(event)">Có mặt</button>
                    <button class="btn btn-sm btn-danger btn-diemdanh vang-mat hidden" onclick="toggleDiemDanh(event)">Vắng mặt</button>
                @else
                    <button class="btn btn-sm btn-success btn-diemdanh co-mat hidden" onclick="toggleDiemDanh(event)">Có mặt</button>
                    <button class="btn btn-sm btn-danger btn-diemdanh vang-mat" onclick="toggleDiemDanh(event)">Vắng mặt</button>
                @endif
            </div>
            <div class="col-sm-2">
                @if ($dataItem->trang_thai == 1 || $dataItem->trang_thai == 3)
                    <span class="span-thucong">Cập nhật thủ công</span>
                    <span class="span-tudong hidden">Điểm danh bằng App</span>
                @elseif ($dataItem->trang_thai == 2)
                    <span class="span-thucong hidden">Cập nhật thủ công</span>
                    <span class="span-tudong">Điểm danh bằng App</span>
                @else
                <span class="span-thucong hidden">Cập nhật thủ công</span>
                <span class="span-tudong hidden">Điểm danh bằng App</span>
                @endif
            </div>
        </div>
    @endforeach
</div>
