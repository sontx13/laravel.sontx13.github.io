<head>
    <title>Trình chiếu điểm danh</title>
</head>
<link rel="stylesheet" href="/css/style.css">
<div class="content-trinhchieu">
    <div class="text-center">
        <span class="ten-hoidong">{{ setting('site.tenhoidong') }}</span>
    </div>
    <div class="text-center">
        <span class="ten-khoahop">{{$kyhop->khoahop->ten_khoa_hop}}</span>
    </div>
    <div class="text-center">
        <span class="ten-kyhop">{{$kyhop->ten_ky_hop}}</span>
    </div>
    <div class="text-center">
        <span class="ten-diadiem">{{$kyhop->dia_diem}}, ngày {{date('d')}} tháng {{date('m')}} năm {{date('Y')}}</span>
    </div>
    <div class="text-center">
        <span id="kq-diem-danh" class="kq-diem-danh">0</span><span class="tong-daibieu">/ {{$toltalDB}}</span>
    </div>
    <div class="text-center">
        <span class="text-daibieu">Đại biểu đã điểm danh</span>
    </div>
</div>
@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop
<style>
    body {
        background-color: #f2f5d7;
    }
</style>

<script src="/js/firebase-config.js"></script>
<script src="/js/firebase-app.js"></script>
<script src="/js/firebase-database.js"></script>
<script src="/js/jquery-3.6.0.min.js"></script>

<script>
    var lsDaiBieuDiemDanhJson =  '<?php Print($lsDaiBieuJson); ?>';
    var isValid = '<?php Print($isValid); ?>';
    var tenantId = '<?php Print($tenantId); ?>';
    var lichhopId = '<?php Print($buoihopId); ?>';
    var useFirebase = "<?php echo setting('site.usefirebase'); ?>";
    var lsDaiBieus = [];
    if (lsDaiBieuDiemDanhJson) {
        lsDaiBieus = JSON.parse(lsDaiBieuDiemDanhJson);
    }
    //Nếu thời gian điểm danh hợp lệ => nghe sự kiện firebase
    if (isValid == "1") {
        DisplaySoLuongDiemDanh(lsDaiBieus);
        if (useFirebase == "1") {
            if (!firebase.apps.length) {
                firebase.initializeApp(firebaseConfig);
            }
            var database = firebase.database();
            const fetchDiemDanhs = database.ref(tenantId + "/diemdanhs/" + lichhopId);
            fetchDiemDanhs.on("child_added", function (snapshot) {
                const messages = snapshot.val();
                lsDaiBieus = HandleEventDiemDanh(lsDaiBieus, messages);
                console.log("child_added" + JSON.stringify(messages));
            });
            fetchDiemDanhs.on("child_changed", function (snapshot) {
                const messages = snapshot.val();
                lsDaiBieus = HandleEventDiemDanh(lsDaiBieus, messages);
                console.log("child_changed" + JSON.stringify(messages));
            });
        } else {
            myInterval = setInterval(function() {
                GetDataDiemDanh();
            }, 2000);
        }
    }
    function DisplaySoLuongDiemDanh(lsDaiBieus) {
        document.getElementById("kq-diem-danh").innerHTML = lsDaiBieus.length;
    }
    function HandleEventDiemDanh(lsDaiBieus, message) {
        var userId = message.userid;
        var trangthai = message.trangthai;
        var exists = lsDaiBieus.filter(x => x.user_id == userId);
        switch(trangthai) {
            // Vắng mặt
            case 1:
                // Kiểm tra đại biểu đã điểm danh hay chưa? -> đã điểm danh thì xóa ở ls đại biểu đã điểm danh
                if (exists && exists.length > 0) {
                    lsDaiBieus = lsDaiBieus.filter(x => x.user_id != userId);
                }
                break;
            // Điểm danh bằng app
            case 2:
            // Điểm danh thủ công
            case 3:
                // Nếu chưa được điểm danh => Thêm vào ls đại biểu đã điểm danh
                if (!exists || exists.length == 0) {
                    lsDaiBieus.push({
                        "user_id" : userId,
                        "trang_thai" : trangthai
                    });
                }
                break;
        }
        DisplaySoLuongDiemDanh(lsDaiBieus);
        return lsDaiBieus;
    }

    function GetDataDiemDanh() {
        $.ajax({
                type: 'post',
                url: '/admin/diem-danh/danh-sach',
                data: {
                    "_token": "{{ csrf_token() }}",
                    lichhopid: lichhopId
                },
                success: function(result) {
                    if (result.error_code == 0) {
                        DisplaySoLuongDiemDanh(result.data);
                    }
                },
                error: function(err) {
                    console.log(err);
                }
            });
    }
</script>


