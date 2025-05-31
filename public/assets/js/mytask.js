
 //$(document).ready(function() {
    
    function loadTask(search = '') {

        let filer = $('#filerStatus').val();
        $.ajax({

            url: App.getSiteurl()+'task/my-task',
            type: "GET",
            data: { search: search,filer:filer,list:1 },
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
            taskHTML += `
                <div class="text-center py-8">
                    <h3 class="text-lg font-medium text-gray-700">No Clients found</h3>
                    <p class="text-gray-500 mt-1"> <?=(!haspermission('','view_clients') ? :'Try adjusting your search');?></p>
                </div>`;
        }else{
            
           
            tasks.forEach(task => {
        
            const dueDate = new Date(task.overdue_date);
            const today = new Date();
            dueDate.setHours(0, 0, 0, 0);
            today.setHours(0, 0, 0, 0);

            let duedateText = dueDate.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
            let dueClass = dueDate < today ? 'text-red-600' : 'text-gray-900';
            

            
                var priority = (task.priority =='High' ? 'px-2 py-1 rounded-full text-xs font-medium text-white flex-shrink-0 bg-danger' : (task.priority == "Low" ? 'px-2 py-1 rounded-full text-xs font-medium text-white flex-shrink-0 bg-blue-500' : 'px-2 py-1 rounded-full text-xs font-medium text-white flex-shrink-0 bg-yellow-500'));
                var status = (task.status =='Pending' ? 'bg-task-medium text-white' : (task.priority == "In_Progress" ? 'bg-blue-500 text-yellow-800' : (task.priority == "Completed" ? 'bg-green-500' : 'bg-green-500 text-green-800' ) ));
                const progress = task.progress;


                // 
                
             const taskHTML = `
        <div class="bg-white draggable-task rounded-lg shadow-sm p-4 cursor-pointer hover:shadow-md transition-shadow duration-200 border-l-4 border-orange-500 draggable-task" 
            draggable="true"
            data-id="${task.id}" 
            data-status="${task.status}"
            onclick="openTaskModal(this)"
            data-title="${task.title}"
            data-desc="${task.description}"
            data-branch="${task.branch_name}"
            data-status="${task.status}"
            data-progress="${progress}%"
            data-date="${duedateText}"
            data-priority="${task.priority}"
            data-duration="${task.duration}"
            data-profiles='${JSON.stringify(task.users)}'
            data-duedate='${task.overdue_date}'
            data-store='${task.storeId}'
            data-progressbar="${task.progress}"
            data-cls="${priority}"
           
            >
            
            <div class="flex justify-between items-start mb-2">
                <h3 class="font-medium text-gray-800 truncate flex-1 text-capitalize">${task.title}</h3>
                <span class="px-2 py-1 rounded-full text-xs font-medium text-orange-800 ml-2 flex-shrink-0 ${priority}">${task.priority}</span>
            </div>
            <p class="text-sm text-gray-600 mb-3 line-clamp-2">${task.description}</p>
            <div class="text-xs text-gray-500 mb-3">Branch: <span class="font-medium">${task.branch_name}</span></div>
            <div >
            <div class="d-flex align-items-center mb-2">
                <div class="w-full justify-content-between itm-align-end bg-gray-200 rounded-full h-2">
                    <div class="h-2 rounded-full transition-all duration-500 ${(task.progress  < 50 ? 'bg-red-500' : (task.progress > 80 ? 'bg-green-500' :'bg-yellow-500'))} " style="width: ${progress}%"></div> 
                </div>
                <span class="text-xs text-gray-500 text-gray-900">${progress}%</span>
            </div>
            </div>
            <div class="flex justify-between items-center">
                <div class="flex -space-x-2 profile-stack ">
                    ${task.users.map(user => `
                        <div class="relative rounded-full overflow-hidden flex items-center justify-center w-6 h-6 text-xs border-2 border-white">
                            <img src="${user.img}" alt="${user.staffName}" class="w-full h-full object-cover">
                        </div>
                    `).join('')}
                </div>
                <span class="text-xs text-gray-500 ${dueClass}">${duedateText}</span>
            </div>
        </div>
    `;
            
            

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

                if (task.status === 'Pending') {
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

        
function openTaskModal(el) {

    const modal = document.getElementById('taskModal');
    const progressEl = document.getElementById('progressIndicator');
    
    const progressLabel = document.getElementById('progressLabel');
    const taskId = document.getElementById('taskId');
    modal.classList.remove('hidden');
    // Fill modal fields from data attributes
    let gettaskId =  modal.querySelector('.modal-title').textContent = el.dataset.id;
    taskId.value = gettaskId;
    modal.querySelector('.modal-title').textContent = el.dataset.title;
    modal.querySelector('.modal-desc').textContent = el.dataset.desc;
    modal.querySelector('.modal-branch').textContent = el.dataset.branch;
    let status = modal.querySelector('.modal-status').textContent = el.dataset.status;
    modal.querySelector('.modal-date').textContent = el.dataset.date;
    let progress = modal.querySelector('.modal-progress-bar').style.width = el.dataset.progress;

    let progressbar = el.dataset.progressbar;
      
    //console.log(progressbar)
    let priority = modal.querySelector('.modal-priority').textContent = el.dataset.priority;
    modal.querySelector('.modal-duration').textContent = el.dataset.duration;
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
                avatar.className = 'relative rounded-full overflow-hidden flex items-center justify-center w-6 h-6 text-xs';
                avatar.innerHTML = `<img src="${user.img}" alt="" class="w-full h-full object-cover">`;

                const nameSpan = document.createElement('span');
                nameSpan.className = 'text-sm text-gray-700';
                nameSpan.textContent = user.staffName;

                wrapper.appendChild(avatar);
                wrapper.appendChild(nameSpan);

                profilesContainer.appendChild(wrapper);
            });
        } catch (e) {
            console.error('Invalid profile data:', e);
        }

        renderHistory(gettaskId)
}

function closeTaskModal() {
  document.getElementById('taskModal').classList.add('hidden');
}

function closeTaskModal(event) {
  if (!event || event.target.id === "taskModal" || event.target.innerText === "✕") {
    document.getElementById("taskModal").classList.add("hidden");
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


function renderHistory(id) {
    $.ajax({
        url: App.getSiteurl()+'task-replays',
        type: "POST",
        data: { taskId: id},
        dataType: "json",
        success: function(response) {
           
            if (response.success) {
                renderReplayUi(response.replay);
            }
        }
    });
}
function renderReplayUi(replay) {
    let html ='';
    if(replay.length ===0) {
        html = `<div class="text-center py-8">
                    <h3 class="text-lg font-medium text-gray-700">No Replay yet</h3>
                </div>`;
    }else{
         html +=` <ul class="-mb-8">`
        replay.forEach(rply=>{
            html +=` <li>
                        <div class="relative pb-8">
                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                            <div class="relative flex space-x-3"><div class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 transition-colors duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-square w-4 h-4 text-green-500"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                            </div>
                            <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                <div class="flex-1">
                                    <div class="space-y-2">
                                        <p class="text-sm text-gray-400 font-medium">${rply.reply_text} </p>
                                    </div>
                                </div>
                                <div class="whitespace-nowrap text-right text-sm text-gray-500">
                                    <div class="flex items-center space-x-2">
                                        <div class="relative rounded-full overflow-hidden flex items-center justify-center w-6 h-6 text-xs ">
                                            <img src="${rply.profileimg}" alt="John Doe" class="w-full h-full object-cover">
                                        </div>
                                            <time datetime="2025-05-30T20:48:12.577Z" class="text-gray-500">5/31/2025, 2:18:12 AM</time>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>`
        })
        html += '</ul>'
    }
    $('#taskreplaysec').html(html);
}
$('#replyTaskForm').on('submit', function(e) {
     let id =  $('#taskId').val();
    let webForm = $('#replyTaskForm');
   
    e.preventDefault();
    let formData = new FormData(this);
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').empty();
    $('#submitBtn').prop('disabled', true).html(
        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
    );
    $.ajax({
        url : App.getSiteurl()+'task/replay',
        method:'POST',
        data: formData,
        contentType: false,
        processData: false,
        success:function(response)
        { 
            if(response.success){
                toastr.success(response.message);
                webForm[0].reset();
                loadTask();
                renderHistory(id);
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

