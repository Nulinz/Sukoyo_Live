<?php $__env->startSection('content'); ?>

    <link rel="stylesheet" href="<?php echo e(asset('assets/css/dashboard_main.css')); ?>">

    <div class="body-div p-3">

        <?php echo $__env->make('dashboard.dashboard_tabs', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="body-head mb-3">
            <h4 class="m-0">ABC Dashboard</h4>
            <div class="d-flex align-items-center flex-wrap gap-2">
                <h5 class="text-decoration-none">Overall Inventory: <span id="overall-inventory">₹ 0</span></h5>
                <select class="form-select" name="store" id="stores">
                    <option value="" selected disabled>Select Store</option>
                    <?php $__currentLoopData = $stores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $store): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($store); ?>"><?php echo e($store); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>

        <div class="container-fluid px-0">
            <div class="row d-flex flex-wrap" id="main_card">
                <!-- Category A Card -->
                <div class="col-sm-6 col-md-4 mb-3 cards">
                    <div class="cardsdiv">
                        <div class="cardshead">
                            <div>
                                <h6>A - Category</h6>
                            </div>
                        </div>
                        <div class="cardssub">
                            <div>
                                <h6 class="text-start">No Of Items</h6>
                                <h5 class="text-start" id="category-a-items">0</h5>
                            </div>
                            <div class="brdr"></div>
                            <div>
                                <h6 class="text-center">Value</h6>
                                <h5 class="text-center" id="category-a-value">₹ 0</h5>
                            </div>
                            <div class="brdr"></div>
                            <div>
                                <h6 class="text-end">Percentage</h6>
                                <h5 class="text-end" id="category-a-percentage">0% Value</h5>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Category B Card -->
                <div class="col-sm-6 col-md-4 mb-3 cards">
                    <div class="cardsdiv">
                        <div class="cardshead">
                            <div>
                                <h6>B - Category</h6>
                            </div>
                        </div>
                        <div class="cardssub">
                            <div>
                                <h6 class="text-start">No Of Items</h6>
                                <h5 class="text-start" id="category-b-items">0</h5>
                            </div>
                            <div class="brdr"></div>
                            <div>
                                <h6 class="text-center">Value</h6>
                                <h5 class="text-center" id="category-b-value">₹ 0</h5>
                            </div>
                            <div class="brdr"></div>
                            <div>
                                <h6 class="text-end">Percentage</h6>
                                <h5 class="text-end" id="category-b-percentage">0% Value</h5>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Category C Card -->
                <div class="col-sm-6 col-md-4 mb-3 cards">
                    <div class="cardsdiv">
                        <div class="cardshead">
                            <div>
                                <h6>C - Category</h6>
                            </div>
                        </div>
                        <div class="cardssub">
                            <div>
                                <h6 class="text-start">No Of Items</h6>
                                <h5 class="text-start" id="category-c-items">0</h5>
                            </div>
                            <div class="brdr"></div>
                            <div>
                                <h6 class="text-center">Value</h6>
                                <h5 class="text-center" id="category-c-value">₹ 0</h5>
                            </div>
                            <div class="brdr"></div>
                            <div>
                                <h6 class="text-end">Percentage</h6>
                                <h5 class="text-end" id="category-c-percentage">0% Value</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid px-0">
            <div class="row d-flex flex-wrap" id="main_card">
                <div class="col-sm-12 col-md-4 mb-3 cards">
                    <div class="cardsdiv">
                        <div class="cardshead">
                            <h6>Items</h6>
                            <select class="form-select" name="" id="">
                                <option value="" selected disabled>Select Month</option>
                                <option value="This Week">This Week</option>
                                <option value="This Month">This Month</option>
                                <option value="Last 6 Months">Last 6 Months</option>
                            </select>
                        </div>
                        <div class="chartsdiv">
                            <div id="chart1"></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-8 mb-3 cards">
                    <div class="cardsdiv">
                        <div class="cardshead">
                            <h6>Stock Movement</h6>
                            <select class="form-select" name="" id="">
                                <option value="" selected disabled>Select Type</option>
                                <option value="Weekly">Weekly</option>
                                <option value="Monthly">Monthly</option>
                                <option value="Yearly">Yearly</option>
                            </select>
                        </div>
                        <div class="chartsdiv">
                            <div id="chart2"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Charts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        let chart1, chart2;
        
        // Initialize empty charts
        function initializeCharts() {
            // Donut Chart
            const donutOptions = {
                series: [],
                labels: [],
                colors: ['#7982B9', '#A5C1DC', '#E9F6FA'],
                chart: {
                    type: 'donut',
                    height: 330,
                },
                legend: {
                    position: 'bottom'
                },
                dataLabels: {
                    enabled: false
                },
                responsive: [{
                    breakpoint: 300,
                    options: {
                        chart: {
                            height: 320,
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };
            chart1 = new ApexCharts(document.querySelector("#chart1"), donutOptions);
            chart1.render();

            // Area Chart
            const areaOptions = {
                chart: {
                    type: 'area',
                    height: 315
                },
                colors: ['#003f5c', '#ffa600', '#bc5090'],
                series: [],
                xaxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
                },
                title: {
                    show: false,
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'straight',
                    width: 2,
                    colors: ['#003f5c', '#ffa600', '#bc5090'],
                },
                fill: {
                    type: 'solid',
                    opacity: 0,
                }
            };
            chart2 = new ApexCharts(document.querySelector("#chart2"), areaOptions);
            chart2.render();
        }

        // Update dashboard data
        function updateDashboard(store) {
            if (!store) return;

            $.ajax({
                url: '<?php echo e(route("dashboard.abc.data")); ?>',
                method: 'POST',
                data: {
                    store: store,
                    _token: '<?php echo e(csrf_token()); ?>'
                },
                success: function(response) {
                    // Update ABC category cards
                    const categories = response.abc_data.categories;
                    
                    // Category A
                    $('#category-a-items').text(categories.A.items);
                    $('#category-a-value').text('₹ ' + new Intl.NumberFormat('en-IN').format(categories.A.value));
                    $('#category-a-percentage').text(categories.A.percentage + '% Value');
                    
                    // Category B
                    $('#category-b-items').text(categories.B.items);
                    $('#category-b-value').text('₹ ' + new Intl.NumberFormat('en-IN').format(categories.B.value));
                    $('#category-b-percentage').text(categories.B.percentage + '% Value');
                    
                    // Category C
                    $('#category-c-items').text(categories.C.items);
                    $('#category-c-value').text('₹ ' + new Intl.NumberFormat('en-IN').format(categories.C.value));
                    $('#category-c-percentage').text(categories.C.percentage + '% Value');
                    
                    // Update overall inventory
                    $('#overall-inventory').text('₹ ' + new Intl.NumberFormat('en-IN').format(response.overall_inventory));
                    
                    // Update charts
                    updateCharts(response.chart_data);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching dashboard data:', error);
                    alert('Error loading dashboard data. Please try again.');
                }
            });
        }

        // Update charts with new data
        function updateCharts(chartData) {
            // Update donut chart
            chart1.updateOptions({
                series: chartData.donut.series,
                labels: chartData.donut.labels
            });

            // Update area chart
            chart2.updateOptions({
                series: chartData.area.series,
                xaxis: {
                    categories: chartData.area.categories
                }
            });
        }

        // Document ready
        $(document).ready(function() {
            // Initialize charts
            initializeCharts();
            
            // Store selection change handler
            $('#stores').on('change', function() {
                const selectedStore = $(this).val();
                updateDashboard(selectedStore);
            });
        });
    </script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/sukoyo/resources/views/dashboard/abc.blade.php ENDPATH**/ ?>