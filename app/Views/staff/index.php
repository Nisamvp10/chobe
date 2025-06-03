<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

    <!-- titilebar -->
    <div class="flex items-center justify-between">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center mb-0">
                <h1 class="h3 mb-0"><?= $page ?? '' ?></h1>
                 <?php if(haspermission(session('user_data')['role'],'create_staff') ) { ?>
                <div>
                    <a href="<?= base_url('staff/create') ?>" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Add a Member
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
                <input id="searchInput" type="text" placeholder="Search staff by name, email, or position..." class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
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
            <div class="w-full md:w-48">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-filter text-gray-400">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    </div>
                    <select id="filerBanch" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none">
                    <option value="all" selected>All Branch</option>
                    <?php 
                    if (!empty($branches)) {
                        foreach ($branches as $branch) {
                        ?>
                            <option value="<?= $branch['id'] ?>"><?= $branch['branch_name']?></option>
                        <?php
                        } 
                    } ?>
                    </select>
                </div>
            </div>
        </div>
         <div class="overflow-x-auto">
            <div id="staffTable"></div>
        </div>
</div><!-- body -->
<?= $this->endSection(); ?>
<?= $this->section('scripts') ?>
    <script>
       // $(document).ready(function() {
 loadClients();
            function loadClients(search = '') {
                let filter = $('#filerStatus').val();
                let branch = $('#filerBanch').val();
                $.ajax({
                    url: "<?= site_url('staff/list') ?>",
                    type: "GET",
                    data: { search: search,filter:filter,branch:branch},
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
                            <h3 class="text-lg font-medium text-gray-700">No Staff found</h3>
                            <p class="text-gray-500 mt-1">Try adjusting your search</p>
                        </div>
                    `;
                }else{
                    html += `
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">`;
                    staff.forEach(user => {
              
                let joinedDate = new Date(user.hire_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
                const specialties = user.specialties ? user.specialties.split(',') : [];
                let tagSpecialty = '';

                specialties.forEach(spec => {
                    tagSpecialty += `<span class="inline-block bg-blue-50 text-blue-700 text-xs px-2 py-1 rounded-full mr-1">${spec.trim()}</span>`;
                })

                        html += `
                            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-shadow cursor-pointer">
                                <div class="px-3 py-3 whitespace-nowrap_">
                                    <div class="flex items-start">
                                        ${user.profileimg ? 
                                           
                                            `<img src="${user.profileimg}?auto=compress&amp;cs=tinysrgb&amp;w=800" alt="Deep Tissue Massage" class="h-12 w-12 rounded-full mr-4">`
                                            :  `<div class="h-9 w-9 rounded-full bg-blue-100 flex items-center justify-center mr-3" >
                                            <span class="text-blue-600 text-medium"> ${user.name.charAt(0)}</span></div>
                                            ` 
                                        }
                                            <div class="flex-1">
                                                <div class="flex items-center justify-between">
                                                    <h3 class="text-lg font-medium text-gray-800">${user.name}</h3>
                                                    <div class="flex space-x-1">
                                                        <a href="<?=base_url('staff/edit/');?>${user.encrypted_id}" class="p-1 text-gray-500 hover:text-primary-600 transition-colors">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen "><path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.375 2.625a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4Z"></path></svg>
                                                        </a>
                                                            <?php if(haspermission(session('user_data')['role'],'delete_staff')) { ?><button onclick="deletestaff(this)" data-id="${user.encrypted_id}" class="p-1 text-gray-500 hover:text-red-600 transition-colors"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash2 "><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path><line x1="10" x2="10" y1="11" y2="17"></line><line x1="14" x2="14" y1="11" y2="17"></line></svg>
                                                        </button> <?php } ?>
                                                    </div>
                                                </div>
                                                <p class="text-sm text-gray-600">${user.position}</p>
                                                <div class="flex items-center mt-1.5 text-sm">
                                                   ${user.booking_status == 1 ? ' <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check-circle text-green-500 mr-1"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><path d="m9 11 3 3L22 4"></path></svg><span class="capitalize text-green-600"> Active </span>' : (user.booking_status == 2 ? '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock text-yellow-500 mr-1"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg> <span class="capitalize text-yellow-600">Onleave </span>' : '<i class="bi bi-lock text-md text-gray-400 mr-1"></i> <span class="capitalize text-red-600">Inactive </span>')}</span>
                                                </div>
                                            </div>
                                    </div>
                                    <div class="flex items-start">
                                        <div class="mt-2 space-y-2">
                                            <div class="flex items-center text-sm text-gray-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mail mr-2 text-gray-500"><rect width="20" height="16" x="2" y="4" rx="2"></rect><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path></svg>
                                                ${user.email}
                                            </div>
                                             <div class="flex items-center text-sm text-gray-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone mr-2 text-gray-500"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                                ${user.phone}
                                            </div>
                                            <div class="flex items-center text-sm text-gray-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar mr-2 text-gray-500"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M3 10h18"></path></svg>
                                                ${joinedDate}
                                            </div>
                                            <div class="flex items-center text-sm text-gray-600">
                                                <i class="bi bi-shop-window mr-2"></i>
                                                ${user.branch}
                                            </div>
                                        </div>
                                    </div>
                                    ${ specialties.length > 0 ?
                                    `<div class="mt-2">
                                        <p class="text-xs font-medium text-gray-500 mb-2">Specialties</p>
                                        <div class="flex gap-1"> ${tagSpecialty} </div>
                                    </div>` :'' }
                                    </div>
                            </div>`;
                    });
                    

                    html += `</div>`;
                }
                $('#staffTable').html(html);
            }
           
            $('#searchInput').on('input',function(){
                let value = $(this).val();
                loadClients(value);
            })
            $('#filerStatus,#filerBanch').on('change',function(){
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

