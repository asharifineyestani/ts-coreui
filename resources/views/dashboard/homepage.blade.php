@extends('dashboard.base')

@section('content')

    <div class="container-fluid">
        <div class="fade-in">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-5">
                            <h4 class="card-title mb-0">آمار بازدید</h4>
                            {{--                            <div class="small text-muted">September 2019</div>--}}
                        </div>
                        <!-- /.col-->
                        <div class="col-sm-7 d-none d-md-block">
                            <div class="btn-group btn-group-toggle float-right mr-3" data-toggle="buttons">
                                <label class="btn btn-outline-secondary" onclick="getData('month')">
                                    <input id="option1" type="radio" name="options" autocomplete="off"> ماه
                                </label>
                                <label class="btn btn-outline-secondary " onclick="getData('year')">
                                    <input id="option2" type="radio" name="options" autocomplete="off" checked=""> سال
                                </label>
                                <label class="btn btn-outline-secondary active" onclick="getData('all')">
                                    <input id="option3" type="radio" name="options" autocomplete="off" > همه
                                </label>
                            </div>
                        </div>
                        <!-- /.col-->
                    </div>
                    <!-- /.row-->
                    <div class="c-chart-wrapper" style="height:300px;margin-top:40px;">
                        <canvas class="chart" id="main-chart" height="300"></canvas>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection

@section('javascript')
    <script src="{{ asset('js/coreui-chartjs.bundle.js') }}"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script>

        getData('all')

        function getData(sort = "all") {
            return axios.get( "/statistics/" + sort)
                .then(response => {
                    return response.data
                }).then(statistics => {
                    let count_data = statistics.count;
                    let label_data = statistics.labels;

                    const mainChart = new Chart(document.getElementById('main-chart'), {
                        type: 'line',
                        data: {
                            labels: label_data,
                            datasets: [
                                {
                                    label: 'بازدید روزانه',
                                    backgroundColor: "lightskyblue",
                                    borderColor: "black",
                                    pointHoverBackgroundColor: '#fff',
                                    borderWidth: 2,
                                    data: count_data
                                }
                            ]
                        },
                        options: {
                            maintainAspectRatio: false,
                            legend: {
                                display: false
                            },
                            scales: {
                                xAxes: [{
                                    gridLines: {
                                        drawOnChartArea: false
                                    }
                                }],
                                yAxes: [{
                                    ticks: {
                                        beginAtZero: true,
                                        maxTicksLimit: 5,
                                        stepSize: 1,
                                        max: Math.max(...count_data)
                                    }
                                }]
                            },
                            elements: {
                                point: {
                                    radius: 0,
                                    hitRadius: 10,
                                    hoverRadius: 4,
                                    hoverBorderWidth: 3
                                }
                            },
                            tooltips: {
                                intersect: true,
                                callbacks: {
                                    labelColor: function (tooltipItem, chart) {
                                        return {backgroundColor: chart.data.datasets[tooltipItem.datasetIndex].borderColor};
                                    }
                                }
                            }
                        }
                    })

                });
        }
    </script>
@endsection
