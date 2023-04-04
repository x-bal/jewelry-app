<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>{{ config('app.name') }} | Alert Page</title>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
    <meta content="" name="description" />
    <meta content="" name="author" />

    <!-- ================== BEGIN core-css ================== -->
    <link href="{{ asset('/') }}css/vendor.min.css" rel="stylesheet" />
    <link href="{{ asset('/') }}css/apple/app.min.css" rel="stylesheet" />
    <link href="{{ asset('/') }}plugins/ionicons/css/ionicons.min.css" rel="stylesheet" />
    <!-- ================== END core-css ================== -->

    <!-- ================== BEGIN page-css ================== -->
    <link href="{{ asset('/') }}plugins/countdown/jquery.countdown.css" rel="stylesheet" />
    <!-- ================== END page-css ================== -->
</head>

<body class='pace-top'>
    <!-- BEGIN #loader -->
    <div id="loader" class="app-loader">
        <span class="spinner"></span>
    </div>
    <!-- END #loader -->

    <!-- BEGIN #app -->
    <div id="app" class="app">
        <!-- BEGIN coming-soon -->
        <div class="coming-soon">
            <!-- BEGIN coming-soon-header -->
            <div class="coming-soon-header">
                <div class="bg-cover"></div>
                <div class="brand">
                    <span class="logo"><i class="ion-ios-cloud"></i></span> <b>{{ App\Models\Setting::first()->val ?? 'Jewelry App' }}</b>
                </div>
                <div class="desc">
                    Halaman ini berfungsi untuk memantau apabila terjadi kehilangan barang <br> dan akan berbunyi dan berfungsi sebagai alarm.

                    <span class="loss"></span>
                </div>
            </div>
            <!-- END coming-soon-header -->
            <!-- BEGIN coming-soon-content -->
            <div class="coming-soon-content">
                <div class="input-group input-group-lg mx-auto mb-2">
                    <audio controls id="myAudio" style="display: none;">
                        <source src="{{ asset('/storage/' . $alert->val) ?? asset('/alert.mp3') }}" type="audio/mp3">
                    </audio>
                </div>

                <div class="barang row"></div>
            </div>
        </div>
    </div>

    <!-- ================== BEGIN core-js ================== -->
    <script src="{{ asset('/') }}js/vendor.min.js"></script>
    <script src="{{ asset('/') }}js/app.min.js"></script>
    <script src="{{ asset('/') }}js/theme/apple.min.js"></script>
    <!-- ================== END core-js ================== -->

    <!-- ================== BEGIN page-js ================== -->
    <script src="{{ asset('/') }}plugins/countdown/jquery.plugin.min.js"></script>
    <script src="{{ asset('/') }}plugins/countdown/jquery.countdown.min.js"></script>
    <script src="{{ asset('/') }}js/demo/coming-soon.demo.js"></script>
    <!-- ================== END page-js ================== -->

    <script>
        $(document).ready(function() {
            let lossing = 0;
            let last = "{{ $last }}"
            let status = 0;

            setInterval(function() {
                $.ajax({
                    url: '/api/alert',
                    method: "GET",
                    type: "GET",
                    success: function(response) {
                        lossing = response.lossing;
                        let barang = response.barang;
                        let brg = response.total;

                        if (brg.length != 0) {
                            status = 1
                        } else {
                            status = 0;
                        }

                        barangs = JSON.parse(localStorage.getItem("barangs"));

                        for (var i = 0; i < brg.length; i++) {

                            if ($.inArray(brg[i], barangs) > -1) {
                                // console.log(brg[i]);
                            } else {
                                list()
                            }
                        }

                        localStorage.setItem("barangs", JSON.stringify(brg))

                        $(".loss").empty().append(`<br><br> <h2>Total Barang Hilang : ` + lossing + `</h2>`)

                        if (status == 1) {
                            $("#myAudio")[0].play();
                        } else {
                            $("#myAudio")[0].pause();
                        }
                    }
                })
            }, 1000)

            function list() {
                $(".barang").empty()

                $.ajax({
                    url: '/api/alert',
                    method: "GET",
                    type: "GET",
                    success: function(response) {
                        lossing = response.lossing;
                        let barang = response.barang;
                        let brg = response.total;

                        let asset = "{{ asset('/') }}";

                        $.each(barang, function(i, data) {
                            $(".barang").append(`<div class="col-md-3 mb-3" data-index="` + data.barang.old_rfid + `"><div class="card">
                                    <div class="h-250px rounded-top" style="background-image: url(` + asset + `storage/` + data.barang.foto + `); background-position: center; background-size: cover; background-repeat: no-repeat;"></div>
                                    <div class="card-body">
                                        <h4 class="card-title">` + data.barang.nama_barang + `</h4>
                                        <p class="card-text">` + data.barang.old_rfid + `</p>
                                    </div>
                                </div></div>`)
                        })
                    }
                })
            }
        })
    </script>
</body>

</html>