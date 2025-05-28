<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-lg-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0"><?= $page ?? '' ?></h1>
            <div>
                <a href="<?= base_url('dashboard/tasks/create') ?>" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> New Task
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-4">
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100 transition-all hover:shadow-md">
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
    </div>
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100 transition-all hover:shadow-md">
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
    </div>
    
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100 transition-all hover:shadow-md">
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
    <div class="col-lg-7">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Recent Tasks</h5>
                <a href="<?= base_url('tasks') ?>" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Branch</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Deadline</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentTasks)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4">No tasks found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentTasks as $task): ?>
                                    <tr>
                                        <td>
                                            <a href="<?= base_url('dashboard/tasks/view/' . $task['id']) ?>" class="text-decoration-none text-dark">
                                                <?= $task['title'] ?>
                                            </a>
                                        </td>
                                        <td><?= $task['branch_name'] ?></td>
                                        <td>
                                            <span class="badge rounded-pill 
                                                <?= $task['priority'] == 'High' ? 'bg-danger' : 
                                                   ($task['priority'] == 'Medium' ? 'bg-warning' : ($task['priority'] == 'Low' ? 'bg-primary': 'bg-success') )?>">
                                                <?= ucfirst($task['priority']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge rounded-pill 
                                                <?= $task['status'] == 'Pending' ? 'bg-warning' : 
                                                   ($task['status'] == 'In_Progress' ? 'bg-primary' : 'bg-success') ?>">
                                                <?= ucfirst(str_replace('_', ' ', $task['status'])) ?>
                                            </span>
                                        </td>
                                        <td><?= date('M d, Y', strtotime($task['overdue_date'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- My Tasks & Notifications -->
    <div class="col-lg-5">
        <!-- My Tasks -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">My Tasks</h5>
                <a href="<?= base_url('dashboard/tasks/my-tasks') ?>" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-3">
                <?php if (empty($myTasks)): ?>
                    <div class="text-center py-4">
                        <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                        <p class="mb-0 mt-2">No tasks assigned to you</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($myTasks as $task): ?>
                        <div class="task-item d-flex align-items-center p-2 border-bottom">
                            <div class="me-3">
                                <?php if ($task['status'] == 'completed'): ?>
                                    <i class="bi bi-check-circle-fill text-success" style="font-size: 1.5rem;"></i>
                                <?php elseif ($task['status'] == 'in_progress'): ?>
                                    <i class="bi bi-hourglass-split text-warning" style="font-size: 1.5rem;"></i>
                                <?php else: ?>
                                    <i class="bi bi-circle text-primary" style="font-size: 1.5rem;"></i>
                                <?php endif; ?>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    <a href="<?= base_url('dashboard/tasks/view/' . $task['id']) ?>" class="text-decoration-none text-dark">
                                        <?= $task['title'] ?>
                                    </a>
                                </h6>
                                <div class="small text-muted">
                                    <i class="bi bi-calendar-event me-1"></i>
                                    <?= date('M d, Y', strtotime($task['deadline'])) ?>
                                    <span class="mx-2">•</span>
                                    <span class="badge rounded-pill 
                                        <?= $task['priority'] == 'high' ? 'bg-danger' : 
                                           ($task['priority'] == 'medium' ? 'bg-warning' : 'bg-success') ?>">
                                        <?= ucfirst($task['priority']) ?>
                                    </span>
                                </div>
                            </div>
                            <div class="task-progress">
                                <div class="progress" style="width: 60px; height: 6px;">
                                    <div class="progress-bar 
                                        <?= $task['priority'] == 'high' ? 'bg-danger' : 
                                           ($task['priority'] == 'medium' ? 'bg-warning' : 'bg-success') ?>" 
                                         role="progressbar" 
                                         style="width: <?= $task['progress'] ?>%;" 
                                         aria-valuenow="<?= $task['progress'] ?>" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100"></div>
                                </div>
                                <div class="small text-end mt-1"><?= $task['progress'] ?>%</div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Notifications -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Recent Notifications</h5>
                <a href="<?= base_url('dashboard/notifications') ?>" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($unreadNotifications)): ?>
                    <div class="text-center py-4">
                        <i class="bi bi-bell-slash text-muted" style="font-size: 2rem;"></i>
                        <p class="mb-0 mt-2">No new notifications</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($unreadNotifications as $notification): ?>
                            <a href="<?= base_url('dashboard/notifications/mark-as-read/' . $notification['id']) ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">Task Notification</h6>
                                    <small><?= timeAgo($notification['created_at']) ?></small>
                                </div>
                                <p class="mb-1"><?= $notification['message'] ?></p>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
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
                toolbar: {
                    show: false
                },
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
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                },
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
                tooltip: {
                    enabled: true,
                }
            },
            yaxis: {
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false,
                },
                labels: {
                    show: true,
                    formatter: function (val) {
                        return val;
                    }
                }
            },
            colors: ['#0F52BA', '#FFC107', '#4CAF50'],
            grid: {
                borderColor: '#e0e0e0',
                strokeDashArray: 5,
                xaxis: {
                    lines: {
                        show: true
                    }
                },
                yaxis: {
                    lines: {
                        show: true
                    }
                },
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