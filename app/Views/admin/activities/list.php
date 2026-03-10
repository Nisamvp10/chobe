<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

    <!-- titilebar -->
    <div class="flex items-center justify-between">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center mb-0">
                <h1 class="h3 mb-0"><?= $page ?? '' ?></h1>
                <?php
                if(haspermission(session('user_data')['role'],'create_task')) { ?>
                <div>
                    <a  class="btn btn-primary" onclick="openModal()">
                        <i class="bi bi-plus-circle me-1"></i> Add New Activity
                    </a>
                </div>
                <?php } ?>
            </div>
        </div>
    </div><!-- closee titilebar -->

    <!-- body -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden p-4">
        <div class="flex flex-col md:flex-row gap-4 mb-2">
    
            <!-- Column 1: Search Input -->
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search text-gray-400">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.3-4.3"></path>
                </svg>
                </div>
                <input type="text" id="searchInput" placeholder="Search Task title..." class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
            </div>
             <div class="flex-1 relative d-none">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
               <i class="bi bi-calendar"></i>
                </div>
                <input type="text" id="filterDate" placeholder="Filter by date" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
            </div>
                

            <!-- Column 2: Status Dropdown -->
            <div class="w-full md:w-48 hidden">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-filter text-gray-400">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    </div>
                    <select id="taskactivityFilterStatus" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none">
                         <option value="all">All</option>
                        <option value="Pending">Pending</option>
                        <option  value="In_Progress">In Progress</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>
            </div>
            </div>
            <!-- table -->
             <div class="overflow-x-auto pt-1" id="tasktbl" data-task="" >
                <div class="flex items-center justify-end  m-1 pb-1 ">
                    <div><span class="px-2 py-2 hover:bg-gray-100 hover:cursor-pointer border border-gray-300 rounded-2 " onclick="multipleDelete()"><i class="bi bi-trash"></i></span></div>
                </div>
                
                <div class="mt-2" id="taskTable"></div>
            </div>
            <!-- close table -->
</div><!-- body -->

<!-- modal -->

<?= view('modal/masterActivityModal');?>
<?= view('modal/delete-alert');?>

<!-- close Modal -->
<?= $this->endSection(); ?>
<?= $this->section('scripts') ?>


<script src="<?=base_url('public/assets/js/activities.js') ;?>" ></script>

<script>
    allactivities();
$('#taskactivityFilterStatus').on('change', function () {
    allactivities();
})
$('#searchInput').on('input',function(){
        let value = $(this).val();
        allactivities(value);
    })
function allactivities(search = '') {

    let activityId = $('#tasktbl').data('task');
    let searchInput = search;
    let filterStatus = $('#taskactivityFilterStatus').val();
    let filterDate = $('#filterDate').val();

    $.ajax({
        url: '<?= base_url('task/all-activities') ?>',
        type: 'POST',
        data: {
            taskId: activityId,
            searchInput: searchInput,
            filter: filterStatus,
            filterDate: filterDate
        },
        dataType: 'json',
        success: function(res) {
            if(res.success) {
                allDataactivity = res.task;
                renderTableactivity();
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', status, error);
        }
    });
}
let currentPage = 1;
let rowsPerPage = 20;
let allDataactivity = [];
    function renderTableactivity() {
        let start = (currentPage - 1) * rowsPerPage;
        let end = start + rowsPerPage;

        let pageData = allDataactivity.slice(start, end);

        let tableHtml = `
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><input type="checkbox"  class=" selectAll w-[20px] h-[20px]"> S/O </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activity Task</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                         <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
        `;

        pageData.forEach(function(task ,indx) {
            
            tableHtml += `
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">${start + indx+1} <input type="checkbox" name="activity_id[]" class="task-checkbox" value="${task.id}"></td>
                    <td class="px-6 py-4 whitespace-nowrap flex items-center gap-2"><div onclick="copyid(${task.id})" class=" w-[40px] h-[40px] bg-primary p-2 rounded-full flex items-center justify-center text-white">${task.id}</div> ${task.activity_title}</td>
                    <td class="px-6 py-4 ">${task.activity_description}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${task.task_title}</td>
                     <td class="px-6 py-4 whitespace-nowrap flex items-center gap-1"><a onclick="openModal(${task.id})"  class="text-blue-600 hover:text-blue-800 mr-3"><i class="bi bi-pencil text-blue-600 hover:text-blue-800 mr-3 border rounded-2 px-2 py-1 hover:cursor-pointer hover:bg-blue-100"></i></a><a  data-id="${task.id}" class="text-red-600 hover:text-red-800 mr-3" onclick="openDeleteModal(this,'deleteAlertModal')" data-message="Are you sure you want to delete ?"><i class="bi bi-trash text-red-600 hover:text-red-800 mr-3 border rounded-2 px-2 py-1 hover:cursor-pointer hover:bg-red-100"></i></a></td>
                </tr>
            `;
        });

        tableHtml +=`</tbody>
        </table></div>`;

        let totalPages = Math.ceil( allDataactivity.length / rowsPerPage);
        tableHtml += `
            <div class="flex justify-between items-center mt-4">
                <div>
                    <label class="mr-2">Rows per page:</label>
                    <select onchange="changeRowsPerPage(this.value)" class="px-2 py-1 border rounded">
                
                        <option value="10"  ${rowsPerPage == 10 ? 'selected' : ''}>10</option>
                        <option value="20"  ${rowsPerPage == 20 ? 'selected' : ''}>20</option>
                        <option value="50"  ${rowsPerPage == 50 ? 'selected' : ''}>50</option>
                        <option value="100" ${rowsPerPage == 100 ? 'selected' : ''}>100</option>
                    </select>
                </div>
                <div>
                    <button onclick="prevPage()" ${currentPage === 1 ? 'disabled' : ''} class="px-3 py-1 bg-gray-200 rounded disabled:opacity-50">Prev</button>
                    <span class="mx-2">Page ${currentPage} of ${totalPages}</span>
                    <button onclick="nextPage(${totalPages})" ${currentPage === totalPages ? 'disabled' : ''} class="px-3 py-1 bg-gray-200 rounded disabled:opacity-50">Next</button>
                </div>
            </div>`;

        $('#taskTable').html(tableHtml);
    }


// Change rows per page
function changeRowsPerPage(value) {
    rowsPerPage = parseInt(value);
    currentPage = 1;
    renderTableactivity(allDataactivity);
}

// Pagination functions
function prevPage() {
    if (currentPage > 1) {
        currentPage--;
        renderTableactivity(allDataactivity);
    }
}
function nextPage(totalPages) {
    if (currentPage < totalPages) {
        currentPage++;
        renderTableactivity(allDataactivity);
    }
}
document.addEventListener('click',function(e){
    // select all checkbox and unselect all checkbox
    if(e.target.classList.contains('selectAll')){
        let checked = e.target.checked;
        if(checked){
            $('.task-checkbox').prop('checked',true);
        }else{
            $('.task-checkbox').prop('checked',false);
        }
    }
    // select individual checkbox
    if(e.target.classList.contains('task-checkbox')){
        let checked = e.target.checked;
        if(checked){
            
        }else{
            $('.task-checkbox').prop('checked',false);
        }
    }
})



let selectedTaskId = null;

// function deleteThis(e){
//     let taskTitle ='';
//     selectedTaskId = $(e).data('id'); 
//    let message = $(e).data('message')
//     $('#deleteTaskMessage').text(message);
// };

// Confirm delete click
//$('#confirmDelete').on('click', function (e) {
function confirmDelete(e){
   
    //select data-id value from confirmDelete button
    selectedTaskId = $(e).data('id');
    if (selectedTaskId) {
        $('#confirmDelete').prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Deleting...'
        );

        $.ajax({
            url: App.getSiteurl()+`activity/delete/${selectedTaskId}`, 
            type: 'POST',
            dataType:'json',
            data: { _method: 'DELETE' }, // if using method spoofing in CI4
            success: function (response) {
               if(response.success) {
                 setTimeout(function () {
                    $('#confirmDelete').prop('disabled', false).html(
                        'Delete'
                    );
                    allactivities();
                    selectedTaskId = null;
                    toggleCustomModal('deleteAlertModal',false);
                }, 2000);
                
                toastr.success(response.message);
               }else{
                 $('#confirmDelete').prop('disabled', false).html(
                        'Yes'
                    );
                toastr.error(response.message);
               }
               
            },
            error: function () {
                toastr.error('Error deleting task');
            }
        });
    }
}
//multiple delete
function multipleDelete(){
    if(confirm('Are you sure you want to delete?')){
        
        if($('.task-checkbox:checked').length == 0){
            toastr.error('Please select at least one task to delete');
            return;
        }
        let selectedTaskIds = [];
        $('.task-checkbox:checked').each(function() {
            selectedTaskIds.push($(this).val());
        });
        if (selectedTaskIds.length > 0) {
            $('#confirmDelete').prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Deleting...'
            );
            $.ajax({
                url: App.getSiteurl()+`activity/multiple-delete`, 
                type: 'POST',
                dataType:'json',
                data: { _method: 'DELETE',activityIds: selectedTaskIds }, // if using method spoofing in CI4
                success: function (response) {
                if(response.success) {
                    setTimeout(function () {
                        $('#confirmDelete').prop('disabled', false).html('Delete');
                        allactivities();
                        selectedTaskId = null;
                        toggleCustomModal('deleteAlertModal',false);
                    }, 2000);
                    
                    toastr.success(response.message);
                }else{
                    $('#confirmDelete').prop('disabled', false).html('Yes');
                    toastr.error(response.message);
                }
                
                },
                error: function () {
                    toastr.error('Error deleting task');
                }
            });
        }
    }
}
function copyid(id) {
   navigator.clipboard.writeText(id).then(function () {

        toastr.success("Copied!");

    }).catch(function () {

        alert("Copy failed");

    });
        
}
</script>



<?= $this->endSection() ;?>