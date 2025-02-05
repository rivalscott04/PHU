@extends('layouts.app')

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">
                    Dashboard
                </h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-xl-4">
            <div class="card overflow-hidden">
                <div class="bg-primary bg-soft">
                    <div class="row">
                        <div class="col-7">
                            <div class="text-primary p-3">
                                <h5 class="text-primary">
                                    Selamat Datang Kembali !
                                </h5>
                                <p>KANWIL Dashboard</p>
                            </div>
                        </div>
                        <div class="col-5 align-self-end">
                            <img src="assets/images/profile-img.png" alt="" class="img-fluid" />
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="avatar-md profile-user-wid mb-4">
                                <img src="{{ asset('images/users/avatar-1.jpg') }}" alt=""
                                    class="img-thumbnail rounded-circle" />
                            </div>
                            <h5 class="font-size-15 text-truncate">
                                {{ $username }}
                            </h5>
                            <p class="text-muted mb-0 text-truncate">
                                {{ ucfirst($role) }}
                            </p>
                        </div>

                        <div class="col-sm-8">
                            <div class="pt-4">
                                <div class="row">
                                    <div class="col-6">
                                        <h5 class="font-size-15">
                                            {{ $diajukan }}
                                        </h5>
                                        <p class="text-muted mb-0">
                                            BAP Diajukan
                                        </p>
                                    </div>
                                    <div class="col-6">
                                        <h5 class="font-size-15">
                                            {{ $diproses }}
                                        </h5>
                                        <p class="text-muted mb-0">
                                            BAP Diproses
                                        </p>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <a href="javascript:void(0);" class="btn btn-primary waves-effect waves-light btn-sm">
                                        Lihat Profil
                                        <i class="mdi mdi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-8">
            <div class="row">
                <div class="col-md-4">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium">Jamaah Haji</p>
                                    <h4 class="mb-0">{{ $jamaahHaji }}</h4>
                                </div>
                                <div class="flex-shrink-0 align-self-center">
                                    <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                        <span class="avatar-title">
                                            <i class="bx bx-user font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium">Jamaah Umrah</p>
                                    <h4 class="mb-0">{{ $jamaahUmrah }}</h4>
                                </div>
                                <div class="flex-shrink-0 align-self-center">
                                    <div class="mini-stat-icon avatar-sm rounded-circle bg-success">
                                        <span class="avatar-title">
                                            <i class="bx bx-group font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium">BAP Selesai</p>
                                    <h4 class="mb-0">{{ $selesai }}</h4>
                                </div>
                                <div class="flex-shrink-0 align-self-center">
                                    <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                        <span class="avatar-title">
                                            <i class="bx bx-check font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end row -->

            <div class="card">
                <div class="card-body">
                    <div class="d-sm-flex flex-wrap">
                        <h4 class="card-title mb-4">
                            Jumlah Jamaah
                        </h4>
                    </div>

                    <div id="stacked-column-chart" class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- end row -->
    <!-- end modal -->
@endsection

@push('js')
    <script>
        // Get the data passed from PHP
        const monthlyData = {!! json_encode($monthlyData) !!};

        // Stacked Column Chart
        // Stacked Column Chart
        const stackedOptions = {
            chart: {
                height: 360,
                type: "bar",
                stacked: true,
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: true
                },
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: "15%",
                    endingShape: "rounded"
                },
            },
            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    return Math.round(val); // Membulatkan angka
                }
            },
            series: [{
                name: "Total Jamaah",
                data: monthlyData.map(item => item.total)
            }],
            xaxis: {
                categories: monthlyData.map(item => item.month)
            },
            yaxis: {
                labels: {
                    formatter: function(val) {
                        return Math.round(val); // Membulatkan angka pada sumbu Y
                    }
                }
            },
            colors: ["#556ee6"],
            legend: {
                position: "bottom"
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return Math.round(val); // Membulatkan angka pada tooltip
                    }
                }
            }
        };

        const stackedChart = new ApexCharts(
            document.querySelector("#stacked-column-chart"),
            stackedOptions
        );
        stackedChart.render();

        // Radial Bar Chart
        const radialOptions = {
            chart: {
                height: 200,
                type: "radialBar",
                offsetY: -10
            },
            plotOptions: {
                radialBar: {
                    startAngle: -135,
                    endAngle: 135,
                    dataLabels: {
                        name: {
                            fontSize: "13px",
                            color: undefined,
                            offsetY: 60
                        },
                        value: {
                            offsetY: 22,
                            fontSize: "16px",
                            color: undefined,
                            formatter: function(val) {
                                return Math.round(val) + "%"; // Membulatkan persentase
                            },
                        },
                    },
                },
            },
            colors: ["#556ee6"],
            fill: {
                type: "gradient",
                gradient: {
                    shade: "dark",
                    shadeIntensity: 0.15,
                    inverseColors: false,
                    opacityFrom: 1,
                    opacityTo: 1,
                    stops: [0, 50, 65, 91],
                },
            },
            stroke: {
                dashArray: 4
            },
            series: [{{ $growthPercentage }}],
            labels: ["Pertumbuhan Bulanan"]
        };

        const radialChart = new ApexCharts(
            document.querySelector("#radialBar-chart"),
            radialOptions
        );
        radialChart.render();
    </script>
@endpush
