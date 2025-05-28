<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
    <!-- titilebar -->
    <div class="flex items-center justify-between">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center mb-0">
                <h1 class="h3 mb-0"><?= $page ?? '' ?></h1>
                <div>
                    <button onclick="openModal()" href="<?= base_url('branch/create') ?>" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Add New Category
                    </button>
                </div>
            </div>
        </div>
    </div><!-- closee titilebar -->

    <!-- Modal -->
    <div id="categoryModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Add New Category</h3>
        <button onclick="closeModal()" class="text-gray-400 hover:text-gray-500">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 6 6 18"></path><path d="m6 6 12 12"></path>
            </svg>
        </button>
        </div>

        <!-- Form -->
        <form id="categoryForm" method="post" action="<?= base_url('category/save') ?>">
            <?= csrf_field() ;?>
            <div class="mb-4">
                <label for="categoryName" class="block text-sm font-medium text-gray-700 mb-1">Category Name</label>
                <input type="text" id="category" name="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter category name" required>
                 <div class="invalid-feedback" id="category_error"></div>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                <button id="submitBtn" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md flex items-center transition-colors"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-save mr-1"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>Save</button>
            </div>
        </form>
    </div>
    </div>



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
            <!-- table -->
             <div class="overflow-x-auto">
                <div id="clientsTable"></div>
            </div>
            <!-- close table -->
</div><!-- body -->
<?= $this->endSection(); ?>
<?= $this->section('scripts') ?>
    <script>
         loadClients();
          // modal
       
        //$(document).ready(function() {

            function loadClients(search = '') {
                let filter = $('#filerStatus').val();
                $.ajax({
                    url: "<?= site_url('permisions/list') ?>",
                    type: "GET",
                    data: { search: search,filter:filter },
                    dataType: "json",
                    success: function(response) {
                        
                        if (response.success) {
                            renderTable(response.staff);
                        }
                    }
                });
            }

            function renderTable(staff){
                let html = '';
                let count = 1;

                if (staff.length === 0) {
                    html += `
                        <div class="text-center py-8">
                            <h3 class="text-lg font-medium text-gray-700">No Branches found</h3>
                            <p class="text-gray-500 mt-1">Try adjusting your search</p>
                        </div>
                    `;
                }else{
                    html += `
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">S.NO</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account Status</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                    `;
                    staff.forEach(user => {
              
                        html += `
                            <tr class="hover:bg-gray-50 ${user.status == 2 ? 'bg-red-100 bg-opacity-50' :'' }"  >
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="text-sm font-medium text-gray-900">${count}</div>
                                    </div>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">${user.name}</div>
                                </td>
                                 <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">${user.email}</div>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">${user.role_name}</div>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">${user.status == 'approved' ? '<span class="inline-block bg-blue-50 text-blue-700 text-xs px-2 py-1 rounded-full mr-1 capitalize"> active </span>' : ''}</div>
                                </td>
                              
                                <td class="px-2 py-2 whitespace-nowrap text-right text-sm font-medium">
                                ${user.status == 'approved' ?  `<a href="<?=base_url('permissions/check-permission/');?>${user.encrypted_id}" class="text-blue-600 hover:text-blue-800 mr-3">View</a>` :`<a href="<?=base_url('category/edit/');?>${user.encrypted_id}" class="text-blue-600 hover:text-blue-800 mr-3"><i class="bi bi-lock"></i></a>` }
                                   
                                </td>
                            </tr>
                        `;
                        count++;
                    });
                    

                    html += `</tbody></table>`;
                }
                $('#clientsTable').html(html);
            }
            //loadClients();

            $('#searchInput').on('input',function(){
                let value = $(this).val();
                loadClients(value);
            })
            $('#filerStatus').on('change',function(){
                let value = $('#searchInput').val();
                loadClients(value);
            })
       
            $('#categoryForm').on('submit', function(e) {
                let webForm = $('#categoryForm');
                e.preventDefault();

                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').empty();

                $('#submitBtn').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
                );

                $.ajax({
                    url : '<?=base_url('category/save');?>',
                    method:'POST',
                    data: $(this).serialize(),
                    dataType : 'json',
                    success:function(response)
                    { 
                        if(response.success){
                            toastr.success(response.message);
                            webForm[0].reset();
                            closeModal();
                            loadClients();
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
    
//})
function unlockCategory(e){
        if(confirm('are you sure ! You want to Unlock Category'))
        {
            $(e).prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Unlocking...'
            );
            let id = $(e).data('id');
            $.ajax({
                url : '<?=base_url('category/unlock');?>',
                method:'POST',
                data: {id:id},
                dataType : 'json',
                success:function(response)
                {
                    if(response.success)
                    {
                        toastr.success(response.message);
                          setTimeout(function() {
                           loadClients();
                        }, 3000)
                        

                    }else{
                        toastr.error(response.message);
                    }
                }
            })
        }
    }
    </script>
<?= $this->endSection() ?>

