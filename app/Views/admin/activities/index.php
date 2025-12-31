<?= $this->extend('layout/main') ?>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<?= $this->section('content') ?>

    <!-- titilebar -->
    <div class="flex items-center justify-between">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center mb-0">
                <h1 class="h3 mb-0"><?= $page ?? '' ?></h1>
                <?php
                if(haspermission(session('user_data')['role'],'create_task')) { ?>
                <div>
                    <a href="<?= base_url('task/create') ?>" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#activities">
                        <i class="bi bi-plus-circle me-1"></i> Add New Activity
                    </a>
                </div>
                <?php } ?>
            </div>
        </div>
    </div><!-- closee titilebar -->

    <!-- body -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden p-4">
        <div class="flex flex-col md:flex-row gap-4 mb-6">
    
            <!-- Column 1: Search Input -->
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search text-gray-400">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.3-4.3"></path>
                </svg>
                </div>
                <input type="text" id="searchInput" placeholder="Search Task title..." class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
            </div>
             <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
               <i class="bi bi-calendar"></i>
                </div>
                <input type="text" id="filterDate" placeholder="Filter by date" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
            </div>
                

            <!-- Column 2: Status Dropdown -->
            <div class="w-full md:w-48">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-filter text-gray-400">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    </div>
                    <select id="taskFilerStatus" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none">
                         <option value="all">All</option>
                        <option value="Pending">Pending</option>
                        <option  value="In_Progress">In Progress</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>
            </div>
            </div>
            <!-- table -->
             <div class="overflow-x-auto" id="tasktbl" data-task="<?=$activityId ?? '' ;?>" >
                <div class="flex h-full gap-6 p-0">
                    <div class="flex flex-col h-full bg-gray-50 rounded-lg p-4 min-w-[300px]">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="font-semibold text-gray-700">Pending</h2>
                            <span class="bg-gray-200 text-gray-600 rounded-full px-2 py-0.5 text-xs">1</span>
                        </div>
                        <div class="flex-1 overflow-y-auto space-y-3">
                             <div id="taskPending" class="flex-1 task-list overflow-y-auto space-y-4 min-h-[80px]"></div>
                        </div>
                    </div>
                    <div class="flex flex-col hidden h-full bg-gray-50 rounded-lg p-4 min-w-[300px] d-none">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="font-semibold text-gray-700">In Progress</h2>
                            <span class="bg-gray-200 text-gray-600 rounded-full px-2 py-0.5 text-xs">1</span>
                        </div>
                        <div class="flex-1 overflow-y-auto space-y-3">
                             <div id="inProgress" class="flex-1 task-list overflow-y-auto space-y-4 min-h-[80px]"></div>
                        </div>
                    </div>

                    <div class="flex flex-col h-full bg-gray-50 rounded-lg p-4 min-w-[300px]">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="font-semibold text-gray-700">Completed</h2>
                            <span class="bg-gray-200 text-gray-600 rounded-full px-2 py-0.5 text-xs">1</span>
                        </div>
                        <div class="flex-1 overflow-y-auto space-y-3">
                             <div id="completed" class="flex-1 task-list overflow-y-auto space-y-4 min-h-[80px]"></div>
                        </div>
                    </div>
                   
                </div>
                <div id="taskTable"></div>
            </div>
            <!-- close table -->
</div><!-- body -->

<!-- modal -->

<!-- Modal -->
<?= view('modal/createactivities');?>
<?= view('modal/activityActionModal');?>


<!-- close Modal -->
<?= $this->endSection(); ?>
<?= $this->section('scripts') ?>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Moment.js (must be before daterangepicker.js) -->
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

<!-- Date Range Picker -->
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script src="<?=base_url('public/assets/js/activities.js') ;?>" ></script>
<script src="<?=base_url('public/assets/js/taskactivity.js') ;?>" ></script>
<script src="<?=base_url('public/assets/js/activitycomments.js') ;?>" ></script>





<?= $this->endSection() ;?>