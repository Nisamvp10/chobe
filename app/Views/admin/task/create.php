<?php $this->extend('layout/main') ;?>
<?= $this->section('content') ?>
<?php
    if(!empty($data))
    {
        $id = encryptor($data['id']);
        $branch = $data['branch_name'];
        $location = $data['location'];
    }else{
        $id=$branch=$location = '';
    }
?>
 <!-- titilebar -->
 <div class="flex items-center justify-between ">
    <div class="flex items-center gap-4">
        <a href="<?=base_url('branches');?>" class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left text-gray-500">
            <path d="m12 19-7-7 7-7"></path>
            <path d="M19 12H5"></path>
            </svg>
        </a> 
        <h1 class="h3 mb-0"><?= $page ?? '' ?></h1>
    </div>
</div><!-- closee titilebar -->

<!-- body -->
<div class="bg-white rounded-lg shadow-sm overflow-hidden p-4">
    <form id="taskCreate" method="post">
        <?= csrf_field() ?>
       <div class="grid grid-cols-1 gap-4 pb-4">

            <div class="w-full">
                <label class="block text-sm font-medium text-gray-700 mb-1">Task Title</label>
                <div class="relative">
                <input type="hidden" name="id" value="<?= $id ?>" />
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
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Branch</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 mt-2 items-center pointer-events-none"><i class="bi bi-diagram-3 text-xl text-gray-400"></i></div>
                        <select name="branch" id="branch" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required="">
                            <option value="">Select Branch</option>
                             <option value="all"  >All</option>
                            <?php
                                if(!empty($branches)){
                                    foreach($branches as $branch){
                                    ?>
                                        <option  value="<?=$branch['id'];?>"><?=$branch['branch_name'];?></option>
                                    <?php 
                                    } 
                                } ?>
                        </select>                       
                        <div class="invalid-feedback" id="branch_error"></div>
                    </div>
                </div>
                <div >
                    <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 mt-2 items-center pointer-events-none"><i class="bi bi-calendar text-xl text-gray-400"></i></div>
                        <input type="date" name="duedate" value=""  id="duedate" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter Your Name">
                        <div class="invalid-feedback" id="duedate_error"></div>
                    </div>
                </div>
            </div>

            <!-- 2 -->
             <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 capitalize">Priority</label>
                    <div class="responseive">
                        <select id="priority" name="priority" class="pl-3 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">">
                            <option value="">Select a Priority</option>
                            <option value="1">High</option>
                            <option value="2">Medium</option>
                            <option value="3">Low</option>
                        </select>
                        <div class="invalid-feedback" id="priority_error"></div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">File - Attachment</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 mt-2 items-center pointer-events-none"><i class="bi bi-paperclip text-xl text-gray-400"></i></div>
                        <input type="file" name="file" id="file" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter Password">
                        <div class="invalid-feedback" id="file_error"></div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Projects</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 mt-2 items-center pointer-events-none"><i class="bi bi-diagram-3 text-xl text-gray-400"></i></div>
                        <select name="project" id="project" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required="">
                            <option value="">Select Project</option>
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
                </div>
             </div>
             </div>
              <div class="grid grid-cols-1 gap-4 pb-4 mt-4">
                <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 capitalize">Assign Staff & Roles</label>
                    <p class="block text-sm font-small text-gray-400 mb-1 capitalize">Assign staff and select their roles for this task.</p>
                    <div id="participants" class="grid grid-cols-2 gap-4 pb-4"></div>
                </div>
              </div>
            </div>
        
       
        <div class="mt-8 flex justify-end gap-3">
                    <?= ($id ? '<button type="button" onClick="deleteBranch(this)" data-id="'.$id.'" class="px-4 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition-colors"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash2 inline-block mr-1"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path><line x1="10" x2="10" y1="11" y2="17"></line><line x1="14" x2="14" y1="11" y2="17"></line></svg>Delete</button>' :'')?>
                    <a href="<?=base_url('branches');?>" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">Cancel</a>
                    <button id="submitBtn" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md flex items-center transition-colors"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-save mr-1"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>Save</button>
            </div>
    </form>
</div><!-- close body -->
<?= $this->endSection();?>
<?= $this->section('scripts') ?>
<script src="<?=base_url('public/assets/js/task.js') ;?>" ></script>
<?= $this->endSection() ;?>