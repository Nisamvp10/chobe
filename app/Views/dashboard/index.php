<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-lg-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0"><?= $page ?? '' ?></h1>
            <div>
                <a href="<?= base_url('task/create') ?>" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> New Task 
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-4">
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100 transition-all hover:shadow-md">
        <a href="<?=base_url('tasks');?>" class="cursor-pointer">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Task</p>
                <h3 class="text-2xl font-semibold mt-1"><?= $totalTasks ?></h3>
                <div class="flex items-center mt-2">
                    <span class="mr-1 text-green-500">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trending-up "><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline><polyline points="16 7 22 7 22 13"></polyline></svg>
                    </span>
                    <span class="text-sm text-green-500">+ <?=$growth;?> %</span>
                    <span class="text-xs text-gray-400 ml-1">from last period</span>
                </div>
            </div>
            <div class="p-3 rounded-full bg-blue-50">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar text-blue-500"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M3 10h18"></path></svg>
            </div>
        </div>
        </a>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100 transition-all hover:shadow-md">
        <a href="<?=base_url('admin/task/pending');?>" class="cursor-pointer">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-gray-500 text-sm">Pending Task</p>
                <h3 class="text-2xl font-semibold mt-1"><?= $pendingTasks ?></h3>
                <div class="flex items-center mt-2">
                    <span class="mr-1 text-green-500">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trending-up "><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline><polyline points="16 7 22 7 22 13"></polyline></svg>
                    </span>
                    <!-- <span class="text-sm text-green-500">+12.5%</span>
                    <span class="text-xs text-gray-400 ml-1">from last period</span> -->
                </div>
            </div>
            <div class="p-3 rounded-full bg-blue-50">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users text-green-500"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </div>
        </div>
        </a>
    </div>
    
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100 transition-all hover:shadow-md">
        <a href="<?=base_url('admin/task/in-progress');?>" class="cursor-pointer">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-gray-500 text-sm">In Progress Task</p>
                <h3 class="text-2xl font-semibold mt-1"><?= $inProgressTasks ?></h3>
                <div class="flex items-center mt-2">
                    <span class="mr-1 text-green-500">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-dollar-sign text-purple-500"><line x1="12" x2="12" y1="2" y2="22"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                    </span>
                    <span class="text-sm text-green-500">+12.5%</span>
                    <span class="text-xs text-gray-400 ml-1">from last period</span>
                </div>
            </div>
            <div class="p-3 rounded-full bg-blue-50">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-dollar-sign text-purple-500"><line x1="12" x2="12" y1="2" y2="22"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
            </div>
        </div>
        </a>
    </div>
    
   
</div>

<!-- Task Distribution & Recent Tasks -->
<div class="row">
    <!-- Priority Distribution Chart -->
    <div class="col-lg-5">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Tasks by Priority</h5>
            </div>
            <div class="card-body">
                <div id="priority-chart" style="height: 300px;"></div>
            </div>
        </div>
    </div>
    
    <!-- Status Distribution Chart -->
    <div class="col-lg-7">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Task Status Distribution</h5>
            </div>
            <div class="card-body">
                <div id="status-chart" style="height: 300px;"></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
   

    <!-- Recent Tasks -->
    <div class="col-lg-12">
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-2">Recent Tasks</h5>
                 <div class="flex flex-col md:flex-row gap-4 mb-6">
    
            <!-- Column 1: Search Input -->
            <div class="flex-1 relative">
                <label for="searchInput" class="block text-sm font-medium text-gray-700 mb-2">Search Task title...</label>
                <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search text-gray-400">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.3-4.3"></path>
                </svg>
                </div>
                <input type="text" id="searchInput" placeholder="Search Task title..." class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
            </div>
            </div>
             <div class="flex-1 ">
                <label for="filterDate" class="block text-sm font-medium text-gray-700 mb-2">Filter by date</label>
                <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
               <i class="bi bi-calendar"></i>
                </div>
                <input type="text" id="filterDate" placeholder="Filter by date"  value="<?=date('Y-m-d' ,strtotime('-1 days'))?> to <?=date('Y-m-d' ,strtotime('-1 days'))?>" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                </div>
            </div>
            <div class="w-full md:w-48">
                <label for="taskProject" class="block text-sm font-medium text-gray-700 mb-2">Filter by project</label>
                <select class="w-full border px-3 py-2 rounded" id="taskProject">
                      <option value="all">All</option>
                <?php 
                    if(!empty($projects)) {
                        foreach($projects as $project) {
                        ?>
                        <option value="<?=$project['id'];?>"><?=$project['project'];?></option>
                        <?php
                        }
                }?>
                </select>
            </div>

            <!-- Column 2: Status Dropdown -->
            <div class="w-full md:w-48">
                <div class="relative">
                    <label for="taskFilterStatus" class="block text-sm font-medium text-gray-700 mb-2">Filter by status</label>
                    <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-filter text-gray-400">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    </div>
                    <select id="taskFilterStatus" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none">
                         <option value="all">All</option>
                        <option  value="Pending">Pending</option>
                        <option value="Completed">Completed</option>
                    </select>
                    </div>
                </div>
            </div>
            </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" id="taskTable">
                    
                </div>
            </div>
        </div>
    </div>


</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Moment.js (must be before daterangepicker.js) -->
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

<!-- Date Range Picker -->
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script>
    // Function to create priority distribution chart
    function createPriorityChart() {
        const options = {
            series: [<?= $highPriorityTasks ?>, <?= $mediumPriorityTasks ?>, <?= $lowPriorityTasks ?>],
            chart: {
                type: 'donut',
                height: 300,
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800,
                    animateGradually: {
                        enabled: true,
                        delay: 150
                    },
                    dynamicAnimation: {
                        enabled: true,
                        speed: 350
                    }
                }
            },
            labels: ['High', 'Medium', 'Low'],
            colors: ['#F44336', '#FFC107', '#4CAF50'],
            legend: {
                position: 'bottom'
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        height: 250
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }],
            plotOptions: {
                pie: {
                    donut: {
                        size: '55%'
                    }
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function(val, opts) {
                    return opts.w.config.series[opts.seriesIndex];
                },
                style: {
                    fontSize: '14px',
                    fontFamily: '-apple-system, BlinkMacSystemFont, "SF Pro Display", "Segoe UI", Roboto',
                    fontWeight: 'bold'
                },
                dropShadow: {
                    enabled: false
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + ' tasks';
                    }
                }
            }
        };

        const chart = new ApexCharts(document.querySelector("#priority-chart"), options);
        chart.render();
    }
    
    // Function to create status distribution chart
    function createStatusChart() {
    const options = {
        series: [{
            name: 'Tasks',
            data: [<?= $pendingTasks ?>, <?= $inProgressTasks ?>, <?= $completedTasks ?>]
        }],
        chart: {
            type: 'bar',
            height: 300,
            toolbar: { show: false },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800,
                animateGradually: {
                    enabled: true,
                    delay: 150
                },
                dynamicAnimation: {
                    enabled: true,
                    speed: 350
                }
            }
        },
        plotOptions: {
            bar: {
                borderRadius: 5,
                dataLabels: {
                    position: 'top',
                },
                distributed: true // enables separate colors for each bar
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return val;
            },
            offsetY: -20,
            style: {
                fontSize: '14px',
                fontFamily: '-apple-system, BlinkMacSystemFont, "SF Pro Display", "Segoe UI", Roboto',
                fontWeight: 'bold',
                colors: ["#304758"]
            }
        },
        xaxis: {
            categories: ['Pending', 'In Progress', 'Completed'],
            position: 'bottom',
            axisBorder: { show: false },
            axisTicks: { show: false },
            crosshairs: {
                fill: {
                    type: 'gradient',
                    gradient: {
                        colorFrom: '#D8E3F0',
                        colorTo: '#BED1E6',
                        stops: [0, 100],
                        opacityFrom: 0.4,
                        opacityTo: 0.5,
                    }
                }
            },
            tooltip: { enabled: true }
        },
        yaxis: {
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: {
                show: true,
                formatter: function (val) {
                    return val;
                }
            }
        },
        colors: ['#eab308', '#0F52BA', '#4CAF50'], // warning (pending), blue (in progress), green (completed)
        grid: {
            borderColor: '#e0e0e0',
            strokeDashArray: 5,
            xaxis: {
                lines: { show: true }
            },
            yaxis: {
                lines: { show: true }
            }
        }
    };

    const chart = new ApexCharts(document.querySelector("#status-chart"), options);
    chart.render();
}

    
    // Initialize charts when document is ready
    document.addEventListener('DOMContentLoaded', function() {
        createPriorityChart();
        createStatusChart();
    });

    

tasks();
let alltaskData = [];
function tasks(startDate='',endDate=''){
    $('#taskTable').html('<p class="text-center p-2 text-muted align-middle justify-content-center d-flex">Loading...</p>');
    let searchInput = $('#searchInput').val();
    let filter = $('#taskFilterStatus').val();
    let taskProject = $('#taskProject').val();

    let taskFilterStatus = $('#taskFilterStatus').val();
    $.ajax({
        url: "<?= base_url('dashboard/tasks') ?>",
        type: "GET",
        data: {
            search: searchInput,
            filter: filter,
            taskProject: taskProject,
            startDate: startDate, endDate: endDate,
            status: taskFilterStatus
        },
        success: function(response) {
           if(response.task.length > 0){
                alltaskData = response.task;
                taskdataRender();
           }else{
            console.log(response.task);
           $('#taskTable').html('<p class="text-center p-2 text-muted align-middle justify-content-center d-flex">No tasks found</p>');
           }
        }
    });
}

let rowsPerPage = 15;
let currentPage = 1;

function taskdataRender(){
    let data = alltaskData;
    let start = (currentPage - 1) * rowsPerPage;
    let end = start + rowsPerPage;
    let paginatedData = data.slice(start, end);
    //first loading loading...
    let html = '';
    if(paginatedData.length > 0){
        
        html = `
        
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Branch</th>
                    <th>Status</th>
                    <th>Task Gen Date</th>
                </tr>
            </thead>
            <tbody>
        `;
        paginatedData.forEach((item,index) => {
            html += `
           <tr>
            <td>${start + index + 1}</td>
                <td>
                    <a href="javascript:void(0)" class="text-decoration-none text-dark">
                        ${item.title}
                    </a>
                </td>
                <td>${item.branch_name}</td>
                <td>
                
                    <span class="badge rounded-pill 
                        ${item.completed_activities == item.total_activities ? 'bg-success' : 
                        (item.completed_activities == 0 ? 'bg-danger' : (item.completed_activities == item.total_activities ? 'bg-success': 'bg-primary') )}">
                            ${item.completed_activities} / ${item.total_activities}
                    </span>
                </td>
                <td class="hidden">
                    <span class="badge rounded-pill 
                        ${item.status == 'Pending' ? 'bg-warning' : 
                        (item.status == 'In_Progress' ? 'bg-primary' : 'bg-success') }">
                        ${item.status}
                    </span>
                </td>
                <td>${item.created}</td>
            </tr>
            `;
        });
        html += `</tbody>
        </table>`;
         let totalPages = Math.ceil(data.length / rowsPerPage);
        html += `
            <div class="flex justify-between items-center mt-4 p-3">
                <div>
                    <label class="mr-2">Rows per page:</label>
                    <select onchange="changeRowsPerPage(this.value)" class="px-2 py-1 border rounded">
                        <option value="15"  ${rowsPerPage == 15 ? 'selected' : ''}>15</option>
                        <option value="25"  ${rowsPerPage == 25 ? 'selected' : ''}>25</option>
                        <option value="50"  ${rowsPerPage == 50 ? 'selected' : ''}>50</option>
                        <option value="100" ${rowsPerPage == 100 ? 'selected' : ''}>100</option>
                    </select>
                </div>
                <div>
                    <button onclick="prevPage()" ${currentPage === 1 ? 'disabled' : ''} class="px-3 py-1 bg-gray-200 rounded disabled:opacity-50">Prev</button>
                    <span class="mx-2">Page ${currentPage} of ${totalPages}</span>
                    <button onclick="nextPage(${totalPages})" ${currentPage === totalPages ? 'disabled' : ''} class="px-3 py-1 bg-gray-200 rounded disabled:opacity-50">Next</button>
                </div>
            </div>`;
    }else{
        console.log(alltaskData);
        html = '<p>No tasks found</p>';
    }
    $('#taskTable').html(html);
}

// Change rows per page
function changeRowsPerPage(value) {
    rowsPerPage = parseInt(value);
    currentPage = 1;
    taskdataRender();
}

// Pagination functions
function prevPage() {
    if (currentPage > 1) {
        currentPage--;
        taskdataRender();
    }
}
function nextPage(totalPages) {
    if (currentPage < totalPages) {
        currentPage++;
        taskdataRender();
    }
}
$('#searchInput').on('keyup', function() {
    tasks();
});

$('#taskProject, #filterDate, #taskFilterStatus').on('change', function() {
    tasks();
});

    $('#filterDate').daterangepicker({
        opens: 'left',
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear',
            format: 'DD-MM-YYYY'
        },
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    });

    // Apply event
    $('#filterDate').on('apply.daterangepicker', function (ev, picker) {
        let startDate = picker.startDate.format('YYYY-MM-DD');
        let endDate = picker.endDate.format('YYYY-MM-DD');
        $(this).val(startDate + ' to ' + endDate);
        tasks(startDate,endDate)

    });

    // Cancel event
    $('#filterDate').on('cancel.daterangepicker', function (ev, picker) {
        $(this).val('');
        $('#results').empty();
    });

</script>
<?= $this->endSection() ?>

<?php
// Helper function to format time ago
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $now = time();
    $diff = $now - $timestamp;
    
    if ($diff < 60) {
        return 'Just now';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' min' . ($mins > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return date('M d, Y', $timestamp);
    }
}

?>

