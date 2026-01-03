
 //$(document).ready(function() {
     $('#filerStatus').on('change',function() {
        loadTask();
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
        taskHTML = '';

        if (tasks.length === 0) {
            taskHTML += `
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
             const taskHTML = `<div class="bg-white rounded-lg shadow-sm hover:shadow-md transition border-l-4 ${task.status === 'pending' ? 'border-orange-500' : 'border-green-500'} p-4 draggable-task cursor-pointer" draggable="true" data-id="${task.id}">

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
        <!-- COMMENT FORM -->
        <form class="comment-form flex items-center gap-2 w-72 " data-task-id="${task.id}" data-activity-id="${task.activityId}">
            <textarea
                name="comment"
                rows="1"
                placeholder="Enter comment..."
                class="border rounded-md px-3 py-1 text-xs w-full resize-none focus:outline-none focus:ring comment-text">${task.comment ?? ''}</textarea>

            <!-- SAVE ICON -->
            <button 
                type="submit"
                class="p-2 bg-blue-500 text-white rounded-2 hover:bg-blue-600"
                title="Save Comment"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 13l4 4L19 7" />
                </svg>
            </button>
        </form>`: ''}

        <!-- LOCK -->
        <div 
            class="p-2 border rounded-md cursor-pointer
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
            data-doc="${task.ducument}"
        >
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
                             <span class="px-2 py-1 text-xs rounded-full capitalize ${status}">${task.status}</span>
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
                } else if (task.status === 'in_Progress') {
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

        
function openTaskModal(el) {
    const modal = document.getElementById('taskModal');
    const progressEl = document.getElementById('progressIndicator');
    
    const progressLabel = document.getElementById('progressLabel');
    const taskId = document.getElementById('taskId');
    modal.classList.remove('hidden');
    

    // Fill modal fields from data attributes
    let gettaskId =  modal.querySelector('.modal-title').textContent = el.dataset.activity;
    taskId.value = gettaskId;
    modal.querySelector('.modal-title').textContent = el.dataset.title;
    modal.querySelector('.modal-desc').textContent = el.dataset.desc;
    // modal.querySelector('.modal-branch').textContent = el.dataset.branch;
    let statusEl = modal.querySelector('.modal-status');
    let status = el.dataset.status;

    statusEl.textContent = status;
    statusEl.className = `modal-status ${
        status === 'pending' ? '!text-red-800 capitalize' : 'capitalize !text-green-500'
    }`;
    modal.querySelector('.modal-date').textContent = el.dataset.date;
    let progress = modal.querySelector('.modal-progress-bar').style.width = el.dataset.progress;

    let progressbar = el.dataset.progressbar;

     let documentUi = el.dataset.doc ? ` <div class="d-flex align-items-center mb-2 mt-2 d-none">
                <a href="${el.dataset.doc}" target="_blank" class="relative px-3 py-1  overflow-hidden flex items-center justify-center rounded-lg text-xs border-1 rouded-5 border text-blue-700">
                    Doc
                </a>`:'';
    $('#documents').html(documentUi);
      
    //console.log(progressbar)
    let priority = modal.querySelector('.modal-priority').textContent = el.dataset.priority;
    // modal.querySelector('.modal-duration').textContent = el.dataset.duration;
    progressEl.classList.remove('bg-red-500', 'bg-yellow-500', 'bg-green-500');

    let progressCls = (progressbar  < 50 ? 'bg-red-500' : (progressbar > 80 ? 'bg-green-500' :'bg-yellow-500'));
    progressEl.classList.add(progressCls);
  
    //edit data
    const users = JSON.parse(el.dataset.profiles);
    //renderStaffList(users);
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
                avatar.innerHTML = `${user.img ? `<img src="${user.img}" alt="" class="w-full h-full object-cover">` :`<div class="relative rounded-full overflow-hidden flex items-center justify-center w-10 h-10 text-xs border-2 bg-blue-100 border-white">
                                    <span class="text-blue-600 font-medium">${user.staffName.charAt(0)}</span>
                            </div>`}`;

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
        
        renderHistory(gettaskId)
        startPolling(gettaskId);   
}

function closeTaskModal() {
  document.getElementById('taskModal').classList.add('hidden');
  
}

function closeTaskModal(event) {
  if (!event || event.target.id === "taskModal" || event.target.innerText === "✕") {
    document.getElementById("taskModal").classList.add("hidden");
    stopPolling()
  }
}
let isHistoryOpen = false;

function toggleHistory() {
    const history = document.getElementById('taskHistory');
    const details = document.getElementById('taskDetails');
    const reply = document.getElementById('replyForm');

    if (!isHistoryOpen) {
        history.classList.remove('hidden');
        details.classList.add('hidden');
        reply.classList.add('hidden');
        isHistoryOpen = true;
    } else {
        history.classList.add('hidden');
        details.classList.remove('hidden');
        reply.classList.add('hidden');
        isHistoryOpen = false;
    }
}

function toggleReply() {
    const history = document.getElementById('taskHistory');
    const details = document.getElementById('taskDetails');
    const reply = document.getElementById('replyForm');

    history.classList.add('hidden');
    details.classList.add('hidden');
    reply.classList.remove('hidden');
    isHistoryOpen = false;
}

function hideReplyForm() {
    const history = document.getElementById('taskHistory');
    const details = document.getElementById('taskDetails');
    const reply = document.getElementById('replyForm');

    reply.classList.add('hidden');
    history.classList.add('hidden');
    details.classList.remove('hidden');
    isHistoryOpen = false;
}
// // set like poll
// let replyPolling = null;
// let lastReplyId = 0;


// function startPolling(taskId) {
//     if (replyPolling) return;

//     replyPolling = setInterval(() => {
//         renderHistory(taskId);
//     }, 5000); // 5 seconds
// }

// function stopPolling() {
//     if (replyPolling) {
//         clearInterval(replyPolling);
//         replyPolling = null;
//     }
// }

// function renderHistory(id) {
//     $.ajax({
//         url: App.getSiteurl()+'activity-task-replays',
//         type: "POST",
//         data: { taskId: id},
//         dataType: "json",
//         success: function(response) {
          
//             if (response.success) {
//                 renderReplayUi(response.replay);
//             }
//         }
//     });
// }
// function renderReplayUi(replay) {
//   let html = '';
//   from =`<div>
//             <form class="mb-4" method="post" id="replyTaskForm">
//                 <?= csrf_field() ;?>
                
//                   <div class="flex space-x-2" >
//                     <textarea placeholder="Enter your Comments..." name="replay" class="flex-1 min-h-[100px] p-3 border rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
//                      <div class="invalid-feedback" id="replay_error"></div>
//                 </div>
//                 <div class="flex justify-end space-x-2 mt-2 gap-2">
//                     <button type="button" class="px-3 py-1 text-gray-600 zrounded-md border rounded-2" onclick="hideReplyForm()">Cancel</button>
//                     <button type="submit" class="flex items-center rounded-2 space-x-1 px-3 py-1 bg-primary text-white rounded-md hover:bg-indigo-700">
//                         <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-send">
//                             <path d="M22 2 11 13"></path><path d="M22 2 15 22 11 13 2 9 22 2z"></path>
//                         </svg>
//                         <span>Send</span>
//                     </button>
//                 </div>
//             </form>
//         </div>`

// if (replay.length === 0) {
//   $('.msgNotification').html(``);
//     html = `
//         <div class="text-center py-8">
//             <h3 class="text-lg font-medium text-gray-700">No Reply yet</h3>
//         </div>`
// } else {

//     let lastDate = '';

//     html += `<ul class="space-y-4">`;

//     replay.forEach(rply => {
//         $('.msgNotification').html(`
//             <span class="absolute right-0 top-0 flex items-center justify-center 
//                         !w-[20px] !h-[20px] rounded-full bg-green-500 text-white text-xs">
//                 ${rply.message_count}
//             </span>
//         `);
//         lastReplyId = rply.rpId
        
//         const replyDateObj = new Date(rply.created_at);

//         const msgDate = replyDateObj.toDateString(); // compare only date
//         const today = new Date().toDateString();
//         const yesterday = new Date(Date.now() - 86400000).toDateString();

//         let displayDate = msgDate;
//         if (msgDate === today) displayDate = 'Today';
//         else if (msgDate === yesterday) displayDate = 'Yesterday';

//         const time = replyDateObj.toLocaleTimeString([], {
//             hour: '2-digit',
//             minute: '2-digit'
//         });

//         // Show date separator only when date changes
//         if (msgDate !== lastDate) {
//             html += `
//                 <li class="flex justify-center">
//                     <span class="px-4 py-1 text-xs text-gray-500 bg-gray-100 rounded-full">
//                         ${displayDate}
//                     </span>
//                 </li>
//             `;
//             lastDate = msgDate;
//         }

//         const isAdmin = rply.is_admin == 1; // adjust if needed

//         html += `
//         <li class="flex ${isAdmin ? 'justify-end' : 'justify-start'}">
//             <div class="max-w-[75%] flex items-end gap-2 ${isAdmin ? 'flex-row-reverse' : 'flex-row'}">

//                 <!-- Avatar -->
//                 <div class="w-8 h-8 rounded-full overflow-hidden flex items-center justify-center text-xs bg-gray-200">
//                     ${
//                         rply.profileimg
//                         ? `<img src="${rply.profileimg}" class="w-full h-full object-cover">`
//                         : `<span class="font-semibold text-gray-600">${rply.name.charAt(0)}</span>`
//                     }
//                 </div>

//                 <!-- Message -->
//                 <div class="px-4 py-2 rounded-2xl shadow text-sm
//                     ${isAdmin 
//                         ? 'bg-blue-600 text-white rounded-br-sm' 
//                         : 'bg-gray-100 text-gray-800 rounded-bl-sm'}">

//                     <p class="mb-0">${rply.reply_text}</p>

//                     <div class="mt-1 text-[11px] opacity-70 text-right">
//                         ${time}
//                     </div>
//                 </div>

//             </div>
//         </li>`;
//     });

//     html += `</ul>`;
// }

// $('#taskreplaysec').html(html +from);
// }

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
            }else{
                toastr.error(response.message)
            }
        }
    });
}

/*******************************
 * RENDER CHAT UI
 *******************************/
function renderReplayUi(replay) {

    let html = '';
    let form = `
        
    `;

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

/*******************************
 * SUBMIT REPLY FORM
 *******************************/
$(document).on('submit', '#replyTaskForm', function (e) {
    e.preventDefault();

    let taskId = $('#taskId').val();
    let formData = new FormData(this);
    formData.append('taskId', taskId);

   $('#submitBtn').prop('disabled', true).html(
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
            }else{
                 toastr.error(res.message);
            }
        },
        complete: function () {
            $('#submitBtn').prop('disabled', false).text('Send');
        }
    });
});


// $(document).on('submit', '#replyTaskForm', function (e) {

//     let id =  $('#taskId').val();
//     let webForm = $('#replyTaskForm');
   
//     e.preventDefault();
//     let formData = new FormData(this);
//     formData.append('taskId', id);
//     $('.is-invalid').removeClass('is-invalid');
//     $('.invalid-feedback').empty();
//     $('#submitBtn').prop('disabled', true).html(
//         '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
//     );
//     $.ajax({
//         url : App.getSiteurl()+'task/activity/replay',
//         method:'POST',
//         data: formData,
//         contentType: false,
//         processData: false,
//         success:function(response)
//         { 
//             if(response.success){
//                 toastr.success(response.message);
//                 webForm[0].reset();
//                 loadTask();
//                 renderHistory(id);
//                 toggleHistory();
//             }else{
//                 if(response.errors){
//                     $.each(response.errors,function(field,message)
//                     {
//                         $('#'+ field).addClass('is-invalid');
//                         $('#' + field + '_error').text(message);
//                     })
//                 }else{
//                     toastr.error(response.message);
//                 }
//             }
//         },error: function() {
//             toastr.error('An error occurred while saving Service');
//         },
//         complete: function() {
//             // Re-enable submit button
//             $('#submitBtn').prop('disabled', false).text('Save Branch');
//         }
//     })
// })

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
function showStep(step) {
    // Hide all steps
    $('.step1, .step2').hide();

    // Remove active button style
    $('.modal-action-btn').removeClass('bg-gray-200 text-gray-800');

    // Show selected step
    $('.step' + step).show();
}