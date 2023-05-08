<div>
    <ul class="nav nav-tabs">
        @foreach ($lsBuoiHops as $keyItem => $dataItem)
            @php
                $isValidDiemDanhTime = App\Helpers\ValidateHelper::CheckThoiGianDiemDanh($dataItem->ngay_dienra, $dataItem->buoi_hop);
            @endphp
            @if ($isValidDiemDanhTime)
                <li class="active">
                    <span class="hidden lichhop-id">{{$dataItem->id}}</span>
                    <span class="hidden ngay-dien-ra">{{$dataItem->ngay_dienra}}</span>
                    <span class="hidden buoi-hop">{{$dataItem->buoi_hop}}</span>
                    <a onclick="drawDiemDanhBuoiHop({{$dataItem->id}}, {{$dataItem->kyhop_id}}, true, event)">{{$dataItem->ngay_dienra}}({{App\Http\Controllers\Voyager\DiemDanhController::GetDisplayBuoiHop($dataItem->buoi_hop)}})</a>
                </li>
            @else
                <li>
                    <span class="hidden lichhop-id">{{$dataItem->id}}</span>
                    <span class="hidden ngay-dien-ra">{{$dataItem->ngay_dienra}}</span>
                    <span class="hidden buoi-hop">{{$dataItem->buoi_hop}}</span>
                    <a onclick="drawDiemDanhBuoiHop({{$dataItem->id}}, {{$dataItem->kyhop_id}}, false , event)">{{$dataItem->ngay_dienra}}({{App\Http\Controllers\Voyager\DiemDanhController::GetDisplayBuoiHop($dataItem->buoi_hop)}})</a>
                </li>
            @endif
        @endforeach
</div>
