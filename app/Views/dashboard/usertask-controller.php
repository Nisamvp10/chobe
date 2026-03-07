<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
    <!-- titilebar -->
    <div class="flex items-center justify-between">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center mb-0">
                <h1 class="h3 mb-0"><?= $page ?? '' ?></h1>
            </div>
        </div>
    </div><!-- closee titilebar -->
<?= view('modal/createBranch'); ?>
  



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
                    url: "<?= site_url('user-ui/list') ?>",
                    type: "GET",
                    data: { search: search,filter:filter },
                    dataType: "json",
                    success: function(response) {
                        
                        if (response.success) {
                            renderTable(response.tasks);
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
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                    `;
                    projects.forEach(task => {
              
                        html += `
                            <tr class="hover:bg-gray-50 "  >
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="flex items-center">
                                       
                                        <div class="text-sm font-medium text-gray-900">${count}</div>
                                    </div>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">${task.title} [${task.total_tasks} / ${task.completed_tasks}]</div>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                   <span data-id="${task.created_from_template}" onclick="locktask(this)" class="text-white -600 hover:text-fff-800 mr-3 !w-[25px] !h-[25px] rounded-2 bg-blue-500 cursor-pointer font-[15px] block text-center   "><i class="bi bi-check"></i></span>
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
       
            $('#projectForm').on('submit', function(e) {
                let webForm = $('#projectForm');
                e.preventDefault();

                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').empty();

                $('#submitBtn').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
                );

                $.ajax({
                    url : '<?=base_url('project/save');?>',
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
function locktask(e){
        if(confirm('are you sure !'))
        {
            $(e).prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
            );
            let id = $(e).data('id');
            $.ajax({
                url : '<?=base_url('user-ui/unlock');?>',
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

