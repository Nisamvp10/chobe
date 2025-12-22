
 //$(document).ready(function() {
    $('#taskFilerStatus').on('change',function() {
        loadTask();
    })
    $('#searchInput').on('input',function(){
        let value = $(this).val();
        loadTask(value);
    })
    function loadTask(search = '',startDate ='', endDate='') {
        
        let  filter = $('#taskFilerStatus').val();
        let taskId = $('#tasktbl').data('task');
        $.ajax({

            url: App.getSiteurl()+'task/activities',
            type: "GET",
            data: { task:taskId,search: search,filter:filter,startDate:startDate,endDate:endDate},
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    renderTable(response.task);
                }
            }
        });
        
    }

    function renderTable(tasks){
        let html = '';
        let pending = '';
        let inProgress ='';
        let completed = ''

        if (tasks.length === 0) {
            html += `
                <div class="text-center py-8">
                    <h3 class="text-lg font-medium text-gray-700">No Clients found</h3>
                    <p class="text-gray-500 mt-1"> <?=(!haspermission('','view_clients') ? :'Try adjusting your search');?></p>
                </div>`;
        }else{
            
          
            tasks.forEach(task => {

            const dueDate = new Date(task.overdue_date);
            const createdOn = new Date(task.createdAt);
            const today = new Date();
            dueDate.setHours(0, 0, 0, 0);
            today.setHours(0, 0, 0, 0);

            let duedateText = dueDate.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
            let dueClass =    task.status === "pending" && dueDate < today         ? "text-red-600"        : "text-gray-900";

            let createdOnText = createdOn.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });         
            
                var priority = (task.priority =='High' ? 'px-2 py-1 rounded-full text-xs font-medium text-white flex-shrink-0 bg-danger' : (task.priority == "Low" ? 'px-2 py-1 rounded-full text-xs font-medium text-white flex-shrink-0 bg-blue-500' : 'px-2 py-1 rounded-full text-xs font-medium text-white flex-shrink-0 bg-yellow-500'));
                var status = (task.status =='pending' ? 'bg-red-500 text-white' : (task.status == "In_Progress" ? 'bg-yellow-500 text-yellow-800' : (task.status == "completed" ? 'bg-green-500 text-white' : 'bg-green-500 text-green-800' ) ));
                const progress = task.progress ?? 0;

                let unlock = `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-unlock h-4 w-4">
                            <rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 9.9-1"></path>
                            </svg>`;
                let lock = `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-lock h-4 w-4 ">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect>  <path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>`;
                // 
            let ectivitUrl = App.getSiteurl()+`activities/${task.id}`;
             const taskHTML = `
        <div class="bg-white draggable-task rounded-lg shadow-sm p-4 cursor-pointer hover:shadow-md transition-shadow duration-200 border-l-4 ${(task.status == 'pending' ? `border-orange-500` :'border-green-500')} draggable-task" draggable="true"
             data-id="${task.id}"  >
         
            <a class=" " >
            <div class="flex justify-between items-start gap-2 mb-2">
                <h3 class="font-medium text-gray-800 truncate flex-1 text-capitalize">${task.title}</h3>
                <span class="px-2 py-2 rounded-2 text-xs font-medium text-orange-800  flex-shrink-0  ${priority}">${task.priority}</span>
                <span class="px-2 py-2 rounded-2 text-xs font-medium text-orange-800  flex-shrink-0 capitalize ${status}">${task.status}</span>
                <span class="px-2 py-2 rounded-2 text-xs font-medium text-yellow-800  border flex-shrink-0 capitalize" data-id="${task.id}" 
                        data-status="${task.status}"
                        onclick="openTaskModal(this)"
                        data-activity = "${task.activityId}"
                        data-title="${task.title}"
                        data-desc="${task.description}"
                        data-status="${task.status}"
                        data-progress="${progress}%"
                        data-date="${duedateText}"
                        data-created = "${createdOnText}"
                        data-priority="${task.priority}"
                        data-duration="${task.duration}"
                        data-profiles='${JSON.stringify(task.users)}'
                        data-users='${JSON.stringify(task.allUsers)}'
                        data-duedate='${task.overdue_date}'
                        data-store="${(task.storeId ? task.storeId  :'all') }"
                        data-progressbar="${task.progress}"
                        data-cls="${priority}"
                        data-project="${task.project}"
                        data-doc="${task.ducument}"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye h-4 w-4 ">
                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"></path>  <circle cx="12" cy="12" r="3"></circle></svg>
                </span>

                <div class="items-center justify-center  bg-gray-300 pointer border rounded-2 p-2  ${task.status == 'pending' ? 'locktotask' : ''}" data-id="${task.activityId}" >${task.status == 'pending' ? unlock : lock}</div>
            </div>
            <p class="text-sm text-gray-600 mb-3 line-clamp-2">${task.description}</p>
            
            <div >
            <div class="d-flex align-items-center mb-2 d-none">
                <div class="w-full justify-content-between itm-align-end bg-gray-200 rounded-full h-2">
                    <div class="h-2 rounded-full transition-all duration-500 ${(task.progress  == 'pending' ? 'bg-red-500' : 'bg-green-500')} " style="width: ${progress}%"></div> 
                </div>
                <span class="text-xs text-gray-500 text-gray-900">  ${progress}%</span>
            </div>

            </div>
            <div class="flex justify-between items-center">
                <div class="flex -space-x-2 profile-stack ">
                   ${task.allUsers.slice(0, 5).map(user => `
                        ${user.img 
                            ? `<div class="relative rounded-full overflow-hidden flex items-center justify-center w-10 h-10 text-xs border-2 border-white">
                                    <img src="${user.img}" alt="${user.name}" title="${user.name}" class="w-full h-full object-cover">
                            </div>`
                            : `<div class="relative rounded-full overflow-hidden flex items-center justify-center w-10 h-10 text-xs border-2 bg-blue-100 border-white">
                                    <span class="text-blue-600 font-medium">${user.name.charAt(0)}</span>
                            </div>`}
                    `).join('')}

                    ${task.allUsers.length > 5 
                        ? `<div class="relative rounded-full overflow-hidden flex items-center justify-center w-10 h-10 text-xs border-2 bg-gray-200 border-white">
                                <span class="text-gray-700 font-semibold">+${task.users.length - 5}</span>
                        </div>` 
                        : ''}

                </div>
                <span class="text-xs text-gray-500 ${dueClass}">${createdOnText}</span>
            </div>
          </a>
        </div>`;
        
                // 
                progressBar = `<div class="flex items-center gap-2">
                            <div
                                role="progressbar"
                                aria-valuemin=${0}
                                aria-valuemax=${100}
                                aria-valuenow=${progress} 
                                class="relative w-full overflow-hidden rounded-full bg-secondary h-2">
                                <div
                                class="h-full bg-green-500 transition-all"
                                style=" width: ${progress}% "
                                ></div>
                            </div>
                            <span class="text-xs">${progress}%</span>
                            </div>
                            `;
                                            html += `
                    <tr class="hover:bg-gray-50">
                        <td class="px-2 py-1 whitespace-nowrap">
                            <div class="flex items-center">
                              <a class="hover:text-gray-800" href="${App.getSiteurl()+'task/view/'}${task.id}">
                            ${task.title}
                            </a>
                            </div>
                        </td>
                        <td class="px-2 py-1 whitespace-nowrap">
                            <div class="text-sm text-gray-900 hover:text-gray-800">${task.branch_name}</div>
                        </td>
                        <td class="px-2 py-1 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <span class="px-3 py-1 text-xs rounded-full capitalize ${priority} ">${task.priority}</span>
                            </div>
                        </td>
                        <td class="px-6 py-1 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                             <span class="px-2 py-1 text-xs rounded-full capitalize ${status} ">${task.status}</span>
                            </div>
                        </td>
                        <td class="px-2 py-1 whitespace-nowrap">
                            ${progressBar}
                        </td>
                        <td class="px-2 py-1 whitespace-nowrap">
                            <div class="text-sm ${dueClass} ">${duedateText}</div>
                        </td>  
                        <td class="px-2 py-1 whitespace-nowrap">
                            <div class="text-sm ">${task.duration}</div>
                        </td>                         
                       
                    </tr>
                `;
                if (task.status === 'pending') {
                    pending += taskHTML;
                } else if (task.status === 'In_Progress') {
                    inProgress += taskHTML;
                } else {
                    completed += taskHTML;
                }
                
                });
                   
                }
                //$('#taskTable').html(html);
                $('#taskPending').html(pending);
                $('#inProgress').html(inProgress);
                $('#completed').html(completed);
                
            }
        loadTask();

        // drag
        $(document).on('dragstart', '.draggable-task', function (e) {
            e.originalEvent.dataTransfer.setData('text/plain', $(this).data('id'));
            e.originalEvent.dataTransfer.setData('source-status', $(this).data('status'));
        });

   
    $('.task-list').on('dragover', function (e) {
        e.preventDefault(); 
    });

    // Handle drop
    $('.task-list').on('drop', function (e) {
        e.preventDefault();
        const taskId = e.originalEvent.dataTransfer.getData('text/plain');
        const sourceStatus = e.originalEvent.dataTransfer.getData('source-status');
        const targetStatus = $(this).attr('id');
        let newStatus = '';
        if (targetStatus === 'inProgress') {
            newStatus = 'In_Progress';
        } else if (targetStatus === 'completed') {
            newStatus = 'Completed';
        } else if (targetStatus === 'taskPending') {
            newStatus = 'Pending';
        }

        if (newStatus && newStatus !== sourceStatus) {
           
            $.ajax({
                url: App.getSiteurl() + 'task/update_status', 
                method: 'POST',
                data: {
                    task_id: taskId,
                    new_status: newStatus
                },
                success: function (res) {
                    if (res.success) {
                        loadTask(); 
                    } else {
                        alert('Status update failed.');
                    }
                }
            });
        }
    });
 //})


function openTaskModal(el) {
   
    const modal = document.getElementById('taskModal');
    const progressEl = document.getElementById('progressIndicator');
    const progressSlider = document.getElementById('progressBar');
    const progressLabel = document.getElementById('progressLabel');
    const taskId = document.getElementById('activitytaskId');
    let documentUi ='';
    $('#documents').html(documentUi);
    modal.classList.remove('hidden');
    // Fill modal fields from data attributes
    let gettaskId = el.dataset.id ?? '';//modal.querySelector('.modal-title').textContent = el.dataset.id;
    taskId.value = gettaskId;
    $('.delete-task').attr('data-title', el.dataset.title);

    modal.querySelector('.modal-title').textContent = el.dataset.title;
    modal.querySelector('.modal-desc').textContent = el.dataset.desc;
    // modal.querySelector('.modal-branch').textContent = el.dataset.branch;
    let status = modal.querySelector('.modal-status').textContent = el.dataset.status;
    modal.querySelector('.modal-date').textContent = el.dataset.created;
    let progress = modal.querySelector('.modal-progress-bar').style.width = el.dataset.progress;

    let progressbar = el.dataset.progressbar;
    progressSlider.value  = progressbar;
    progressLabel.textContent = 'Progress '+progressbar+'%'
    
    let priority = modal.querySelector('.modal-priority').textContent = el.dataset.priority;
    modal.querySelector('.modal-duration').textContent = el.dataset.duration;
    progressEl.classList.remove('bg-red-500', 'bg-yellow-500', 'bg-green-500');

    let progressCls = (progressbar  < 50 ? 'bg-red-500' : (progressbar > 80 ? 'bg-green-500' :'bg-yellow-500'));
    progressEl.classList.add(progressCls);
    document.getElementById('priorityInput').value = priority;

    //edit data
    const taskEdit = document.getElementById('taskEditForm');
    // Populate fields from data attributes
    taskEdit.querySelector('#title').value = el.dataset.title || '';
    taskEdit.querySelector('#description').value = el.dataset.desc || '';
    //taskEdit.querySelector('#project').value = el.dataset.project || '';
    // taskEdit.querySelector('#branch').value = el.dataset.store || '';
    taskEdit.querySelector('#duedate').value = el.dataset.duedate || 0;
    taskEdit.querySelector('#activityStatus').value = el.dataset.status || 0;

    const priorityButtons = taskEdit.querySelectorAll('.priority-btn');
    priorityButtons.forEach(btn => {
    if (btn.dataset.priority === priority) {
      btn.classList.add('bg-orange-100', 'text-orange-800', 'border-orange-300');
      btn.classList.remove('bg-gray-100', 'text-gray-800', 'border-gray-300');
    } else {
      btn.classList.add('bg-gray-100', 'text-gray-800', 'border-gray-300');
      btn.classList.remove('bg-orange-100', 'text-orange-800', 'border-orange-300');
    }
  });

    const users = JSON.parse(el.dataset.profiles);
    const allstaff = JSON.parse(el.dataset.users);
    renderStaffList(allstaff,users);
    const priorityEl = modal.querySelector('.modal-priority');

    if (priorityEl && el.dataset.cls) {

        priorityEl.className = 'modal-priority';
        const classes = el.dataset.cls.split(' ');
        classes.forEach(cls => {
            priorityEl.classList.add(cls);
        });

        modal.classList.remove('hidden'); 
    }
        const profilesContainer = modal.querySelector('.modal-profiles');
        profilesContainer.innerHTML = '';

        try {
           
            users.forEach(user => {
               const wrapper = document.createElement('div');
                wrapper.className =  'flex items-center gap-2 bg-gray-50 p-2 rounded mb-2';

                const avatar = document.createElement('div');
                avatar.className = 'relative rounded-full overflow-hidden flex items-center justify-center w-10 h-10 text-xs';
                avatar.innerHTML = user.img 
                ? `<img src="${user.img}" alt="${user.staffName}" class="w-full h-full object-cover">`
                : `<div class="relative rounded-full overflow-hidden flex items-center justify-center w-10 h-10 text-xs border-2 bg-blue-100 border-white">
                    <span class="text-blue-600 font-medium">${user.staffName.charAt(0)}</span>
                </div>`;
                const nameSpan = document.createElement('span');
                nameSpan.className = 'text-sm text-gray-700';
                nameSpan.textContent = user.staffName;

                const prio = document.createElement('span');
                prioText = (user.userPriority ==1 ? 'High' : (user.userPriority == 2 ? 'Medium' : 'Low'));
                prio.className = (user.userPriority ==1 ? 'px-2 py-1 rounded-full text-xs font-medium text-white flex-shrink-0 bg-danger' : (user.userPriority == 2 ? 'px-2 py-1 rounded-full text-xs font-medium text-white flex-shrink-0 bg-blue-500' : 'px-2 py-1 rounded-full text-xs font-medium text-white flex-shrink-0 bg-yellow-500'));
                prio.textContent = prioText;

                wrapper.appendChild(avatar);
                wrapper.appendChild(nameSpan);
                wrapper.appendChild(prio);

                profilesContainer.appendChild(wrapper);
            });
        } catch (e) {
            console.error('Invalid profile data:', e);
        }
}

function closeTaskModal() {
  document.getElementById('taskModal').classList.add('hidden');
}

function closeTaskModal(event) {
  if (!event || event.target.id === "taskModal" || event.target.innerText === "✕") {
    document.getElementById("taskModal").classList.add("hidden");
  }
}

function toggleEditForm() {
  const editForm = document.getElementById('editForm');
  const taskDetails = document.getElementById('taskDetails');
  const taskHistory = document.getElementById('taskHistory');
  const replyForm = document.getElementById('replyForm');
  
  // Toggle visibility
    if (editForm.classList.contains('hidden')) 
    {
        editForm.classList.remove('hidden');
        taskDetails.classList.add('hidden');
    } else {
        editForm.classList.add('hidden');
        taskDetails.classList.remove('hidden');
    }
}

function toggleReplay() {
    showOnly('replyForm');
}

 const priorityButtons = document.querySelectorAll('.priority-btn');

  priorityButtons.forEach(btn => {
    btn.addEventListener('click', () => {
          

      priorityButtons.forEach(b => {
        b.classList.remove(
          'bg-orange-100', 'text-orange-800', 'border-orange-300',
          'bg-gray-100', 'text-gray-800', 'border-gray-300'
        );
        b.classList.add('bg-gray-100', 'text-gray-800', 'border-gray-300');
      });

      btn.classList.remove('bg-gray-100', 'text-gray-800', 'border-gray-300');
      btn.classList.add('bg-orange-100', 'text-orange-800', 'border-orange-300');

      const selectedPriority = btn.getAttribute('data-priority');
    //document.getElementById('priorityInput').value = selectedPriority;
      document.getElementById('priorityInput').value = selectedPriority;
    });
  });

  const progressSlider = document.getElementById('progressBar');
  const progressLabel = document.getElementById('progressLabel');

  progressSlider.addEventListener('input', () => {
    progressLabel.textContent = `Progress: ${progressSlider.value}%`;
  });

function renderStaffList(users = [], existingUser = []) {
    
    const staffListContainer = document.getElementById('participantsactivities');
    staffListContainer.innerHTML = '';

    const assignedIds = existingUser.map(u => u.userId);

    users.forEach(staff => {
        const isChecked = assignedIds.includes(staff.id.toString()) ? 'checked' : '';

        const staffHTML = `
        <div class="flex align-items-center p-2 border rounded-md cursor-pointer border-gray-300">
            <input type="checkbox" class="h-4 w-4 text-indigo-600 rounded" 
                   name="staff[]" value="${staff.id}" ${isChecked}>
                   ${staff.profileimg ? 
                   
            `<img src="${staff.profileimg ?? 'default.png'}" 
                 alt="${staff.name}" class="w-6 h-6 rounded-full ml-2">` :
                 `<div class="h-9 w-9 ml-2 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                                <span class="text-blue-600 font-medium">${staff.name.charAt(0)}</span>
                                            </div>`}
            <span class="ml-2 text-sm mx-4">${staff.name}</span>
        </div>`;
        staffListContainer.insertAdjacentHTML('beforeend', staffHTML);
    });
}


$('#taskEditForm').on('submit', function(e) {


    let webForm = $('#taskEditForm');
    e.preventDefault();
    let formData = new FormData(this);
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').empty();
    $('#submitBtn').prop('disabled', true).html(
        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
    );
    $.ajax({
        url : App.getSiteurl()+'task/activityupdate',
        method:'POST',
        data: formData,
        contentType: false,
        processData: false,
        success:function(response)
        { 
            if(response.success){
                toastr.success(response.message);
                //webForm[0].reset();
                loadTask();
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
            toastr.error('An error occurred while saving Service');
        },
        complete: function() {
            // Re-enable submit button
            $('#submitBtn').prop('disabled', false).text('Save Branch');
        }
    })
})


$(function() {
    $('#filterDate').daterangepicker({
        opens: 'left',
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear',
            format: 'DD-MM-YYYY'
        },
        ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    });

    // Apply event
    $('#filterDate').on('apply.daterangepicker', function(ev, picker) {
         let startDate = picker.startDate.format('YYYY-MM-DD');
        let endDate = picker.endDate.format('YYYY-MM-DD');
        $(this).val(startDate + ' to ' + endDate);
        let search = $('#searchInput').val();
        loadTask(search,startDate, endDate)     

        
    });

    // Cancel event
    $('#filterDate').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        $('#results').empty();
    });
});

let selectedTaskId = null;

function deleteTask(e){
    let taskTitle ='';
    selectedTaskId = $('#taskEditForm #taskId').val(); // store the clicked task ID
    //let taskTitle = $(e).data('title');
    // Update modal message
    $('#deleteTaskMessage').text(`Are you sure you want to delete ? This action cannot be undone.`);

    // Show modal "${taskTitle}"
    //$('#deleteModal').modal('show');
};

// Confirm delete click
$('#confirmDeleteTask').on('click', function () {
     $('#confirmDeleteTask').prop('disabled', true).html(
        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Deleting...'
    );
    if (selectedTaskId) {

        $.ajax({
            url: App.getSiteurl()+`task/delete/${selectedTaskId}`, 
            type: 'POST',
            dataType:'json',
            data: { _method: 'DELETE' }, // if using method spoofing in CI4
            success: function (response) {
               if(response.status == true) {
                 setTimeout(function () {
                    $('#deleteModal').modal('hide');
                    $('.modal-backdrop').remove(); // remove the dark background
                    $('body').removeClass('modal-open'); // remove scroll lock
                     $('#confirmDeleteTask').prop('disabled', false).html(
                        'Delete'
                    );
                     loadTask();
                     closeTaskModal();
                }, 2000);
                
                toastr.success(response.msg);
               }else{
                toastr.error(response.msg);
               }
               
            },
            error: function () {
                toastr.error('Error deleting task');
            }
        });
    }
});

$(document).on('click', '.locktotask', function (e) {
    e.preventDefault();
    let activityId = $(this).data('id');
    $.ajax({
        method :  'POST',
        url : App.getSiteurl() + 'activity/lock',
        data:{id:activityId},
        dataType: 'json',
        success:function(res) {
           if(res.success) {
            toastr.success(res.message);
            loadTask();
           }else{
            toastr.error(res.message);
           }
        }
    })
})