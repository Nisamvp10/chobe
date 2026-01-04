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
                    <a href="<?= base_url('task/create') ?>" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Add New Task
                    </a>
                </div>
                <?php } ?>
                 <?php
                if(haspermission('','daily_assign')) { ?>
                <div>
                    <a  class="btn !bg-green-500 text-white " onclick="openAssignModal()">
                        <i class="bi bi-plus-circle me-1"></i>Daily Assign
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
            <div class="w-full md:w-48">
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
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-filter text-gray-400">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    </div>
                    <select id="taskFilterStatus" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none">
                         <option value="all">All</option>
                        <option <?=($taskStatus == 'pending' ? 'selected' :'') ;?> value="Pending">Pending</option>
                        <option <?=($taskStatus == 'in-progress' ? 'selected' :'') ;?> value="In_Progress">In Progress</option>
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
 
<div id="taskModal" class="fixed inset-0 bg-black/50 flex items-center  justify-center z-100 hidden" onclick="closeTaskModal(event)">
  <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl h-[90vh] overflow-y-auto">
    <!-- head -->

     <div class="p-4 border-b flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800 modal-title"></h2>
        <div class="flex space-x-2">
            <button  class="modal-action-btn p-1.5 rounded-md text-gray-500 hover:bg-gray-100" title="View History">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock "><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </button>
            <?php if(hasPermission('','task_edit')) {?>
                <button onclick="showStep(4)"  class="modal-action-btn p-1.5 rounded-md text-gray-500 hover:bg-gray-100"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen "><path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.375 2.625a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4Z"></path></svg></button>
            <?php } ?>
            <button  onclick="showStep(2)" class="modal-action-btn p-1.5 rounded-md text-gray-500 hover:bg-red-100 hover:text-red-600 delete-task"  ><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-square">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg></button>
            <?php
            if(hasPermission('','task_delete')) {?>
                <button  class="p-1.5 rounded-md text-gray-500 hover:bg-red-100 hover:text-red-600 delete-task"  data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="deleteTask(this)"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash "><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path></svg></button>
            <?php } ?>
            
            <button onclick="closeTaskModal()" class="p-1.5 rounded-md text-gray-500 hover:bg-gray-100"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x "><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg></button></div></div>
     <!-- close Head -->
   <div class="step1 step-active" id="step1">
    <div class="flex-1 overflow-y-auto p-4 space-y-4" id="taskDetails">
        <div class="flex items-center gap-2">
            <span class="px-2 py-1 rounded-full text-xs text-white modal-priority font-medium"></span>
            <span class="text-sm text-gray-500">Created on <span class="modal-date"></span> </span>
        </div>
      <p class="text-gray-700 modal-desc">Description</p>
      <p class="text-gray-700 ">Branch : <span class="modal-branch"></span></p>
      <p class="text-gray-700">Status: <span class="modal-status">Pending</span></p>
      <div class="w-full bg-gray-200 rounded-full h-2">
        <div id="progressIndicator" class="modal-progress-bar h-2 rounded-full transition-all duration-500 " style="width: 0%;"></div>
      </div>
      <div id="documents"></div>
      <p class="text-sm text-gray-500">Duration <span class="modal-duration"></span></p>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Assigned Staff:</h3>
            <div class="space-y-2">
                <div class="modal-profiles  grid grid-cols-2 gap-2"></div>
            </div>
        </div>
    </div>
    </div>
    
    <div class="step2">
    <!-- history -->
     <div class="w-full p-6 overflow-y-auto border-l  h-full flex flex-col" >
        <div class="space-y-4">
            <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Messages</h3>
        
            </div>
            <div class="">
            <div id="taskReplys"></div>
                 <div  class="w-full p-6 overflow-y-auto border-l flex flex-col">
            <form class="mb-4" id="replyForm">
                <div class="flex space-x-2">
                    <textarea placeholder="Enter your reply..." name="replay" class="flex-1 min-h-[100px] p-3 border rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>
                <div class="flex justify-end space-x-2 mt-2 gap-3">
                    <button type="button" onclick="showStep(2)" class="px-3 py-1 text-gray-600 hover:bg-gray-100 rounded-2 border" onclick="hideReplyForm()">Cancel</button>
                    <button type="submit" id="formmBtn" class="flex items-center space-x-1 px-3 py-1 bg-green-600 text-white rounded-2 hover:bg-indigo-700">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-send">
                            <path d="M22 2 11 13"></path><path d="M22 2 15 22 11 13 2 9 22 2z"></path>
                        </svg>
                        <span>Send</span>
                    </button>
                </div>
            </form>
        </div>  <!-- close  replay form -->
            </div>
        </div>
    </div>
</div>

     <!-- history -->
      <!-- Reply Form -->
      
    <div class="step4">
    <!-- from  -->
     <!-- Task Edit Panel -->
    <div id="editForm" class="w-full p-6 overflow-y-auto border-l  h-full flex flex-col h-[90vh]" >
      <h2 class="text-xl font-semibold mb-4">Edit Task</h2>
      <form id="taskEditForm" class="space-y-4">
        <?= csrf_field() ;?>
         <input type="hidden" name="taskId" id="taskId" >
        <div >
          <label class="block mb-1 font-medium">Title</label>
          <input type="text" name="title" class="w-full border px-3 py-2 rounded" id="title" />
           <div class="invalid-feedback" id="title_error"></div>
        </div>

        <div>
          <label class="block mb-1 font-medium">Description</label>
          <textarea class="w-full border px-3 py-2 rounded" name="description" id="description"></textarea>
           <div class="invalid-feedback" id="description_error"></div>
        </div>

        <div >
          <label class="block mb-1 font-medium">Project</label>
          <select id="project" name="project" class="w-full border px-3 py-2 rounded">
            <option value="">Select Project</option>
            <option value="all"  >All</option>
            <?php
                if(!empty($projects)){
                    foreach($projects as $project){
                    ?>
                        <option  value="<?=$project['id'];?>"><?=$project['project'];?></option>
                    <?php 
                    } 
                } ?>
          </select>
           <div class="invalid-feedback" id="project_error"></div>
        </div>

        <div  >
          <input type="hidden" id="projectUnit" name="projectUnit" class="w-full border px-3 py-2 rounded">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
            <div class="flex space-x-2" id="priorityButtons">
                 <input type="hidden" name="priority" id="priorityInput" value="">
                <button name="priority"  type="button" class="priority-btn px-4 py-2 rounded-md text-sm bg-gray-100 text-gray-800 border border-gray-300" data-priority="Low">Low</button>
                <button name="priority"  type="button" class="priority-btn px-4 py-2 rounded-md text-sm bg-orange-100 text-orange-800 border border-orange-300" data-priority="Medium">Medium</button>
                <button name="priority"  type="button" class="priority-btn px-4 py-2 rounded-md text-sm bg-gray-100 text-gray-800 border border-gray-300" data-priority="High">High</button>
            </div>
        </div>
        <div class="hidden">
            <label for="progress" class="block text-sm font-medium text-gray-700 mb-1" id="progressLabel">Progress: 60%</label>
            <input type="range" name="progress" id="progressBar" min="0" max="100" step="5" class="w-full" value="">
        </div>
        <div class="hidden">
            <div class="grid grid-cols-2 gap-2">
                <div class="items-center rounded-md cursor-pointer border-gray-300">
                    <label class="block font-medium">Due Date</label>
                    <input type="date" name="duedate" value="" id="duedate" class="w-full border px-3 py-2 rounded" />
                </div>
       
                <div class="items-center rounded-md cursor-pointer border-gray-300 hidden">
                      <input type="hidden" id="taskStatus" name="status" class="w-full border px-3 py-2 rounded">
                 </div>
            </div>
            
        </div>
        <div>
          <label class="block mb-2 font-medium">Assign Staff</label>
          <div class="mt-1 grid grid-cols-2 gap-2" id="participants">
          
          </div>
        </div>

           <div class="mt-8 flex justify-end gap-3">
                  
                    <a onclick="showStep(1)" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">Cancel</a>
                    <button id="submitBtn" class="bg-blue-600 hover:bg-primary-700 text-white rounded-2 px-4 py-2 rounded-md flex items-center transition-colors"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-save mr-1"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>Save</button>
            </div>
      </form>
    </div>
    </div>
     <!-- close edit form -->
  </div>
</div>



<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Delete Task</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p id="deleteTaskMessage">Are you sure you want to delete this Task? This action cannot be undone.</p>
        <p id="deleteTaskMessage">This is Demo version </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="dutton" class="btn btn-danger" id="confirmDeleteTask">Delete</button>
      </div>
    </div>
  </div>
</div>


<!-- open -->
 <!-- close screen -->
<!-- close Modal -->
 <?= view('modal/autoAssignModal') ?>
<?= $this->endSection(); ?>
<?= $this->section('scripts') ?>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Moment.js (must be before daterangepicker.js) -->
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

<!-- Date Range Picker -->
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />


    <script src="<?=base_url('public/assets/js/tasklist.js') ?>" ></script>
    <script src="<?=base_url('public/assets/js/task.js') ;?>" ></script>
    <script src="<?=base_url('public/assets/js/autoassign.js') ;?>" ></script>


<?= $this->endSection() ;?>