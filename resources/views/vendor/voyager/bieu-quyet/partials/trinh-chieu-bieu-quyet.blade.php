@section('page_title', 'Trình chiếu biểu quyết')
<head>
    <title>Trình chiếu biểu quyết</title>
</head>
<div class="trinh-chieu-bieuquyet container no-padding">
    <div class="row ketqua">
        <div class="col-sm-12 text-center display-table">
            <span class="vertical-middle">KẾT QUẢ BIỂU QUYẾT</span>
        </div>
    </div>
    <div class="row thoigian">
        <div class="col-sm-6 display-table">
            <span class="vertical-middle">THỜI GIAN</span>
        </div>
        <div class="col-sm-6 text-center">
            <span id="dongho"></span>
        </div>
    </div>
    <div class="row tongso">
        <div class="col-sm-6 display-table">
            <span class="vertical-middle">THAM GIA</span>
        </div>
        <div class="col-sm-3 soluong">
            <span id="tongso_diemdanh">{{$tongso_diemdanh}}</span> / <span id="tongso_daibieu">{{$tongso_daibieu}}</span>
        </div>
        <div class="col-sm-3 text-right display-table tyle">
            <span id="tyle_thamgia">{{$tyle_thamgia}} </span>%
        </div>
    </div>
    <div class="row dongy">
        <div class="col-sm-6 display-table">
            <span class="vertical-middle">ĐỒNG Ý</span>
        </div>
        <div class="col-sm-3 soluong">
            <span id="tongso_dongy">{{$tongso_dongy}}</span>
        </div>
        <div class="col-sm-3 tyle text-right">
            <span id="tyle_dongy">{{$tyle_dongy}} </span>%
        </div>
    </div>
    <div class="row khongdongy">
        <div class="col-sm-6 display-table">
            <span class="vertical-middle">KHÔNG ĐỒNG Ý</span>
        </div>
        <div class="col-sm-3 soluong">
            <span id="tongso_khongdongy">{{$tongso_khongdongy}}</span>
        </div>
        <div class="col-sm-3 tyle text-right">
            <span id="tyle_khongdongy">{{$tyle_khongdongy}} </span>%
        </div>
    </div>
    <div class="row khongbieuquyet">
        <div class="col-sm-6 display-table">
            <span class="vertical-middle">KHÔNG BIỂU QUYẾT</span>
        </div>
        <div class="col-sm-3 soluong">
            <span id="tongso_khongbieuquyet">{{$tongso_khongbieuquyet}}</span>
        </div>
        <div class="col-sm-3 tyle text-right">
            <span id="tyle_khongbieuquyet">{{$tyle_khongbieuquyet}} </span>%
        </div>
    </div>
</div>

<link rel="stylesheet" href="/css/bootstrap.min.css">
<link rel="stylesheet" href="/css/style.css">
<script src="/js/jquery-3.6.0.min.js"></script>
<script src="/js/firebase-config.js"></script>
<script src="/js/firebase-app.js"></script>
<script src="/js/firebase-database.js"></script>
<script>
    var kyhopid = '<?php echo $kyhopid; ?>';
    var buoihopid = '<?php echo $buoihopid; ?>';
    var tenantId = '<?php echo $tenantId; ?>';
    var isShow = '<?php echo $isShow; ?>';
    var idbieuquyet = '<?php echo $idbieuquyet; ?>';
    var myInterval;
    var time_delay = 0;
    var tg_bieuquyet = '<?php echo $thoigian_bieuquyet; ?>';
    var tg_batdau = '<?php echo strtotime($thoigian_batdau); ?>';
    var server_start = parseInt(tg_batdau) * 1000;
    var daibieuJson = '<?php echo $daibieuJson; ?>';
    var useFirebase = "<?php echo setting('site.usefirebase'); ?>";
    var timeWarning = "<?php echo setting('site.timewarning'); ?>";

    var listenedBQDangDienRa = false;
    var listenedGiaTriBQ = false;
    var listenedDiemDanh = false;

    //Tồn tại biểu quyết đang biểu quyết
    var daibieus = JSON.parse(daibieuJson);

    if (isShow && buoihopid > 0) {
        // Khởi tạo firebase nếu là view trình chiếu
        if (useFirebase == "1") {
            if (!firebase.apps.length) {
                firebase.initializeApp(firebaseConfig);
                var database = firebase.database();
                // ref biểu quyết đang diễn ra
                var params = {
                        "tenantId": tenantId,
                        "kyhopid": kyhopid,
                        "buoihopid": buoihopid,
                        "bieuquyetid": idbieuquyet,
                        "database": database
                };
                EventListenerBQDangDienRa(params);
                // Trường hợp có event đang diễn ra => nghe event thay đổi giá trị bình chọn + điểm danh => tỷ lệ biểu quyết
                if (idbieuquyet > 0) {
                    // event thay đổi giá trị bình chọn
                    EventListenerGiaTriBQ(params);
                    // event điểm danh
                    EventListenerDiemDanhs(params);
                }
            }
        }
    }
    // Tồn tại danh sách đại biểu
    if (daibieus.length > 0) {
        // Hiển thị kết quả BQ
        DisplayKetQuaBQ(daibieus);
        // Nếu đang biểu quyết => hiển thị count down
        if (idbieuquyet > 0) {
            var totalSeconds = parseFloat(tg_bieuquyet) * 60;
            var server_end = new Date(server_start).setSeconds(new Date(server_start).getSeconds() + totalSeconds);
            console.log("server end: " + server_end);
            var server_now = <?php echo time(); ?> * 1000;
            var client_now = new Date().getTime();
            var client_end = server_end - server_now + client_now;
            console.log("client end: " + client_end);

            // Hiển thị count down thời gian biểu quyết với interval = 1s
            CountDown(client_end, myInterval);
            myInterval = setInterval(function() {
                CountDown(client_end, myInterval);
                setTimeout(() => {
                    GetKetQuaBieuQuyet(idbieuquyet);
                }, 1500);
            }, 1000);
        }
    }

    function EventListenerGiaTriBQ(params) {
        if (!listenedGiaTriBQ) {
            listenedGiaTriBQ = true;
            const fetchGiaTriBieuQuyets = params.database.ref(params.tenantId + "/bieuquyets/" + params.kyhopid + "/" + params.bieuquyetid);
            fetchGiaTriBieuQuyets.on("child_added", function (snapshot) {
                const messages = snapshot.val();
                console.log("ref giá trị biểu quyết child_added" + JSON.stringify(messages));
                HandleEventGiaTriBieuQuyet(messages);
            });

            fetchGiaTriBieuQuyets.on("child_changed", function (snapshot) {
                const messages = snapshot.val();
                console.log("ref giá trị biểu quyết child_changed" + JSON.stringify(messages));
                HandleEventGiaTriBieuQuyet(messages);
            });
        }
    }

    function EventListenerDiemDanhs(params) {
        if (!listenedDiemDanh) {
            listenedDiemDanh = true;
            const fetchDiemDanhs = params.database.ref(params.tenantId + "/diemdanhs/" + params.buoihopid);
            fetchDiemDanhs.on("child_added", function (snapshot) {
                const messages = snapshot.val();
                console.log("Ref điểm danh child_added" + JSON.stringify(messages));
                HandleEventDiemDanh(messages);

            });
            fetchDiemDanhs.on("child_changed", function (snapshot) {
                const messages = snapshot.val();
                console.log("Ref điểm danh child_changed" + JSON.stringify(messages));
                HandleEventDiemDanh(messages);
            });
        }
    }

    function EventListenerBQDangDienRa(params) {
        if (!listenedBQDangDienRa) {
            listenedBQDangDienRa = true;
            const fetchBieuquyets = params.database.ref(params.tenantId + "/bieuquyets/" + params.kyhopid + "/dangdienra");
            // Kết thúc biểu quyết
            fetchBieuquyets.on("child_removed", function (snapshot) {
                const messages = snapshot.val();
                console.log("ref biểu quyết đang diễn ra child_removed" + JSON.stringify(messages));
                HandleEventTrangThaiBieuQuyet(1, messages);
            });
            // Bắt đầu biểu quyết
            fetchBieuquyets.on("child_added", function (snapshot) {
                const messages = snapshot.val();
                console.log("ref biểu quyết đang diễn ra child_added" + JSON.stringify(messages));
                HandleEventTrangThaiBieuQuyet(0, messages, params);
            });
        }
    }
    // Hiển thị đồng hồ
    function CountDown(client_end, x) {
        var now = new Date().getTime() + time_delay;
        //lấy khoảng thời gian từ lúc thời gian hết hạn đến thời gian hiện tại
        var distance = client_end - now;
        console.log("distance: " + distance);
        //Tính phút vs giây hiển thị
        var minutes = Math.floor(distance/ (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);
        var result = "";
        result += minutes < 10 ? "0" + minutes + ":" : minutes + ":";
        result += seconds < 10 ? "0" + seconds : seconds;
        var donghoElement = document.getElementById("dongho");
        donghoElement.innerHTML = result;
        // Đếm hết thời gian hiển thị giá trị về 0
        var timewarningInt = parseInt(timeWarning);
        timewarningInt = timewarningInt == 0 ? 15 : timewarningInt;
        if (distance >= 0 && distance <= timewarningInt *1000) {
            var url = window.location.origin;
            var audio = new Audio(url + '/audio/beep-tick.mp3');
            audio.play();
            if (!donghoElement.classList.contains("color-red")) {
                donghoElement.classList.add("color-red");
            }
        }

        if (distance < 0) {
            donghoElement.classList.remove("color-red");
            myInterval = clearInterval(x);
            document.getElementById("dongho").innerHTML = "";
        }
    }
    // Hiển thị kết quả BQ
    function DisplayKetQuaBQ(dsDaiBieu) {
        // Tổng số đại biểu
        var tongso_daibieu = dsDaiBieu.length;
        document.getElementById("tongso_daibieu").innerHTML = tongso_daibieu;
        // Tổng số điểm danh
        var diemdanhs = dsDaiBieu.filter(x => x.trangthai_diemdanh == 1);
        var tongso_diemdanh = diemdanhs.length;
        document.getElementById("tongso_diemdanh").innerHTML = tongso_diemdanh;
        // Tổng số đồng ý
        var dongys = dsDaiBieu.filter(x => x.trangthai_diemdanh == 1 && x.gia_tri == 1);
        var tongso_dongy = dongys.length;
        document.getElementById("tongso_dongy").innerHTML = tongso_dongy;
        // Tổng số không đồng ý
        var khongdongys = dsDaiBieu.filter(x => x.trangthai_diemdanh == 1 && x.gia_tri == 2);
        var tongso_khongdongy = khongdongys.length;
        document.getElementById("tongso_khongdongy").innerHTML = tongso_khongdongy;
        // Tổng số không biểu quyết
        var khongbieuquyets = dsDaiBieu.filter(x => x.trangthai_diemdanh == 1 && x.gia_tri == 3);
        var tongso_khongbieuquyet = tongso_diemdanh - tongso_dongy - tongso_khongdongy;
        document.getElementById("tongso_khongbieuquyet").innerHTML = tongso_khongbieuquyet;
        // Tính tỷ lệ
        // tỷ lệ tham gia
        var tyle_thamgia = tongso_daibieu > 0 ? tongso_diemdanh/tongso_daibieu * 100 : 0;
        tyle_thamgia = Math.round(tyle_thamgia * 100) / 100;
        document.getElementById("tyle_thamgia").innerHTML = tyle_thamgia;

        // tỷ lệ đồng ý
        var tyle_dongy = tongso_daibieu > 0 ? tongso_dongy/tongso_daibieu * 100 : 0;
        tyle_dongy = Math.round(tyle_dongy * 100) / 100;
        document.getElementById("tyle_dongy").innerHTML = tyle_dongy;

        // tỷ lệ không đồng ý
        var tyle_khongdongy = tongso_daibieu > 0 ? tongso_khongdongy/tongso_daibieu * 100 : 0;
        tyle_khongdongy = Math.round(tyle_khongdongy * 100) / 100;
        document.getElementById("tyle_khongdongy").innerHTML = tyle_khongdongy;

        // tỷ lệ không biểu quyết
        var tyle_khongbieuquyet = tongso_daibieu > 0 ? tongso_khongbieuquyet/tongso_daibieu * 100 : 0;
        tyle_khongbieuquyet = Math.round(tyle_khongbieuquyet * 100) / 100;
        document.getElementById("tyle_khongbieuquyet").innerHTML = tyle_khongbieuquyet;
    }
    // Xử lý khi nhận được message bắt đầu, kết thúc biểu quyết
    function HandleEventTrangThaiBieuQuyet(type_event, messages, params) {
        switch(type_event) {
            //Bắt đầu
            case 0:
                // Handle count down
                HandleCountDown(messages);
                HandleTyleBQ(messages);
                idbieuquyet = messages.bieuquyetid;
                params.bieuquyetid = idbieuquyet;
                EventListenerGiaTriBQ(params);
                EventListenerDiemDanhs(params);
                break;
            //Kết thúc
            case 1:
                KetThucBQ(messages);
                idbieuquyet = 0;
                break;
        }
    }
    // Xử lý đồng hồ đếm ngược
    function HandleCountDown(messages) {
        var tg_bieuquyet = messages.thoigian_bieuquyet;
        var tg_batdau = messages.thoigian_batdau;
        var totalSeconds = parseFloat(tg_bieuquyet) * 60;
        var server_end = new Date(tg_batdau).setSeconds(new Date(tg_batdau).getSeconds() + totalSeconds);
        if (time_delay == 0) {
            var start_time = new Date().getTime();
            $.ajax({
                type: 'get',
                url: '/api/time',
                success: function(time) {
                    var end_time = new Date().getTime();
                    var server_time = new Date(time).getTime();
                    var client_synced = server_time + (end_time - start_time)/2;
                    time_delay = client_synced - new Date().getTime();
                    if (!myInterval) {
                        CountDown(server_end, myInterval);
                        myInterval = setInterval(function() {
                            CountDown(server_end, myInterval);
                        }, 1000);
                    }
                },
                error: function(err) {
                    console.log(err);
                    toastr.error('Lấy thời gian từ server lỗi');
                }
            });
        } else {
            if (!myInterval) {
                CountDown(server_end, myInterval);
                myInterval = setInterval(function() {
                    CountDown(server_end, myInterval);
                }, 1000);
            }
        }
    }
    function HandleEventGiaTriBieuQuyet(messages) {
        if (idbieuquyet == messages.bieuquyetid) {
            var objIndex = daibieus.findIndex((x => x.user_id == messages.userid));
            if (objIndex >= 0) {
                daibieus[objIndex].gia_tri = messages.giatri;
            }
            DisplayKetQuaBQ(daibieus);
        } else {
            console.log("Idbieuquyet != messages.bieuquyetid");
        }
    }
    function HandleEventDiemDanh(messages) {
        var objIndex = daibieus.findIndex((x => x.user_id == messages.userid));
        var trangthai_diemdanh = messages.trangthai == 1 ? 0 : 1;
        if (objIndex >= 0) {
            // Trạng thái = 1 (vắng mặt) => vắng mặt, ngược lại => có mặt
            daibieus[objIndex].trangthai_diemdanh = trangthai_diemdanh;
        } else {
            daibieus.push({
                "user_id": messages.userid,
                "trangthai_diemdanh": trangthai_diemdanh,
                "gia_tri": null
            });
        }
        DisplayKetQuaBQ(daibieus);
    }
    // Xử lý tỷ lệ biểu quyết
    function HandleTyleBQ(messages) {
        DisplayKetQuaBQ(daibieus);
    }
    // Kết thúc biểu quyết
    function KetThucBQ(messages) {
        myInterval = clearInterval(myInterval);
        for(var index = 0; index < daibieus.length; index++) {
            daibieus[index].gia_tri = null;
        }
        listenedGiaTriBQ = false;
        document.getElementById("dongho").innerHTML = "";
    }
    // Lấy kết quả biểu quyết đang diễn ra
    function GetKetQuaBieuQuyet(bieuquyetid) {
        $.ajax({
                type: 'post',
                url: '/admin/bieu-quyet/ket-qua',
                data: {
                    "_token": "{{ csrf_token() }}",
                    bieuquyetid: bieuquyetid,
                    buoihopid: buoihopid,
                    kyhopid: kyhopid
                },
                success: function(result) {
                    if (result.error_code == 0) {
                        DisplayKetQuaBQ(result.data);
                    }
                },
                error: function(err) {
                    console.log(err);
                }
            });
    }
</script>

