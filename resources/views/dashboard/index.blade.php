@extends('layouts.master')

@section('content')
<div class="row">
    <!-- BEGIN col-3 -->
    <div class="col-xl-3 col-md-6">
        <div class="widget widget-stats bg-white text-inverse">
            <div class="stats-icon stats-icon-square bg-gradient-cyan-blue text-white"><i class="ion-ios-cube"></i></div>
            <div class="stats-content">
                <div class="stats-title text-gray-700">TOTAL BARANG</div>
                <div class="stats-number">{{ $totalBarang }}</div>
                <div class="stats-progress progress">
                    <!-- <div class="progress-bar" style="width: 70.1%;"></div> -->
                </div>
                <div class="stats-desc text-gray-700">Monthly</div>
            </div>
        </div>
    </div>
    <!-- END col-3 -->
    <!-- BEGIN col-3 -->
    <div class="col-xl-3 col-md-6">
        <div class="widget widget-stats bg-white text-inverse">
            <div class="stats-icon stats-icon-square bg-gradient-cyan-blue text-white"><i class="ion-ios-close"></i></div>
            <div class="stats-content">
                <div class="stats-title text-gray-700">TOTAL LOSS</div>
                <div class="stats-number">{{ $totalLoss }}</div>
                <div class="stats-progress progress">
                    <!-- <div class="progress-bar" style="width: 40.5%;"></div> -->
                </div>
                <div class="stats-desc text-gray-700">Monthly</div>
            </div>
        </div>
    </div>
    <!-- END col-3 -->
    <!-- BEGIN col-3 -->
    <div class="col-xl-3 col-md-6">
        <div class="widget widget-stats bg-white text-inverse">
            <div class="stats-icon stats-icon-square bg-gradient-cyan-blue text-white"><i class="ion-ios-refresh"></i></div>
            <div class="stats-content">
                <div class="stats-title text-gray-700">TOTAL PENARIKAN</div>
                <div class="stats-number">{{ $totalPenarikan }}</div>
                <div class="stats-progress progress">
                    <!-- <div class="progress-bar" style="width: 76.3%;"></div> -->
                </div>
                <div class="stats-desc text-gray-700">Monthly</div>
            </div>
        </div>
    </div>
    <!-- END col-3 -->
    <!-- BEGIN col-3 -->
    <div class="col-xl-3 col-md-6">
        <div class="widget widget-stats bg-white text-inverse">
            <div class="stats-icon stats-icon-square bg-gradient-cyan-blue text-white"><i class="ion-ios-pricetags"></i></div>
            <div class="stats-content">
                <div class="stats-title text-gray-700">TOTAL PENJUALAN</div>
                <div class="stats-number">{{ $totalPenjualan }}</div>
                <div class="stats-progress progress">
                    <!-- <div class="progress-bar" style="width: 54.9%;"></div> -->
                </div>
                <div class="stats-desc text-gray-700">Monthly</div>
            </div>
        </div>
    </div>
    <!-- END col-3 -->
</div>
@endsection