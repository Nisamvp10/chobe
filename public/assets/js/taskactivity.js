
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
        let ui ='';

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
             const taskHTML = `<div class="bg-white rounded-lg shadow-sm hover:shadow-md transition 
border-l-4 ${task.status === 'pending' ? 'border-orange-500' : 'border-green-500'} 
p-4 draggable-task cursor-pointer"
draggable="true"
data-id="${task.id}">

    <!-- TITLE -->
    <h6 class="text-sm font-semibold text-blue-600 truncate mb-2">
        ${task.title}
    </h6>

    <!-- ROW 1 : DESCRIPTION + COMMENT + LOCK -->
    <div class="flex justify-between items-center gap-4">

        <!-- DESCRIPTION -->
        <div class="flex-1 text-xs text-gray-600 line-clamp-2">
            ${task.description}
        </div>
        ${task.copen == 1 ? `
            <form class="comment-form flex items-center gap-2 w-72
                        ${task.comment ? 'is-locked' : ''}"
                data-task-id="${task.id}"
                data-activity-id="${task.activityId}">

                <textarea
                    name="comment"
                    rows="1"
                    placeholder="Enter comment..."
                    class="comment-text border rounded-md px-3 py-1 text-xs w-full resize-none
                        focus:outline-none focus:ring
                        ${task.comment ? 'bg-gray-200 cursor-not-allowed' : ''}"
                    ${task.comment ? 'disabled' : ''}>${task.comment ?? ''}</textarea>

                <!-- ✏️ EDIT BUTTON -->
                ${task.comment ? `
                <button type="button"
                        class="edit-comment p-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                        title="Edit Comment">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen "><path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.375 2.625a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4Z"></path></svg>
                </button>` : ''}

                <!-- ✔ SAVE BUTTON -->
                <button type="submit"
                        class="save-comment p-2 bg-green-500 text-white rounded hover:bg-green-600
                            ${task.comment ? 'hidden' : ''}"
                        title="Save Comment">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 13l4 4L19 7" />
                </svg>
                </button>
            </form>` : ''}

        <!-- LOCK -->
        <div 
            class="p-2 border rounded-md cursor-pointer hidden
            ${task.status === 'pending' ? 'bg-gray-200 locktotask' : 'bg-green-100'}"
            data-id="${task.activityId}">
            ${task.status === 'pending' ? unlock : lock}
        </div>
    </div>

    <!-- ROW 2 : BADGES + DATES + VIEW -->
    <div class="flex flex-wrap items-center gap-2 text-xs text-gray-600 mt-3">

        <span class="px-2 py-1 rounded-full ${priority}">
            ${task.priority}
        </span>

        <span class="px-2 py-1 rounded-full capitalize ${status}">
            ${task.status}
        </span>

        <span>${createdOnText}</span>
        <span>${duedateText}</span>

        <!-- VIEW ICON -->
        <button
            class="ml-auto p-2 border rounded-2 hover:bg-gray-100"
            onclick="openTaskModal(this)"
            data-id="${task.id}"
            data-activity="${task.activityId}"
            data-title="${task.title}"

            data-desc="${task.description}"
            data-status="${task.status}"
            data-progress="${progress}%"
            data-comments='${JSON.stringify(task.allCommets)}'
            data-date="${duedateText}"
            data-created="${createdOnText}"
            data-priority="${task.priority}"
            data-duration="${task.duration}"
            data-profiles='${JSON.stringify(task.users)}'
            data-users='${JSON.stringify(task.allUsers)}'
            data-duedate='${task.overdue_date}'
            data-store="${task.storeId ?? 'all'}"
            data-progressbar="${task.progress}"
            data-project="${task.project}"
            data-doc="${task.ducument}" >
         
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-600"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z" />
                <circle cx="12" cy="12" r="3" />
            </svg>
        </button>

    </div>

</div>`

        
                // 
              ui += taskHTML;
                                
                if (task.status === 'pending') {
                    pending += taskHTML;
                } else if (task.status === 'In_Progress') {
                    inProgress += taskHTML;
                } else {
                    completed += taskHTML;
                }
                
                });
                   
                }
                $('#activityTask').html(ui);
                // $('#taskPending').html(pending);
                // $('#inProgress').html(inProgress);
                // $('#completed').html(completed);
                
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
    const taskId = document.getElementById('taskId');
    
    let documentUi ='';
    $('#documents').html(documentUi);
    modal.classList.remove('hidden');
    // Fill modal fields from data attributes
    let gettaskId = el.dataset.activity ?? '';//modal.querySelector('.modal-title').textContent = el.dataset.id;
    taskId.value =  gettaskId;
    $('.delete-task').attr('data-title', el.dataset.title);
    modal.querySelector('.modal-title').textContent = el.dataset.title;
    modal.querySelector('.modal-desc').textContent = el.dataset.desc;
    // modal.querySelector('.modal-branch').textContent = el.dataset.branch;
    let status = modal.querySelector('.modal-status').textContent = el.dataset.status;
    modal.querySelector('.modal-date').textContent = el.dataset.created;
    //let progress = modal.querySelector('.modal-progress-bar').style.width = el.dataset.progress;

    // let progressbar = el.dataset.progressbar;
    // progressSlider.value  = progressbar;
    // progressLabel.textContent = 'Progress '+progressbar+'%'
    
    let priority = modal.querySelector('.modal-priority').textContent = el.dataset.priority;
    modal.querySelector('.modal-duration').textContent = el.dataset.duration;
    //progressEl.classList.remove('bg-red-500', 'bg-yellow-500', 'bg-green-500');

  
    //document.getElementById('priorityInput').value = priority;

    //edit data
    // const taskEdit = document.getElementById('taskEditForm');
    // // Populate fields from data attributes
    // taskEdit.querySelector('#title').value = el.dataset.title || '';
    // taskEdit.querySelector('#description').value = el.dataset.desc || '';
    // //taskEdit.querySelector('#project').value = el.dataset.project || '';
    // // taskEdit.querySelector('#branch').value = el.dataset.store || '';
    // taskEdit.querySelector('#duedate').value = el.dataset.duedate || 0;
    // taskEdit.querySelector('#activityStatus').value = el.dataset.status || 0;

//     const priorityButtons = taskEdit.querySelectorAll('.priority-btn');
//     priorityButtons.forEach(btn => {
//     if (btn.dataset.priority === priority) {
//       btn.classList.add('bg-orange-100', 'text-orange-800', 'border-orange-300');
//       btn.classList.remove('bg-gray-100', 'text-gray-800', 'border-gray-300');
//     } else {
//       btn.classList.add('bg-gray-100', 'text-gray-800', 'border-gray-300');
//       btn.classList.remove('bg-orange-100', 'text-orange-800', 'border-orange-300');
//     }
//   });
    const users = JSON.parse(el.dataset.profiles);
    const allstaff = JSON.parse(el.dataset.users);
    const comments = JSON.parse(el.dataset.comments);
    renderStaffList(allstaff,users);
    renderCommentList(comments);
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
       // profilesContainer.innerHTML = '';

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

                //profilesContainer.appendChild(wrapper);
            });
        } catch (e) {
            console.error('Invalid profile data:', e);
        }
        renderHistory(gettaskId)
        startPolling(gettaskId);   
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

//   progressSlider.addEventListener('input', () => {
//     progressLabel.textContent = `Progress: ${progressSlider.value}%`;
//   });
// sadssdsdsdsd 123
function renderStaffList(users = [], existingUser = []) {
    const assignedIds = existingUser.map(u => String(u.userId));
    let staffHTML = '';

    users.forEach(staff => {
        const isChecked = assignedIds.includes(String(staff.id)) ? 'checked' : '';
        staffHTML += `<div class="flex items-center p-2 border rounded-md cursor-pointer border-gray-300 gap-2">
        
            
            <input type="checkbox" class="h-4 w-4 hidden text-indigo-600 rounded" name="staff[]" value="${staff.id}"
                   ${isChecked}>

            ${
                staff.profileimg
                ? `<img src="${staff.profileimg}" 
                        alt="${staff.name}" 
                        class="w-8 h-8 rounded-full">`
                : `<div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                        <span class="text-blue-600 font-medium">
                            ${staff.name.charAt(0).toUpperCase()}
                        </span>
                   </div>`
            }

            <span class="text-sm font-medium">${staff.name}</span>
        </div>`;
    });
    document.getElementById('modal-profiles').innerHTML = staffHTML;
}

function renderCommentList(comments = []) {

    let commentHtml = `<div class="space-y-4 max-h-64 overflow-y-auto p-2">`;

    if (comments.length === 0) {
        commentHtml += `
            <div class="text-center text-gray-500">
                No comments yet ...
            </div>
        `;
    } else {
        comments.forEach(cmt => {
            console.log(cmt);
            commentHtml += `
                <div class="border rounded-md p-2">
                    <div class="font-medium text-sm flex items-cnter gap-2 mb-1">
                       <div> ${cmt.name ?  cmt.name : 'Unknown'}</div>
                       <div>  ${cmt.created_at}</div>
                    </div>
                    <div class="text-sm text-gray-700">
                        ${cmt.comment}
                    </div>
                </div>
            `;
        });
    }

    commentHtml += `</div>`;
    document.getElementById('commentSection').innerHTML = commentHtml;

}

function showStep(step) {
    // Hide all steps
    $('.step1, .step2,.step3').hide();

    // Remove active button style
    $('.modal-action-btn').removeClass('bg-gray-200 text-gray-800');

    // Show selected step
    $('.step' + step).show();
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




/*******************************
 * POLLING VARIABLES
 *******************************/
let replyPolling = null;
let lastReplyId = 0;

/*******************************
 * START / STOP POLLING
 *******************************/
function startPolling(taskId) {
    if (replyPolling) return;

    replyPolling = setInterval(() => {
        renderHistory(taskId);
    }, 5000); // 5 seconds
}

function stopPolling() {
    if (replyPolling) {
        clearInterval(replyPolling);
        replyPolling = null;
    }
}

/*******************************
 * LOAD REPLY HISTORY (AJAX)
 *******************************/
function renderHistory(taskId) {
    $.ajax({
        url: App.getSiteurl() + 'activity-task-replays',
        type: "POST",
        data: { taskId: taskId },
        dataType: "json",
        success: function (response) {
            if (response.success) {
                renderReplayUi(response.replay);
            }
        }
    });
}

/*******************************
 * RENDER CHAT UI
 *******************************/
function renderReplayUi(replay) {

    let html = '';
    let form = ``;

    /*******************************
     * NO REPLIES
     *******************************/
    if (replay.length === 0) {
        $('.msgNotification').html('');
        html = `
            <div class="text-center py-8">
                <h3 class="text-lg font-medium text-gray-700">No Reply yet</h3>
            </div>
        `;
    }
    /*******************************
     * HAS REPLIES
     *******************************/
    else {
        let lastDate = '';
        html += `<ul class="space-y-4">`;
       

        replay.forEach(rply => {

            // 🔔 Notification badge
            $('.msgNotification').html(`
                <span class="absolute right-0 top-0 flex items-center justify-center
                    w-[20px] h-[20px] rounded-full bg-green-500 text-white text-xs">
                    ${rply.message_count}
                </span>
            `);

            lastReplyId = rply.rpId; //is last_reply_id

            const replyDate = new Date(rply.created_at);
            const msgDate = replyDate.toDateString();
            const today = new Date().toDateString();
            const yesterday = new Date(Date.now() - 86400000).toDateString();

            let displayDate = msgDate;
            if (msgDate === today) displayDate = 'Today';
            else if (msgDate === yesterday) displayDate = 'Yesterday';

            const time = replyDate.toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit'
            });

            // Date separator
            if (msgDate !== lastDate) {
                html += `
                    <li class="flex justify-center">
                        <span class="px-4 py-1 text-xs text-gray-500 bg-gray-100 rounded-full">
                            ${displayDate}
                        </span>
                    </li>
                `;
                lastDate = msgDate;
            }

            const isAdmin = rply.is_admin == 1;

            html += `
                <li class="flex ${isAdmin ? 'justify-end' : 'justify-start'}">
                    <div class="max-w-[75%] flex items-end gap-2
                        ${isAdmin ? 'flex-row-reverse' : 'flex-row'}">

                        <!-- Avatar -->
                        <div class="w-8 h-8 rounded-full overflow-hidden bg-gray-200 flex items-center justify-center text-xs">
                            ${
                                rply.profileimg
                                ? `<img src="${rply.profileimg}" class="w-full h-full object-cover">`
                                : `<span class="font-semibold">${rply.name.charAt(0)}</span>`
                            }
                        </div>

                        <!-- Message -->
                        <div class="px-4 py-2 rounded-2xl shadow text-sm
                            ${isAdmin
                                ? 'bg-blue-600 text-white rounded-br-sm'
                                : 'bg-gray-100 text-gray-800 rounded-bl-sm'}">
                            <p>${rply.reply_text}</p>
                            <div class="mt-1 text-[11px] opacity-70 text-right">
                                ${time}
                            </div>
                        </div>
                    </div>
                </li>
            `;
        });

        html += `</ul>`;
    }

    $('#taskreplaysec').html(html);
    //$('#taskreplayForm').html(form);
}

function showNewMessageToast() {
    const toast = document.createElement('div');
    toast.className = `
        fixed bottom-6 right-6 bg-green-600 text-white
        px-4 py-2 rounded-lg shadow-lg text-sm z-50
    `;
    toast.innerText = 'New message received';

    document.body.appendChild(toast);

    setTimeout(() => toast.remove(), 3000);
}


/*******************************
 * SUBMIT REPLY FORM
 *******************************/
$(document).on('submit', '#replyTaskForm', function (e) {
    e.preventDefault();

    let taskId = $('#taskId').val();
    let formData = new FormData(this);
    formData.append('taskId', taskId);

   $('#replaysubmitBtn').prop('disabled', true).html(
        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
    );

    $.ajax({
        url:App.getSiteurl()+'task/activity/replay',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (res) {
            if (res.success) {
                $('#replyTaskForm')[0].reset();
                renderHistory(taskId); // 🔄 refresh immediately
            }
        },
        complete: function () {
            $('#replaysubmitBtn').prop('disabled', false).text('Send');
        }
    });
});


// edit comment
document.addEventListener('click', function (e) {

    const editBtn = e.target.closest('.edit-comment');
    if (!editBtn) return;

    const form = editBtn.closest('.comment-form');
    const textarea = form.querySelector('.comment-text');
    const saveBtn = form.querySelector('.save-comment');

    // Enable textarea
    textarea.disabled = false;
    textarea.classList.remove('bg-gray-200', 'cursor-not-allowed');
    textarea.focus();

    // Toggle buttons
    editBtn.classList.add('hidden');
    saveBtn.classList.remove('hidden');
});
