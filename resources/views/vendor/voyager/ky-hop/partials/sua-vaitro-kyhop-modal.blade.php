<div class="modal fade" id="suaVaiTroKyHopModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <span class="modal-title" id="themmoiModalLabel">Thiết lập vai trò</span>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-sm-3">
                    <span>Chủ tọa</span>
                </div>
                <div class="col-sm-9">
                    @can('edit_daibieu-kyhop')
                        <select class="form-control" id="cbx-chutoa" multiple="multiple" onchange="OnChangeChuToa({{$kyhop->id}})">
                            @foreach ($daibieukhoahops as $daibieukhoahop)
                                @if (in_array($daibieukhoahop->user_id, $chutoaids))
                                    <option selected value="{{$daibieukhoahop->user_id}}">{{$daibieukhoahop->user->name}}</option>
                                @else
                                    <option value="{{$daibieukhoahop->user_id}}">{{$daibieukhoahop->user->name}}</option>
                                @endif
                            @endforeach
                        </select>
                    @else
                        <select class="form-control" id="cbx-chutoa" multiple="multiple" disabled="disabled" onchange="OnChangeChuToa({{$kyhop->id}})">
                            @foreach ($daibieukhoahops as $daibieukhoahop)
                                @if (in_array($daibieukhoahop->user_id, $chutoaids))
                                    <option selected value="{{$daibieukhoahop->user_id}}">{{$daibieukhoahop->user->name}}</option>
                                @else
                                    <option value="{{$daibieukhoahop->user_id}}">{{$daibieukhoahop->user->name}}</option>
                                @endif
                            @endforeach
                        </select>
                    @endcan

                </div>
            </div>
            <div id="danhsach-daibieu-kyhop">

            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
        </div>
      </div>
    </div>
</div>
