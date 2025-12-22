 //$(document).ready(function() {
loadClients();
            function loadClients(search = '') {
                let filer = $('#filerStatus').val();
                $.ajax({
                    url: App.getSiteurl() +'clients/list',
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
                        <table class="min-w-full divide-y divide-gray-200 border-collapse border bg-gray-100">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Authorized Personnel Details</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                    `;
                   
                    clients.forEach(client => {
                let joinedDate = new Date(client.join_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
                        url = App.getSiteurl()+'clients/edit/'+client.encrypted_id;
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
                                    <div class="text-sm text-gray-900">${client.address}</div>
                                </td>
                               
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        ${
                                            client.clientsInfo.length > 0 
                                            ? client.clientsInfo.map(info => `
                                                <div class="mb-1">
                                                    <strong>${info.authorized_personnel}</strong><br>
                                                    ${info.email ? `<span class="text-gray-600">${info.email}</span><br>` : ''}
                                                    ${info.phone ? `<span class="text-gray-600">${info.phone}</span><br>` : ''}
                                                    ${info.designation ? `<span class="text-gray-600">${info.designation}</span>` : ''}
                                                </div>
                                            `).join('') 
                                            : 'No contacts available'
                                        }

                                    </div>
                                </td>
                                                
                                <td class="px-2 py-2 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="${url}" class="text-blue-600 hover:text-blue-800 mr-3">View</a>
                                    <button onclick="deleteThis(this)" data-id="${client.encrypted_id}" class="p-1.5 rounded-md text-gray-500 hover:bg-red-100 hover:text-red-600 deleteThis " data-bs-toggle="modal" data-bs-target="#deleteModal"  data-message="Are you sure you want to delete ?">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash "><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path></svg></button>
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
      //  });

function closeTaskModal() {
  document.getElementById('taskModal').classList.add('hidden');
}


let selectedTaskId = null;

function deleteThis(e){
    let taskTitle ='';
    selectedTaskId = $(e).data('id'); 
   let message = $(e).data('message')
    $('#deleteTaskMessage').text(message);
};

// Confirm delete click
$('#confirmDelete').on('click', function () {
     $('#confirmDelete').prop('disabled', true).html(
        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Deleting...'
    );
    if (selectedTaskId) {

        $.ajax({
            url: App.getSiteurl()+`client/delete/${selectedTaskId}`, 
            type: 'POST',
            dataType:'json',
            data: { _method: 'DELETE' }, // if using method spoofing in CI4
            success: function (response) {
               if(response.status == true) {
                 setTimeout(function () {
                    $('#deleteModal').modal('hide');
                    $('.modal-backdrop').remove(); // remove the dark background
                    $('body').removeClass('modal-open'); // remove scroll lock
                     $('#confirmDelete').prop('disabled', false).html(
                        'Delete'
                    );
                     loadClients();
                     closeTaskModal();
                }, 2000);
                
                toastr.success(response.msg);
               }else{
                 $('#confirmDelete').prop('disabled', false).html(
                        'Yes'
                    );
                toastr.error(response.msg);
               }
               
            },
            error: function () {
                toastr.error('Error deleting task');
            }
        });
    }
});
