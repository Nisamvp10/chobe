<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
    <!-- titilebar -->
    <div class="flex items-center justify-between">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center mb-0">
                <h1 class="h3 mb-0"><?= $page ?? '' ?></h1>
                <?php 
                if(haspermission(session('user_data')['role'],'Nt')) {  ?>        
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
                    <option value="0">Read</option>
                    <!-- <option value="on leave">On Leave</option> -->
                    <option value="1">Unread</option>
                    </select>
                </div>
            </div>
            </div>
            <!-- table -->
             <div class="overflow-x-auto">
                <div id="notificationList"></div>
            </div>
            <!-- close table -->
</div><!-- body -->
<?= $this->endSection(); ?>
<?= $this->section('scripts') ?>
<script src="<?=base_url('public/assets/js/notifications.js');?>" ></script>
<script>
      function loadNotifications(search = '') {
                let filer = $('#filerStatus').val();
                $.ajax({
                    url: '<?=base_url('notification-list');?>',
                    type: "POST",
                    data: { search: search,filter:filer },
                    dataType: "json",
                    success: function(response) {
                        
                        if (response.success) {
                            renderTable(response.notification);
                        }else{
                            toastr.error(response.message);
                        }
                    }
                });
            }

            function renderTable(notify){

                let notifyHtml = '';

                if (notify.length === 0) {
                    notifyHtml += `
                        <div class="text-center py-8">
                            <h3 class="text-lg font-medium text-gray-700">No Services found</h3>
                            <p class="text-gray-500 mt-1">Try adjusting your search</p>
                        </div>
                    `;
                }else{
                    notifyHtml += `
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">`;
                    notify.forEach(notification => {
                    let genDate = new Date(notification.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });

                        notifyHtml += `
                            <div class="bg-white draggable-task rounded-lg shadow-sm p-4 cursor-pointer hover:shadow-md transition-shadow duration-200 border-l-4 ${notification.is_read == 1 ?'border-orange-500': 'border-green-400'} draggable-task" ${notification.is_read == 1 ? '' : `onclick="viewNotification('${notification.id}')"`}>
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="font-medium text-gray-800  flex-1 text-capitalize">${notification.title}</h3>
                                </div>
                                <p class="text-sm text-gray-600 mb-3 line-clamp-2">${notification.message}</p>
                                <div class="flex justify-between items-center">
                                    <div class="relative rounded-full overflow-hidden flex items-center justify-center w-6 h-6 text-xs border-2 border-white">
                                    ${notification.created_by_image ? 
                                        `<img src="${notification.created_by_image}" alt="${notification.created_by_name}" class="w-full h-full object-cover">`:
                                        `<div class="h-9 w-9 rounded-full bg-blue-100 flex items-center justify-center mr-3">${notification.created_by_name.charAt(0)}</div>` 
                                    }
                                    </div>
                                     <span class="text-xs text-gray-500 ">${notification.created_by_name}</span>
                                    <span class="text-xs text-gray-500 ">${genDate}</span>
                                </div>
                                
                                    <div class="p-2">
                                       
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center text-gray-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock mr-1"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                                <span class="text-sm"> min</span>
                                                </div>
                                          
                                        </div>
                                    </div>
                                </div>
                            
                        `;
                    });

                    notifyHtml += `</div>`;
                }
                $('#notificationList').html(notifyHtml);
            }
            loadNotifications();

            $('#searchInput').on('input',function(){
                let value = $(this).val();
                loadNotifications(value);
            })
            $('#filerStatus').on('change',function(){
                let value = $('#searchInput').val();
                loadNotifications(value);
            })
        //});

        function viewNotification(e) {
            let id = e;
            $.ajax({
                    url:  App.getSiteurl()+'notification/view',
                    type: "POST",
                    data: { id: id },
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                                loadNotifications()
                                toastr.success(response.message);
                                window.location.href=App.getSiteurl()+"tasks/notification-task/"+response.id;
                        }else{
                            toastr.error(response.message);
                        }
                    }
            });
        }
</script>
<?= $this->endSection() ?>

