<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
    <!-- titilebar -->
    <div class="flex items-center justify-between">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center mb-0">
                <h1 class="h3 mb-0"><?= $page ?? '' ?></h1>
                <?php
                if(haspermission(session('user_data')['role'],'create_branch')) { ?>
                <div>
                    <a href="<?= base_url('staff/create') ?>" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Add New Member
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
                    <option value="all" selected>All Status</option>
                    <option value="1">Active</option>
                    <option value="2">On Leave</option>
                    <option value="3">Inactive</option>
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
        //$(document).ready(function() {
            
            function loadClients(search = '') {
                let filter = $('#filerStatus').val();
                let branch = '<?=$storeId ??''?>'
                $.ajax({
                    url: "<?= site_url('staff/list') ?>",
                    type: "GET",
                    data: { search: search,filter:filter,branch:branch },
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

                if (staff.length === 0) {
                    html += `
                        <div class="text-center py-8">
                            <h3 class="text-lg font-medium text-gray-700">No Team found</h3>
                            <p class="text-gray-500 mt-1">Try adjusting your search</p>
                        </div>
                    `;
                }else{
                    html += `
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Branch</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                    `;
                    staff.forEach(user => {
              
                let joinedDate = new Date(user.hire_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });

                        html += `
                            <tr class="hover:bg-gray-50">
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="flex items-center">
                                        ${user.profileimg ? 
                                           `<img class="h-9 w-9 rounded-full mr-3" src="${user.profileimg}" alt="${user.name}">`
                                           :    `<div class="h-9 w-9 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                                <span class="text-blue-600 font-medium">${user.name.charAt(0)}</span>
                                            </div>`
                                        }
                                        <div class="text-sm font-medium text-gray-900">${user.name}</div>
                                    </div>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mail text-gray-400 mr-1"><rect width="20" height="16" x="2" y="4" rx="2"></rect><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path></svg>
                                        ${user.email}
                                    </div>
                                    <div class="text-sm text-gray-900 flex items-center mt-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone text-gray-400 mr-1"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                        ${user.phone}
                                    </div>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">${joinedDate}</div>
                                </td>
                                 <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 flex items-center">Poistion : ${user.position}</div>
                                     <div class="text-sm text-gray-900 flex items-center mt-1"><span class="inline-block bg-blue-50 text-blue-700 text-xs px-2 py-1 rounded-full mr-1">Role :  ${user.role_name}</span></div>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 flex items-center">
                                        ${user.booking_status == 1 ? ' <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check-circle text-green-500 mr-1"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><path d="m9 11 3 3L22 4"></path></svg><span class="capitalize text-green-600"> Active </span>' : (user.booking_status == 2 ? '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock text-yellow-500 mr-1"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg> <span class="capitalize text-yellow-600">Onleave </span>' : '<i class="bi bi-lock text-md text-gray-400 mr-1"></i> <span class="capitalize text-red-600">Inactive </span>')}</span>
                                    </div>
                                </td>
                              
                             
                                <td class="px-2 py-2 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="<?=base_url('staff/edit/');?>${user.encrypted_id}" class="text-blue-600 hover:text-blue-800 mr-3">Edit</a>
                                   <?php if(haspermission(session('user_data')['role'],'delete_staff')) { ?><button onclick="deletestaff(this)" data-id="${user.encrypted_id}" class="p-1 text-gray-500 hover:text-red-600 transition-colors"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash2 "><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path><line x1="10" x2="10" y1="11" y2="17"></line><line x1="14" x2="14" y1="11" y2="17"></line></svg>
                                                        </button> <?php } ?>
                                </td>
                            </tr>
                        `;
                    });
                    

                    html += `</tbody></table>`;
                }
                $('#clientsTable').html(html);
            }
            loadClients();

            $('#searchInput').on('input',function(){
                let value = $(this).val();
                loadClients(value);
            })
            $('#filerStatus').on('change',function(){
                let value = $('#searchInput').val();
                loadClients(value);
            })
            
        //});

        function deletestaff(e) {
        if (confirm("are you sure")) {
            let id = $(e).data('id');
             $.ajax({
                url : '<?=base_url('staff/delete');?>',
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
                        }, 3000);

                    }else{
                        toastr.error(response.message);
                    }
                }
            })
        }
    }
    </script>
<?= $this->endSection() ?>

