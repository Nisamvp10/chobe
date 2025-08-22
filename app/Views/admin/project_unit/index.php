<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
    <!-- titilebar -->
    <div class="flex items-center justify-between">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center mb-0">
                <h1 class="h3 mb-0"><?= $page ?? '' ?></h1>
                <?php if(haspermission('','create_project_unit')) : ?>
                <div>
                    <button onclick="openModal()"  class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#projectUnitModal">
                        <i class="bi bi-plus-circle me-1"></i> Add New Project Unit
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div><!-- closee titilebar -->
<?= view('modal/projectUnit'); ?>
  



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
                <input type="text" id="searchInput" placeholder="Search Store name " class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
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
                    <option value="all">Project Unit Type</option>
                          <option value="Stores">Stores</option>
                            <option value="Suppliers">Suppliers</option>
                            <option value="Managers">Managers</option>
                            <option value="Companies">Companies</option>
                    </select>
                </div>
            </div>
            </div>
            <!-- table -->
             <div class="overflow-x-auto">
                <div id="projectTable"></div>
            </div>
            <!-- close table -->
</div><!-- body -->
<?= $this->endSection(); ?>
<?= $this->section('scripts') ?>
    <script>
         projects();
          // modal
        function openModal() {
            document.getElementById('categoryModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('categoryModal').classList.add('hidden');
        }
        //$(document).ready(function() {

            function projects(search = '') {
                let filter = $('#filerStatus').val();
                $.ajax({
                    url: "<?= site_url('project/list') ?>",
                    type: "GET",
                    data: { search: search,filter:filter },
                    dataType: "json",
                    success: function(response) {
                        
                        if (response.success) {
                            renderTable(response.projects);
                        }
                    }
                });
            }

            function renderTable(projects){
                let html = '';
                let count = 1;

                if (projects.length === 0) {
                    html += `
                        <div class="text-center py-8">
                            <h3 class="text-lg font-medium text-gray-700">No Projects found</h3>
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
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Person</th> 
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Oracle code</th> 
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Polaris code</th> 
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact mail </th> 
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                    `;
                    projects.forEach(project => {
              
                        html2 += `
                            <tr class="hover:bg-gray-50 ${project.is_active ==0 ? 'bg-red-100 bg-opacity-50' :'' }"  >
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="flex items-center">
                                       
                                        <div class="text-sm font-medium text-gray-900">${count}</div>
                                    </div>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">${project.project}</div>
                                </td>
                              
                                <td class="px-2 py-2 whitespace-nowrap text-right text-sm font-medium">
                                ${project.is_active ==1 ?  `<a href="<?=base_url('settings/project/edit/');?>${project.encrypted_id}" class="text-blue-600 hover:text-blue-800 mr-3">View</a>` :`<span data-id="${project.encrypted_id}" onclick="unlockCategory(this)" class="text-blue-600 hover:text-blue-800 mr-3 cursor-pointer "><i class="bi bi-lock"></i></span>` }
                                </td>
                            </tr>
                        `;
                        count++;
                    });
                    

                    html += `</tbody></table>`;
                }
                $('#projectTable').html(html);
            }
            //loadClients();

            $('#searchInput').on('input',function(){
                let value = $(this).val();
                projects(value);
            })
            $('#filerStatus').on('change',function(){
                let value = $('#searchInput').val();
                projects(value);
            })
       
            $('#projectUnitForm').on('submit', function(e) {
                let webForm = $('#projectUnitForm');
                e.preventDefault();

                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').empty();

                $('#submitBtn').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
                );

                $.ajax({
                    url : '<?=base_url('project-unit/save');?>',
                    method:'POST',
                    data: $(this).serialize(),
                    dataType : 'json',
                    success:function(response)
                    { 
                        if(response.success){
                            toastr.success(response.message);
                            webForm[0].reset();
                            closeModal();
                            projects();
                        }else{
                            if(response.errors){
                                $.each(response.errors,function(field,message)
                                {
                                    $('#'+ field).addClass('is-invalid');
                                    $('#' + field + '_error').text(message);
                                })
                            }else{
                                 toastr.error(response.message);
                            }
                        }
                    },error: function() {
                        toastr.error('An error occurred while saving Project');
                    },
                    complete: function() {
                        // Re-enable submit button
                        $('#submitBtn').prop('disabled', false).text('Save ');
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
                url : '<?=base_url('project/unlock');?>',
                method:'POST',
                data: {id:id},
                dataType : 'json',
                success:function(response)
                {
                    if(response.success)
                    {
                        toastr.success(response.message);
                          setTimeout(function() {
                           projects();
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

