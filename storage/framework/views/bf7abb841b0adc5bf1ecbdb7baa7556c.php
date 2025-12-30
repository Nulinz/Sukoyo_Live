<?php $__env->startSection('content'); ?>

    <link rel="stylesheet" href="<?php echo e(asset('assets/css/dashboard_main.css')); ?>">

    <div class="body-div p-3">

        <?php echo $__env->make('dashboard.dashboard_tabs', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <!-- Cards -->
        <div class="container-fluid px-0">
            <div class="row d-flex flex-wrap" id="main_card">

                <div class="body-head mb-3">
                    <h4 class="m-0">Overview</h4>
                </div>
             <div class="col-sm-6 col-md-4 col-xl-3 mb-3 cards">
    <div class="cardsdiv">
        <div class="cardsmain">
            <div>
                <h6>Today Sales</h6>
                <h5>₹ <?php echo e(number_format($todaySales, 2)); ?></h5>
            </div>
            <img src="<?php echo e(asset('assets/images/icon_1.png')); ?>" height="50px" alt="">
        </div>
        <div class="cardssub d-block">
            <?php
                $isUp = $salesDifference >= 0;
                $arrowClass = $isUp ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down';
                $color = $isUp ? 'var(--green)' : 'var(--red)';
                $diffText = $isUp ? 'Up from yesterday' : 'Down from yesterday';
            ?>
            <h5>
                <span style="color: <?php echo e($color); ?>;">
                    <i class="fas <?php echo e($arrowClass); ?> pe-1"></i>
                    <?php echo e(number_format(abs($salesDifference), 1)); ?>%
                </span>
                <?php echo e($diffText); ?>

            </h5>
        </div>
    </div>
</div>

            <div class="col-sm-6 col-md-4 col-xl-3 mb-3 cards">
    <div class="cardsdiv">
        <div class="cardsmain">
            <div>
                <h6>Today Purchase</h6>
                <h5>₹ <?php echo e(number_format($todayPurchase, 2)); ?></h5>
            </div>
            <img src="<?php echo e(asset('assets/images/icon_2.png')); ?>" height="50px" alt="">
        </div>
        <div class="cardssub d-block">
            <?php
                $isUpPurchase = $purchaseDifference >= 0;
                $purchaseArrow = $isUpPurchase ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down';
                $purchaseColor = $isUpPurchase ? 'var(--green)' : 'var(--red)';
                $purchaseText = $isUpPurchase ? 'Up from past week' : 'Down from past week';
            ?>
            <h5>
                <span style="color: <?php echo e($purchaseColor); ?>;">
                    <i class="fas <?php echo e($purchaseArrow); ?> pe-1"></i>
                    <?php echo e(number_format(abs($purchaseDifference), 1)); ?>%
                </span>
                <?php echo e($purchaseText); ?>

            </h5>
        </div>
    </div>
</div>

               <div class="col-sm-6 col-md-4 col-xl-3 mb-3 cards">
    <div class="cardsdiv">
        <div class="cardsmain">
            <div>
                <h6>Today Bills</h6>
                <h5><?php echo e($todayBills); ?></h5>
            </div>
            <img src="<?php echo e(asset('assets/images/icon_3.png')); ?>" height="50px" alt="">
        </div>
        <div class="cardssub d-block">
            <?php
                $isUpBills = $billDifference >= 0;
                $billArrow = $isUpBills ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down';
                $billColor = $isUpBills ? 'var(--green)' : 'var(--red)';
                $billText = $isUpBills ? 'Up from yesterday' : 'Down from yesterday';
            ?>
            <h5>
                <span style="color: <?php echo e($billColor); ?>;">
                    <i class="fas <?php echo e($billArrow); ?> pe-1"></i>
                    <?php echo e(number_format(abs($billDifference), 1)); ?>%
                </span>
                <?php echo e($billText); ?>

            </h5>
        </div>
    </div>
</div>
<div class="col-sm-6 col-md-4 col-xl-3 mb-3 cards">
    <div class="cardsdiv">
        <div class="cardsmain">
            <div>
                <h6>To Pay</h6>
                <h5>₹ <?php echo e(number_format($toPayAmount, 2)); ?></h5>
            </div>
            <img src="<?php echo e(asset('assets/images/icon_8.png')); ?>" height="50px" alt="">
        </div>
        <div class="cardssub d-block">
            <h5>
                For <?php echo e($vendorsToPay); ?> Vendor<?php echo e($vendorsToPay > 1 ? 's' : ''); ?>

            </h5>
        </div>
    </div>
</div>


                <div class="body-head my-3">
                    <h4 class="m-0">Customer Insights</h4>
                </div>
               <div class="col-sm-6 col-md-4 col-xl-4 mb-3 cards">
                   <div class="cardsdiv">
                        <div class="cardsmain">
                            <div>
                                <h5 class="mb-1">Total Customers</h5>
                                <h6 class="mb-2">With Loyalty Points</h6>
                                <h4 class="mb-0"><?php echo e($customersWithLoyalty); ?></h4>
                            </div>
                            <img src="<?php echo e(asset('assets/images/icon_9.png')); ?>" height="75px" alt="">
                        </div>
                    </div>
                </div>

              <div class="col-sm-6 col-md-4 col-xl-4 mb-3 cards">
    <div class="cardsdiv">
        <div class="cardsmain">
            <div>
                <h5 class="mb-1">Today New Customers</h5>
                <h6 class="mb-1">With Loyalty Points</h6>
                <h4 class="mb-0"><?php echo e($todayNewCustomersWithLoyalty); ?></h4>
            </div>
            <div class="progress-circle" id="circle1">
                <div class="percentage" id="percent1">0%</div>
            </div>
        </div>
    </div>
</div>

                <div class="col-sm-6 col-md-4 col-xl-4 mb-3 cards">
    <div class="cardsdiv">
        <div class="cardsmain">
            <div>
                <h5 class="mb-1">Returning Customers</h5>
                <h6 class="mb-2">With Loyalty Points</h6>
                <h4 class="mb-0"><?php echo e($returningCustomersWithLoyalty); ?></h4>
            </div>
            <div class="progress-circle" id="circle2">
                <div class="percentage" id="percent2">0%</div>
            </div>
        </div>
    </div>
</div>

            </div>
        </div>

        <!-- Charts -->
        <div class="container-fluid px-0">
            <div class="row d-flex flex-wrap" id="main_card">

                <div class="body-head my-3">
                    <h4 class="m-0">Product Performance</h4>
                </div>
                <div class="col-sm-12 col-md-6 mb-3 cards">
                    <div class="cardsdiv">
                        <div class="cardshead">
                            <h6>Top Selling Products</h6>
                            <select class="form-select" name="month" id="topSellingMonthSelect">
                                <option value="" disabled>Select Month</option>
                                <?php for($m = 1; $m <= 12; $m++): ?>
                                    <option value="<?php echo e($m); ?>" <?php echo e($m == date('n') ? 'selected' : ''); ?>>
                                        <?php echo e(date('F', mktime(0, 0, 0, $m, 1))); ?>

                                    </option>
                                <?php endfor; ?>
                            </select>


                        </div>
                        <div class="chartsdiv">
                            <div id="chart1"></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6 mb-3 cards">
                    <div class="cardsdiv">
                        <div class="cardshead">
                            <h6>Least Selling Products</h6>
                          <select class="form-select" id="leastSellingMonthSelect">
        <option value="" disabled>Select Month</option>
        <?php for($m = 1; $m <= 12; $m++): ?>
            <option value="<?php echo e($m); ?>" <?php echo e($m == date('n') ? 'selected' : ''); ?>>
                <?php echo e(date('F', mktime(0, 0, 0, $m, 1))); ?>

            </option>
        <?php endfor; ?>
    </select>
                        </div>
                        <div class="chartsdiv">
                            <div id="chart2"></div>
                        </div>
                    </div>
                </div>

                <div class="body-head my-3">
                    <h4 class="m-0">Financial Summary</h4>
                </div>
                <div class="col-sm-12 col-md-6 mb-3 cards">
                    <div class="cardsdiv">
                        <div class="cardshead">
                            <h6>Gross Avenue</h6>
                        </div>
                        <div class="chartsdiv">
                            <div id="chart3"></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6 mb-3 cards">
                    <div class="cardsdiv">
                        <div class="cardshead">
                            <h6>Loyalty Discount</h6>
                        </div>
                        <div class="chartsdiv">
                            <div id="chart4"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Charts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    let target1 = <?php echo e($loyaltyTargetPercent); ?>;
    let target2 = <?php echo e($returningCustomersPercent); ?>;
    
    let current1 = 0;
    const circle1 = document.getElementById('circle1');
    const percent1 = document.getElementById('percent1');
    const interval1 = setInterval(() => {
        if (current1 <= target1) {
            circle1.style.background = `conic-gradient(#4A3AFF ${current1 * 3.6}deg, #eee ${current1 * 3.6}deg)`;
            percent1.textContent = current1 + '%';
            current1++;
        } else {
            clearInterval(interval1);
        }
    }, 20);

    let current2 = 0;
    const circle2 = document.getElementById('circle2');
    const percent2 = document.getElementById('percent2');
    const interval2 = setInterval(() => {
        if (current2 <= target2) {
            circle2.style.background = `conic-gradient(#4A3AFF ${current2 * 3.6}deg, #eee ${current2 * 3.6}deg)`;
            percent2.textContent = current2 + '%';
            current2++;
        } else {
            clearInterval(interval2);
        }
    }, 20);
</script>
<script>
    $('#monthSelector').on('change', function() {
    let month = $(this).val();
    $.get('/top-selling-products', { month: month }, function(response) {
        topSellingChart.updateOptions({
            xaxis: { categories: response.products },
            series: [{ name: 'Qty Sold', data: response.quantities }]
        });
    });
});
 
</script>
<script>
    document.getElementById('topSellingMonthSelect').addEventListener('change', function () {
    const month = this.value;
    fetch(`/dashboard/top-selling-products/${month}`)
        .then(res => res.json())
        .then(data => {
            const options = {
                series: [{
                    data: data.quantities
                }],
                colors: ['#4A3AFF'],
                chart: {
                    type: 'bar',
                    height: 350
                },
                plotOptions: {
                    bar: {
                        borderRadiusApplication: 'end',
                        horizontal: false,
                    }
                },
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories: data.products
                }
            };

            document.querySelector("#chart1").innerHTML = ""; // clear previous chart
            const chart = new ApexCharts(document.querySelector("#chart1"), options);
            chart.render();
        });
});

</script>
<script>
    document.getElementById('leastSellingMonthSelect').addEventListener('change', function () {
        const month = this.value;
        fetch(`/dashboard/least-selling-products/${month}`)
            .then(res => res.json())
            .then(data => {
                const options = {
                    series: [{
                        data: data.quantities
                    }],
                    chart: {
                        type: 'bar',
                        height: 350
                    },
                    colors: ['#49b4f2ff'],
                    plotOptions: {
                        bar: {
                            borderRadius: 4,
                            borderRadiusApplication: 'end',
                            horizontal: true,
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    xaxis: {
                        categories: data.products
                    }
                };

                document.querySelector("#chart2").innerHTML = ""; // clear previous chart
                const chart2 = new ApexCharts(document.querySelector("#chart2"), options);
                chart2.render();
            });
    });

    // Auto-load on page load
    document.addEventListener("DOMContentLoaded", function () {
        const currentMonth = new Date().getMonth() + 1;
        fetch(`/dashboard/least-selling-products/${currentMonth}`)
            .then(res => res.json())
            .then(data => {
                const options = {
                    series: [{
                        data: data.quantities
                    }],
                    chart: {
                        type: 'bar',
                        height: 350
                    },
                    colors: ['#49b4f2ff'],
                    plotOptions: {
                        bar: {
                            borderRadius: 4,
                            borderRadiusApplication: 'end',
                            horizontal: true,
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    xaxis: {
                        categories: data.products
                    }
                };

                document.querySelector("#chart2").innerHTML = "";
                const chart2 = new ApexCharts(document.querySelector("#chart2"), options);
                chart2.render();
            });
    });
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    fetch("/dashboard/gross-avenue-data")
        .then(res => res.json())
        .then(data => {
            var options = {
                chart: {
                    type: 'area',
                    height: 350
                },
                colors: ['#003488', '#bc5090'],
                series: [
                    {
                        name: 'Last Year',
                        data: data.last_year
                    },
                    {
                        name: 'This Year',
                        data: data.this_year
                    }
                ],
                xaxis: {
                    categories: [
                        'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                        'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
                    ]
                },
                title: {
                    show: false
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 2,
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.4,
                        opacityTo: 0.1,
                        stops: [0, 90, 100]
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#chart3"), options);
            chart.render();
        });
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    fetch("/dashboard/loyalty-discount-data")
        .then(res => res.json())
        .then(data => {
            const options = {
                series: [
                    {
                        name: 'Points Redeemed',
                        data: data.redeemed
                    },
                    {
                        name: 'Points Not Redeemed',
                        data: data.not_redeemed
                    }
                ],
                colors: ['#4A3AFF', '#C893FD'],
                chart: {
                    type: 'bar',
                    height: 350
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        borderRadius: 5,
                        borderRadiusApplication: 'end'
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: [
                        'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                        'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
                    ],
                },
                yaxis: {
                    title: false,
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val + " points"
                        }
                    }
                }
            };

            const chart4 = new ApexCharts(document.querySelector("#chart4"), options);
            chart4.render();
        });
});
</script>

  
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const currentMonth = new Date().getMonth() + 1; // JS months are 0-based

        // Auto-load top selling chart
        fetch(`/dashboard/top-selling-products/${currentMonth}`)
            .then(res => res.json())
            .then(data => {
                const options = {
                    series: [{ data: data.quantities }],
                    colors: ['#4A3AFF'],
                    chart: { type: 'bar', height: 350 },
                    plotOptions: {
                        bar: {
                            borderRadiusApplication: 'end',
                            horizontal: false
                        }
                    },
                    dataLabels: { enabled: false },
                    xaxis: { categories: data.products }
                };

                document.querySelector("#chart1").innerHTML = "";
                const chart1 = new ApexCharts(document.querySelector("#chart1"), options);
                chart1.render();
            });
    });
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Sukoyo_Live\resources\views/dashboard/admin.blade.php ENDPATH**/ ?>