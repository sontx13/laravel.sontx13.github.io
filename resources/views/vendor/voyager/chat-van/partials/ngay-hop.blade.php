<div>
    <ul class="nav nav-tabs">
        @foreach ($lsNgayHops as $keyItem => $dataItem)
            @if ($keyItem == 0)
                <li class="active">
                    <span id="tab-thoigian-{{$dataItem->id}}" class="hidden thoigian-id">{{$dataItem->id}}</span>
                    <a href="javascript:void(0)" onclick="drawChatVanNgayHop({{$dataItem->id}}, {{$dataItem->kyhop_id}})">{{$dataItem->ngay_dienra}}</a>
                </li>
            @else
                <li>
                    <span id="tab-thoigian-{{$dataItem->id}}" class="hidden thoigian-id">{{$dataItem->id}}</span>
                    <a href="javascript:void(0)" onclick="drawChatVanNgayHop({{$dataItem->id}}, {{$dataItem->kyhop_id}})">{{$dataItem->ngay_dienra}}</a>
                </li>
            @endif
        @endforeach
</div>
