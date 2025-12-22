
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
                <?php if(isset($activityId) && !empty($activityId)) { ?>
                                     <input type="hidden" name="taskId" id="taskId" value="<?=encryptor($activityId);?>" >
                                     <?php } ?>
                <div class="grid grid-cols-1 gap-4 pb-4">

                    <div class="w-full">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Activity Title</label>
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
