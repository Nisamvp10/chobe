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
                        <i class="bi bi-plus-circle me-1"></i> Add New Task
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
             <div class="overflow-x-auto" id="tasktbl" >
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
                    <div class="flex flex-col h-full bg-gray-50 rounded-lg p-4 min-w-[300px]">
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


<!-- Delete Confirmation Modal -->
<div class="modal fade" id="activities" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered hadow-xl w-full max-w-2xl  max-w-3xl flex flex-col">
    <div class="modal-content ">
      <div class="modal-header">
        <h5 class="modal-title">Create New Activity</h5>
        <p></p>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="bg-white rounded-lg  overflow-hidden p-2">
            <form id="taskCreate" method="post">
                 <?= csrf_field() ?>
                <div class="grid grid-cols-1 gap-4 pb-4">

                    <div class="w-full">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Task Title</label>
                        <div class="relative">
                        <input type="hidden" name="id" value="">
                        <div class="absolute inset-y-0 left-0 pl-3 mt-2 items-center pointer-events-none">
                            <i class="bi bi-list-check text-xl text-gray-400"></i>
                        </div>
                        <input type="text" name="title" id="title" value="" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter task Title">
                        <div class="invalid-feedback" id="title_error"></div>
                        </div>
                    </div>

                    <div class="w-full">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 mt-2 items-center pointer-events-none">
                            <i class="bi bi-pencil text-xl text-gray-400"></i>
                        </div>
                        <textarea name="description" id="description" class="pl-10 mt-2 flex min-h-[80px] w-full rounded-md border border-input bg-background  py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"></textarea>
                        <div class="invalid-feedback" id="description_error"></div>
                        </div>
                    </div>

                    
                    <div class="w-full">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Select Status</label>
                        <div class="responseive">
                        <select id="status" name="status" class="pl-3 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">"&gt;
                            <option value="">Select a Status</option>
                            <option value="1">Pending</option>
                            <option value="2">In Progress</option>
                            <option value="3">Completed</option>
                        </select>
                        <div class="invalid-feedback" id="status_error"></div>
                    </div>
                    </div>

                </div>


             
                
                    <div class="grid grid-cols-1 gap-4 pb-4 mt-4">
                        <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1 capitalize">Assign Staff &amp; Roles</label>
                            <p class="block text-sm font-small text-gray-400 mb-1 capitalize">Assign staff and select their roles for this task.</p>
                            <div id="participants" class="grid grid-cols-2 gap-4 pb-4">
                            <?php 
                            if(!empty($staff)){
                                foreach($staff as $taffkey) {
                                    ?>
                                    <div class="staff-wrapper border rounded-md p-3 flex items-center justify-between">
                                        <div class="flex items-center space-x-2">
                                            <input type="checkbox" name="staff[]" class="staff-checkbox" data-id="1" value="<?=$taffkey['name'];?>" id="staff-1">
                                            <label for="staff-1"><?=$taffkey['name'];?></label>
                                        </div>
                                    </div>
                                    <?php 
                                }
                            } ?>
                            </div>
                        </div>
                    </div>
                    </div>
                
            
                    <div class="mt-8 flex justify-end gap-3">
                            <a  data-bs-dismiss="modal" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">Cancel</a>
                            <button id="submitBtn" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md flex items-center transition-colors"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-save mr-1"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>Save</button>
                    </div>
            </form>
        </div>
      </div>
      
    </div>
  </div>
</div>

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



<?= $this->endSection() ;?>