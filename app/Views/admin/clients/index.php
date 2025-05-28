<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
    <!-- titilebar -->
    <div class="flex items-center justify-between">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center mb-0">
                <h1 class="h3 mb-0"><?= $page ?? '' ?></h1>
                <?php
                if(haspermission(session('user_data')['role'],'create_client')) { ?>
                <div>
                    <a href="<?= base_url('clients/create') ?>" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Add New Client
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
                <input type="text" id="searchInput" placeholder="Search client name, or Phone Number..." class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
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
                    <option value="0">Active</option>
                    <!-- <option value="on leave">On Leave</option> -->
                    <option value="1">Inactive</option>
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
                    url: "<?= site_url('clients/list') ?>",
                    type: "GET",
                    data: { search: search,filer:filer },
                    dataType: "json",
                    success: function(response) {
                        
                        if (response.success) {
                            renderTable(response.clients);
                        }
                    }
                });
            }

            function renderTable(clients){
                let html = '';

                if (clients.length === 0) {
                    html += `
                        <div class="text-center py-8">
                            <h3 class="text-lg font-medium text-gray-700">No Clients found</h3>
                            <p class="text-gray-500 mt-1"> <?=(!haspermission('','view_clients') ? :'Try adjusting your search');?></p>
                        </div>
                    `;
                }else{
                    html += `
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Spend</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Visit</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Visit</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                    `;
                    clients.forEach(client => {
              
                let joinedDate = new Date(client.join_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });

                        html += `
                            <tr class="hover:bg-gray-50">
                                <td class="px-2 py-2 whitespace-nowrap">
                                
                                    <div class="flex items-center">
                                        ${client.profile ? 
                                           `<img class="h-9 w-9 rounded-full mr-3" src="${client.profile}" alt="${client.profile}">` 
                                          :    `<div class="h-9 w-9 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                                <span class="text-blue-600 font-medium">${client.name.charAt(0)}</span>
                                            </div>`
                                        }
                                        <div class="text-sm font-medium text-gray-900">${client.name}</div>
                                    </div>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">${client.phone}</div>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">${joinedDate}</div>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">${client.spend}</div>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">${client.total_visits}</div>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">${client.lastvisit}</div>
                                </td>                         
                                <td class="px-2 py-2 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="<?=base_url('clients/edit/');?>${client.encrypted_id}" class="text-blue-600 hover:text-blue-800 mr-3">View</a>
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
        });
    </script>
<?= $this->endSection() ?>

