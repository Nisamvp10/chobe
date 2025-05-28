<?php $this->extend('layout/main') ;?>
<?= $this->section('content') ?>
<?php
    if(!empty($data))
    {
        $id = encryptor($data['id']);
        $category = $data['category'];
    }else{
        $id=$category = '';
    }
?>
 <!-- titilebar -->
 <div class="flex items-center justify-between">
    <div class="flex items-center gap-4">
        <a href="<?=base_url('categories');?>" class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
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
 <div class="flex flex-col md:flex-row gap-4 mb-6">
    
            <!-- Column 1: Search Input -->
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search text-gray-400">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.3-4.3"></path>
                </svg>
                </div>
                <input type="text" id="searchInput" placeholder="Search branch by name, or location..." class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
            </div>
            
            <!-- Column 2: Status Dropdown -->
            <div class="w-full md:w-48">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-filter text-gray-400">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    </div>
                    <select id="filerStatus" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none">
                    <option value="all">Account Status</option>
                    <option value="1">Active</option>
                    <!-- <option value="on leave">On Leave</option> -->
                    <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
        </div>
    <form id="permissionsForm" method="post">
        <?= csrf_field() ?>
        <div class="w-full md:w-48 gap-4 grid grid-cols-1 mb-3"> 
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                   <i  class="bi bi-building-lock"></i>
                    </div>
                    <select name="role" id="role" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none">
                          <option value="">Choose Role</option>
                        <?php
                        if(!empty($roles)) {
                            foreach ($roles as $role) {
                                ?>
                                    <?= ($role->id !=1 ? '<option value="'.$role->id.'">'.$role->role_name.'</option>' :'') ?>
                                <?php
                            }
                        } ?>
                    </select>
                     <div class="invalid-feedback" id="role_error"></div>
                </div>
            </div>
            <div id="permissionsList"></div>
            <?php if(haspermission(session('user_data')['role'],'create_permissions')) { ?>
             <div class="mt-8 flex justify-end gap-3 mt-3">
                    <?= ($id ? '<button type="button" onClick="deleteBranch(this)" data-id="'.$id.'" class="px-4 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition-colors"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash2 inline-block mr-1"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path><line x1="10" x2="10" y1="11" y2="17"></line><line x1="14" x2="14" y1="11" y2="17"></line></svg>Delete</button>' :'')?>
                    <a href="<?=base_url('categories');?>" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">Cancel</a>
                    <button id="submitBtn" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md flex items-center transition-colors"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-save mr-1"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>Save</button>
                </div>
                <?php } ?>
            <!-- 2 -->
        
        </div>
    </form>
</div><!-- close body -->
<?= $this->endSection();?>
<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {

        $('#permissionsForm').on('submit', function(e) {
            let webForm = $('#permissionsForm');
            e.preventDefault();

            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').empty();

            $('#submitBtn').prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
            );
            $.ajax({
                url : '<?=base_url('permissions/save');?>',
                method:'POST',
                data: $(this).serialize(),
                dataType : 'json',
                success:function(response)
                { 
                    if(response.success){
                        toastr.success(response.message);
                        webForm[0].reset();
                    }else{
                        if(response.errors){
                            $.each(response.errors,function(field,message)
                            {
                                $('#'+ field).addClass('is-invalid');
                                $('#' + field + '_error').text(message);
                            })
                        }
                    }
                },error: function() {
                    toastr.error('An error occurred while saving Branch');
                },
                complete: function() {
                    // Re-enable submit button
                    $('#submitBtn').prop('disabled', false).text('Save Branch');
                }
            })
        })
    })

    function loadPermissions (search = '') {
        let filer = $('#filerStatus').val();
        let roleId = $('#role').val();
        $.ajax({
            url: "<?= site_url('permissions/controls') ?>",
            type: "GET",
            data: { search: search,filer:filer,role:roleId },
            dataType: "json",
            success: function(response) {
                
                if (response.success) {
                     renderTable(response.permissions, response.assignedPermissions);
                }
            }
        })
    }
    function renderTable (permissions,assignedPermissions= []) {
        let html = '';
        if (permissions === 0) {

            html += `
                    <div class="text-center py-8">
                        <h3 class="text-lg font-medium text-gray-700">No Branches found</h3>
                        <p class="text-gray-500 mt-1">Try adjusting your search</p>
                    </div>`;
        }else {
            html += '<div class="grid grid-cols-1 md:grid-cols-6 lg:grid-cols-6 gap-4">';
            permissions.forEach(permission => {
                const isChecked = assignedPermissions.includes(permission.id.toString()) ? 'checked' : '';
                html += ` <div class="bg-white border border-gray-200 rounded-lg p-2 hover:shadow-md transition-shadow cursor-pointer">
                            <h4 class="text-sm font-medium text-gray-800 capitalize">${permission.permission_name.replaceAll('_', ' ')} </h4>
                           <label class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="${permission.id}" ${isChecked} >
                            </label>
                        </div>`;
            })
            html +'</div>';
        }
         $('#permissionsList').html(html);
    }

    loadPermissions();
    $('#role').on('change',function(){
        loadPermissions();
    })
</script>
<?= $this->endSection() ?>
