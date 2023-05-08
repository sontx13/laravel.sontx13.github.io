<div class="">
    <div class="row header">
        <div class="col-sm-1 text-center">
            <span>STT</span>
        </div>
        <div class="col-sm-7">
            <span>Họ và tên</span>
        </div>
        <div class="col-sm-2">
            <span>Đơn vị</span>
        </div>
        <div class="col-sm-2">
        </div>
    </div>
    <ul id="sortlist">
    @foreach ($dsDaiBieus as $index => $daibieu)
        <li>
            <div class="row">
                <div class="col-sm-1 text-center">
                    <span>{{$index + 1}}</span>
                    <span class="daibieuid hidden">{{$daibieu->user_id}}</span>
                </div>
                <div class="col-sm-7">
                    @if ($daibieu->user != null)
                        <span>{{$daibieu->user->name}}</span>
                    @endif
                </div>
                <div class="col-sm-2">
                    @if ($daibieu->user != null && $daibieu->user->donvi != null)
                        <span>{{$daibieu->user->donvi->ten_donvi}}</span>
                    @else
                        <span></span>
                    @endif
                </div>
                <div class="col-sm-2">
                    @can('delete_daibieu-khoahop')
                        <a href="javascript:void(0)" onclick="ShowPopUpXoaDaiBieuKhoaHop({{$khoahopid}}, {{$daibieu->user_id}})" title="Xóa"><i class="glyphicon glyphicon-trash icon icon-xoa"></i></a>
                    @endcan
                </div>
            </div>
        </li>
    @endforeach
    </ul>
</div>
