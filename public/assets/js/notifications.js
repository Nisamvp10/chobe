  function loadNotifications(search = '') {
                let filer = $('#filerStatus').val();
                $.ajax({
                    url: App.getSiteurl()+'notification-list',
                    type: "POST",
                    data: { search: search,filter:filer },
                    dataType: "json",
                    success: function(response) {
                        
                        if (response.success) {
                            renderTable();
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