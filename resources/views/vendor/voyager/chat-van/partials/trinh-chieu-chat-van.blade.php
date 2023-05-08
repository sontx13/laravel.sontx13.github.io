<head>
    <title>Trình chiếu chất vấn</title>
</head>
<div class="content-trinhchieu-chatvan container no-padding">
    <div class="row">
        <div class="col-sm-12 text-center title display-table">
            <span class="vertical-middle">PHIÊN CHẤT VẤN</span>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <table class="table table-borderd">
                <tbody>
                    <tr>
                        <td colspan="2">
                            @if ($phienchatvan != null)
                                <span id="nguoitraloi">{{$phienchatvan->phien_chatvan}}</span>
                            @else
                                <span id="nguoitraloi"></span>
                            @endif
                        </td>
                        <td class="with-30 dongho text-center">
                            @if ($phienchatvan != null)
                                @if ($phienchatvan->trang_thai == App\Enums\TrangThaiPhienChatVanEnum::DaHoanThanh)
                                    <span>{{App\Http\Controllers\Voyager\ChatVanController::GetDisplayTTPhienChatVan($phienchatvan->trang_thai)}}</span>
                                @else
                                    <span id="phienDongHo"></span>
                                @endif
                            @else
                                <span id="phienDongHo"></span>
                            @endif
                        </td>
                    </tr>

                    @php
                        $index = 1;
                    @endphp
                    @if ($phienchatvan != null && !$isTrinhChieu)
                        @foreach ($phienchatvan->chatvans as $keyItem => $dataItem)
                            @if ($dataItem->trang_thai == App\Enums\TrangThaiChatVanEnum::DangChatVan || $dataItem->trang_thai == App\Enums\TrangThaiChatVanEnum::DaChatVan)
                                <tr>
                                    <td class="text-center">
                                        <span>{{$index}}</span>
                                    </td>
                                    <td>
                                        <span>Đại biểu {{$dataItem->user->name}}</span>
                                    </td>
                                    <td class="with-30 dongho text-center">
                                        @if ($dataItem->trang_thai == App\Enums\TrangThaiChatVanEnum::DangChatVan)
                                            <span id="tgdaibieu-{{$dataItem->id}}">00:00</span>
                                        @elseif ($dataItem->trang_thai == App\Enums\TrangThaiChatVanEnum::DaChatVan)
                                            <span>{{App\Http\Controllers\Voyager\ChatVanController::GetDisplayTTChatVan($dataItem->trang_thai)}}</span>
                                        @endif
                                    </td>
                                </tr>
                                @php
                                    $index++;
                                @endphp
                            @endif
                        @endforeach
                    @endif
                </tbody>
            </table>
            <table class="table table-borderd">
                <tbody id="ds-daibieus">
                </tbody>
            </table>
        </div>
    </div>
    <div class="row footer-trinhchieu text-center">
        <div class="col-sm-12 display-table">
            <span class="vertical-middle">{{$kyhop->khoahop->ten_khoa_hop}}, {{$kyhop->ten_ky_hop}}</span>
        </div>
    </div>
</div>
<link rel="stylesheet" href="/css/bootstrap.min.css">
<link rel="stylesheet" href="/css/style.css">
<style>
    body {
        background-color:#007bff;
    }
</style>
<script src="/js/firebase-config.js"></script>
<script src="/js/firebase-app.js"></script>
<script src="/js/firebase-database.js"></script>
<script>
    var tenantId = "<?php Print($tenantId); ?>"; // Id của tenant
    var kyhopid = "<?php Print($kyhopid); ?>"; // Id của kỳ họp
    var phienchatvanid = "<?php Print($phienchatvanid); ?>"; // Id của phiên chất vấn đang diễn ra
    var isTrinhChieu = "<?php Print($isTrinhChieu); ?>"; // View có phải là trình chiếu hay xem kết quả -view trình chiếu thì nghe firebase, xem kết quả
    var tgBatDauPhienStr = "<?php Print($thoigianbatdau); ?>"; //thời gian bắt đầu của phiên chất vấn đang diễn ra
    var tgPhienChatVanStr = "<?php Print($thoigianphien); ?>"; //thời gian phiên chất vấn đang diễn ra
    var chatvanStr = '<?php Print($chatvan); ?>'; // danh sách đại biểu chất vấn của phiên đang diễn ra (đang chất vấn + đã chất vấn)
    var chatvans = [];
    if (chatvanStr != '') {
        chatvans = JSON.parse(chatvanStr);
    }
    var countDownChatVans = []; // danh sách countdown của phiên chất vấn để hiển thị thời gian countdown
    var expireTimePhienChatVan = null; // thời gian hết hạn của phiên chất vấn

    var listenedPhienCVDangDienRa = false; // flag đánh dấu đã nghe sự kiện phiên chất vấn đang diễn ra chưa
    var listenedDaiBieuChatVan = false; // flag đánh dấu đã nghe sự kiện đại biểu chất chưa
    var timer = null; // timer interval
    // là view trình chiếu sẽ nghe event firebase
    if (isTrinhChieu) {
        // Nghe event firebase
        // Khởi tạo firebase nếu là view trình chiếu
        if (!firebase.apps.length) {
            firebase.initializeApp(firebaseConfig);
            var database = firebase.database();
            // ref biểu quyết đang diễn ra
            var params = {
                    "tenantId": tenantId,
                    "kyhopid": kyhopid,
                    "phienchatvanid": phienchatvanid,
                    "database": database
            };
            EventListenerPhienCVDangDienRa(params);
            // Trường hợp có phiên chất vấn đang diễn ra => nghe event đại biểu chất vấn
            if (phienchatvanid > 0) {
                // event đại biểu chất vấn
                EventListenerDaiBieuChatVan(params);
            }
        }
    }
    var ClearIntervalPhien = false;
    var ClearIntervalChatVan = 0;
    // Tồn tại id phiên chất vấn đang diễn ra
    if (phienchatvanid > 0) {
        // Thời gian hết hạn phiên
        expireTimePhienChatVan = getExpireTime(new Date(tgBatDauPhienStr), parseInt(tgPhienChatVanStr));
        displayDataPhienChatVan();
    }
    // Hàm hiẻn thị dữ liệu phiên chất vấn
    function displayDataPhienChatVan() {
        countDownChatVans = [];
        chatvans.forEach(chatvanItem => {
            // Trạng thái đang chất vấn
            if (chatvanItem.thoigian_batdau && chatvanItem.trang_thai == 2) {
                var expireTime = getExpireTime(new Date(chatvanItem.thoigian_batdau), parseInt(chatvanItem.thoigian_dangky));
                countDownChatVans.push({
                    "id": chatvanItem.id,
                    "expire_time": expireTime
                });
            }
        });
        displayDaiBieuChatVans(chatvans);
        if (!timer) {
            displayCountDown(timer);
            timer = setInterval(function() {
                displayCountDown(timer);
            }, 1000);
        }
    }
    // Hàm hiển thị danh sách đại biểu chất vấn trên view của phiên dựa vào array đại biểu chất vấn
    function displayDaiBieuChatVans(daibieuChatVans) {
        resetItemDaiBieuChatVan();
        var dbChatvans = daibieuChatVans.sort((a, b) => b.trang_thai == 2 || b.stt > a.stt ? 1 : -1);
        var indexDaiBieu = 1;
        for(var index =0; index < dbChatvans.length; index++) {
            if (dbChatvans[index].trang_thai == 2 || dbChatvans[index].trang_thai == 3) {
                addItemDaiBieuChatVan(dbChatvans[index], indexDaiBieu);
                indexDaiBieu++;
            }
        }
    }
    // hàm add đại biểu chất vấn vào danh sách trên view
    function addItemDaiBieuChatVan(chatvan, index) {
        const row = document.createElement("tr");
        // td index
        const tdIndex = document.createElement("td");
        tdIndex.classList.add("text-center");
        const spanIndex = document.createElement("span");
        spanIndex.textContent= index;
        tdIndex.appendChild(spanIndex);
        // td tên đại biểu
        const tdDaiBieu = document.createElement("td");
        const spanDaiBieu = document.createElement("span");
        spanDaiBieu.textContent= "Đại biểu " + chatvan.user.name;
        tdDaiBieu.appendChild(spanDaiBieu);
        // td coutdown
        const tdCountDown = document.createElement("td");
        tdCountDown.classList.add("text-center");
        tdCountDown.classList.add("with-30");
        tdCountDown.classList.add("dongho");
        const spanCountDown = document.createElement("span");
        // Đang chất vấn
        if (chatvan.trang_thai == 2) {
            spanCountDown.id = "tgdaibieu-" + chatvan.id;
        }
        // Đã chất vấn
        else {
            spanCountDown.textContent = "Đã chất vấn";
        }
        tdCountDown.appendChild(spanCountDown);

        row.appendChild(tdIndex);
        row.appendChild(tdDaiBieu);
        row.appendChild(tdCountDown);
        document.getElementById("ds-daibieus").appendChild(row);
    }
    // hàm xóa danh sách đại biểu chất vấn trên view
    function resetItemDaiBieuChatVan() {
        var dsDaiBieus = document.getElementById("ds-daibieus");
        while (dsDaiBieus.firstChild) {
            dsDaiBieus.removeChild(dsDaiBieus.lastChild);
        }
    }
    // hàm trả về expire time với param là thời gian bắt đầu + thời gian countdown cần đếm ngược
    function getExpireTime(timeStart, timeCountDown) {
        timeStart.setMinutes(timeStart.getMinutes() + timeCountDown);
        return timeStart.getTime();
    }

    // hàm hiển thị đồng hồ countdown của phiên chất vấn + danh sách đại biểu đang chất vấn
    // flag isDrawCountDownPhienChatVan = true sẽ vẽ lại countdown của phiên chất vấn
    function displayCountDown(timer) {
        displayCountDownPhienChatVan();
        if (ClearIntervalChatVan < countDownChatVans.length) {
            countDownChatVans.forEach(chatvanItem => {
                displayCountDownChatVan(chatvanItem);
            })
        }
    }
    // hàm hiển thị đồng hồ countdown của phiên chất vấn
    function displayCountDownPhienChatVan() {
        var objResultPhien = calculateTimeCountDown(expireTimePhienChatVan);
        document.getElementById("phienDongHo").innerHTML = objResultPhien.timeDisplay;
        // Đếm hết thời gian hiển thị giá trị về 0
        console.log('distacne phiên: ' + objResultPhien.distance);
        if (objResultPhien.distance < 0) {
            if (ClearIntervalPhien && ClearIntervalChatVan == countDownChatVans.length) {
                clearInterval(timer);
            }
            ClearIntervalPhien = true;
            document.getElementById("phienDongHo").innerHTML = "00:00";
        }
    }
    // hàm hiển thị đồng hồ countdown của đại biểu chất vấn
    function displayCountDownChatVan(chatvan) {
        var objResultChatVan = calculateTimeCountDown(chatvan.expire_time);
        // Đếm hết thời gian hiển thị giá trị về 0
        console.log('distacne chất vấn ' + chatvan.id + ' : ' + objResultChatVan.distance);
        if (objResultChatVan.distance < 0) {
            if (ClearIntervalPhien && ClearIntervalChatVan == countDownChatVans.length) {
                clearInterval(timer);
            }
            ClearIntervalChatVan++;
            document.getElementById("tgdaibieu-" + chatvan.id).innerHTML = "00:00";
        } else {
            document.getElementById("tgdaibieu-" + chatvan.id).innerHTML = objResultChatVan.timeDisplay;
        }
    }
    // hàm tính thời gian countdown còn lại dựa vào expire time
    function calculateTimeCountDown(expireTime) {
        var now = new Date().getTime();
        //lấy khoảng thời gian từ lúc thời gian hết hạn đến thời gian hiện tại
        var distance = expireTime - now;
        //Tính phút vs giây hiển thị
        var minutes = Math.floor(distance/ (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);
        // Display the result in the element with id="demo"
        var timeDisplay = "";
        timeDisplay += minutes < 10 ? "0" + minutes + ":" : minutes + ":";
        timeDisplay += seconds < 10 ? "0" + seconds : seconds;
        return {
            "distance" : distance,
            "timeDisplay" : timeDisplay
        }
    }

    // hàm lắng nghe sự kiện phiên chất vấn đang diễn ra
    function EventListenerPhienCVDangDienRa(params) {
        if (!listenedPhienCVDangDienRa) {
            listenedPhienCVDangDienRa = true;
            const fetchPhienCVs = params.database.ref(params.tenantId + "/chatvans/" + params.kyhopid + "/dangdienra");
            // Kết thúc phiên chất vấn
            fetchPhienCVs.on("child_removed", function (snapshot) {
                const messages = snapshot.val();
                console.log("ref phiên chất vấn đang diễn ra child_removed" + JSON.stringify(messages));
                HandleEventTrangThaiPhienChatVan(1, messages);
            });
            // Bắt đầu phiên chất vấn
            fetchPhienCVs.on("child_added", function (snapshot) {
                const messages = snapshot.val();
                console.log("ref phiên chất vấn đang diễn ra child_added" + JSON.stringify(messages));
                HandleEventTrangThaiPhienChatVan(0, messages, params);
            });
        }
    }

    // hàm lắng nghe sự kiện đại biểu chất vấn
    function EventListenerDaiBieuChatVan(params) {
        if (!listenedDaiBieuChatVan) {
            listenedDaiBieuChatVan = true;
            const fetchDaiBieuChatVans = params.database.ref(params.tenantId + "/chatvans/" + params.kyhopid + "/" + params.phienchatvanid);
            fetchDaiBieuChatVans.on("child_added", function (snapshot) {
                const messages = snapshot.val();
                console.log("ref đại biểu chất vấn child_added" + JSON.stringify(messages));
                HandleEventDaiBieuChatVan(messages, 0);
            });

            fetchDaiBieuChatVans.on("child_removed", function (snapshot) {
                const messages = snapshot.val();
                console.log("ref đại biểu chất vấn child_remove" + JSON.stringify(messages));
                HandleEventDaiBieuChatVan(messages, 1);
            });

            fetchDaiBieuChatVans.on("child_changed", function (snapshot) {
                const messages = snapshot.val();
                console.log("ref đại biểu chất vấn child_remove" + JSON.stringify(messages));
                HandleEventDaiBieuChatVan(messages, 2);
            });
        }
    }

    // hàm handle xử lý khi nhận được sự kiện đại biểu chất vấn
    function HandleEventDaiBieuChatVan(messages, type) {
        if (phienchatvanid == messages.phienchatvanid) {
            var userid = messages.userid;
            var userIndex = chatvans.findIndex((x => x.nguoi_chatvan == userid));
            switch(type) {
                // Bắt đầu chất vấn
                case 0:
                // Kết thúc chất vấn
                case 2:
                    if (userIndex >= 0) {
                        chatvans[userIndex].trang_thai = messages.trang_thai ? messages.trang_thai : chatvans[userIndex].trang_thai;
                        chatvans[userIndex].thoigian_batdau = messages.thoigian_batdau ? messages.thoigian_batdau : chatvans[userIndex].thoigian_batdau;
                        chatvans[userIndex].thoigian_dangky = messages.thoigian_chatvan ? messages.thoigian_chatvan : chatvans[userIndex].thoigian_dangky;
                    } else {
                        var itemChatvan = {
                            "id": messages.chatvanid,
                            "trang_thai": messages.trang_thai,
                            "thoigian_batdau": messages.thoigian_batdau,
                            "thoigian_dangky": messages.thoigian_chatvan,
                            "nguoi_chatvan" : messages.userid,
                            "user": {
                                "name": messages.user_name
                            }
                        };
                        chatvans.push(itemChatvan);
                    }
                    break;
                // Xóa đại biểu chất vấn
                case 1:
                    if (userIndex >= 0) {
                        chatvans.splice(userIndex, 1);
                    }
                    break;
            }
            displayDataPhienChatVan();
        } else {
            console.log("phienchatvanid != messages.phienchatvanid");
        }
    }
    // hàn handle Xử lý khi nhận được sự kiện bắt đầu, kết thúc biểu quyết
    function HandleEventTrangThaiPhienChatVan(type_event, messages, params) {
        switch(type_event) {
            //Bắt đầu
            case 0:
                // Handle count down phiên chất vấn
                HandleCountDownPhienChatVan(messages);
                phienchatvanid = messages.phienchatvanid;
                params.phienchatvanid = phienchatvanid;
                // nghe event đại biểu chất vấn
                EventListenerDaiBieuChatVan(params);
                break;
            //Kết thúc
            case 1:
                HandleKetThucPhienChatVan(messages);
                phienchatvanid = 0;
                break;
        }
    }
    // hàm handle xử lý hiển thị countdown của phiên chất vấn
    function HandleCountDownPhienChatVan(messages) {
        var idPhien = messages.phienchatvanid;
        HandleDisplayNguoiTraLoi(messages.nguoitraloi);
        var tg_batdau = messages.thoigian_batdau;
        var thoigiantraloi = messages.thoigian_traloi;
        var totalSeconds = parseFloat(thoigiantraloi) * 60;
        expireTimePhienChatVan = new Date(tg_batdau).setSeconds(new Date(tg_batdau).getSeconds() + totalSeconds);
        if (!timer) {
            displayCountDown(timer);
            timer = setInterval(function() {
                displayCountDown(timer);
            }, 1000);
        }
    }
    // hàm handle xử lý hiển thị người trả lời
    function HandleDisplayNguoiTraLoi(nguoitraloi) {
        var donghoElement = document.getElementById("nguoitraloi");
        donghoElement.innerHTML = nguoitraloi;
    }
    // hàm handle xử lý khi kết thúc phiên chất vấn
    function HandleKetThucPhienChatVan(messages) {

        timer = clearInterval(timer);
        chatvans = [];
        expireTimePhienChatVan = null;
        listenedDaiBieuChatVan = false;
        document.getElementById("nguoitraloi").innerHTML = "";
        document.getElementById("phienDongHo").innerHTML = "";
        resetItemDaiBieuChatVan();
    }
</script>


