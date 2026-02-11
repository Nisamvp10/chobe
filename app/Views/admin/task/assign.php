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
                <select class="w-full border !pl-10 px-4 py-2 rounded masetrTask" id="masetrTask" name="masetrTask">
                      <option value="">Master Tasks</option>
                <?php 
                    if(!empty($masterTasks)) {
                        foreach($masterTasks as $masterTask) {
                        ?>
                        <option value="<?=$masterTask['id'];?>"><?=$masterTask['title'];?></option>
                        <?php
                        }
                }?>
                </select>
                <div class="invalid-feedback" id="masetrTask_error"></div>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-3 gap-4 pb-4" id="activities"></div>
    
        
       
        <div class="mt-8 flex justify-end gap-3">
                    <!-- <?= ($id ? '<button type="button" onClick="deleteBranch(this)" data-id="'.$id.'" class="px-4 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition-colors"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash2 inline-block mr-1"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path><line x1="10" x2="10" y1="11" y2="17"></line><line x1="14" x2="14" y1="11" y2="17"></line></svg>Delete</button>' :'')?> -->
                    <a href="<?=base_url('branches');?>" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">Cancel</a>
                    <button id="submitBtn" class="bg-primary-600 hover:bg-primary-700 rounded-2 bg-blue-500 text-white px-4 py-2 rounded-md flex items-center transition-colors"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-save mr-1"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>Save</button>
            </div>
    </form>
</div><!-- close body -->
<?= $this->endSection();?>
<?= $this->section('scripts') ?>
<script src="<?=base_url('public/assets/js/task.js') ;?>" ></script>
<?= $this->endSection() ;?>