
<div id="activities" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 wrapModal">
  <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl h-auto overflow-y-auto">
    <div class="modal-content ">
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
      <h2 class="text-2xl font-bold text-gray-900 head">Create New Activity</h2>
      <button data-close="activities" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
        ✕
      </button>
    </div>
      <div class="p-6">
        <div class="bg-white rounded-lg  overflow-hidden p-2">
            <form id="taskCreate" method="post">
                 <?= csrf_field() ?>
                <?php if(isset($activityId) && !empty($activityId)) { ?>
                                     <input type="hidden" name="taskId" id="taskId" value="<?=encryptor($activityId);?>" >
                                     <?php } ?>
                <div class="grid grid-cols-1 gap-4 pb-4">

                    <div class="w-full">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Activity Title</label>
                        <div class="relative">
                        <input type="hidden" name="id" value="" id="activityId">
                        <input type="hidden" name="type" value="1">
                        <input type="hidden" name="status" value="1">
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

                    <?php if(!empty($masterTasks)) { ?>
                     <div class="w-full ">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Select Task</label>
                        <div class="responseive">
                        <select id="taskId" name="taskId" class="pl-3 pr-3 py-2 w-full activity-tasks border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select a Task</option>
                            <?php 
                            foreach($masterTasks as $masterTask) {
                                ?>
                                <option value="<?=encryptor($masterTask['id']);?>"><?=$masterTask['title'];?></option>
                                <?php 
                            }?>
                        </select>
                        <div class="invalid-feedback" id="taskId_error"></div>
                    </div>
                    </div>
                    <?php } ?>

                </div>

                    <div class="grid grid-cols-1 gap-4 pb-4 mt-4 hidden">
                        <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1 capitalize">Assign Staff &amp; Roles</label>
                            <p class="block text-sm font-small text-gray-400 mb-1 capitalize">Assign staff and select their roles for this task.</p>
                            <div id="participantsactivityTasks" class="grid grid-cols-2 gap-4 pb-4">
                           
                            </div>
                        </div>
                    </div>
                    </div>
            
                    <div class="mt-8 flex justify-end gap-3">
                            <a data-close="activities" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-2 hover:bg-gray-50 transition-colors">Cancel</a>
                            <button id="submitBtn" class="bg-blue-500 hover:bg-primary-700 text-white px-4 py-2 rounded-2 flex items-center transition-colors"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-save mr-1"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>Save</button>
                    </div>
            </form>
        </div>
      </div>
      
    </div>
  </div>
</div>
