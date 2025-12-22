<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
    <!-- titilebar -->
    <div class="flex items-center justify-between">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center mb-0">
                <h1 class="h3 mb-0"><?= $page ?? '' ?></h1>
                <?php 
                if(haspermission(session('user_data')['role'],'create_services')) {  ?>        
                <div>
                    <a href="<?= base_url('services/create') ?>" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Add New Service
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
                <input type="text" id="searchInput" placeholder="Search Service by name, Category or price ..." class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
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
                    <option value="all">Account Type</option>
                    <option value="0">Normal</option>
                    <!-- <option value="on leave">On Leave</option> -->
                    <option value="1">Popular</option>
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
        $(document).ready(function() {

            function loadClients(search = '') {
                let filer = $('#filerStatus').val();
                $.ajax({
                    url: "<?= site_url('services/list') ?>",
                    type: "GET",
                    data: { search: search,filter:filer },
                    dataType: "json",
                    success: function(response) {
                        
                        if (response.success) {
                            renderTable(response.services);
                        }
                    }
                });
            }

            function renderTable(services){
                let html = '';

                if (services.length === 0) {
                    html += `
                        <div class="text-center py-8">
                            <h3 class="text-lg font-medium text-gray-700">No Services found</h3>
                            <p class="text-gray-500 mt-1">Try adjusting your search</p>
                        </div>
                    `;
                }else{
                    html += `
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">`;
                    services.forEach(service => {
              
                //let joinedDate = new Date(branch.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });

                        html += `
                            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-shadow cursor-pointer">
                                <div class="px-2 py-2 whitespace-nowrap_">
                                    <div class="relative h-40 overflow-hidden">
                                        ${service.image_url ? 
                                           
                                            `<img src="${service.image_url}?auto=compress&amp;cs=tinysrgb&amp;w=800" alt="Deep Tissue Massage" class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-300">
                                            `: `<div class="h-9 w-9 rounded-full bg-blue-100 flex items-center justify-center mr-3">${service.name.charAt(0)}</div>` 
                                        }
                                        ${service.is_popular ==1 ? 
                                        `<div class="absolute top-3 right-3 bg-yellow-400 text-gray-900 text-xs font-bold px-2 py-1 rounded-full">Popular</div>` : ''
                                        }
                                    </div>
                                    </div>
                                    <div class="p-2">
                                        <div class="flex justify-between items-start mb-2">
                                            <h3 class="text-lg font-medium text-gray-800"><a href="<?=base_url('service/edit/');?>${service.encrypted_id}">${service.name}</a></h3>
                                            <div class="inline-block bg-blue-50 text-blue-700 text-xs px-2 py-1 rounded-full">${service.category_name}</div>
                                        </div>
                                        <p class="text-sm text-gray-600 mb-4 line-clamp-2">${service.description}</p>
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center text-gray-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock mr-1"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                                <span class="text-sm">${service.duration} min</span>
                                                </div>
                                            <div class="flex items-center font-medium text-lg text-gray-900">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-dollar-sign text-green-600"><line x1="12" x2="12" y1="2" y2="22"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                                                ${service.price}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            
                        `;
                    });
                    

                    html += `</div>`;
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
        });
    </script>
<?= $this->endSection() ?>

