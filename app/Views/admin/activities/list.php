<?= $this->extend('layout/main') ?>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

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
        <div class="flex flex-col md:flex-row gap-4 mb-6">
    
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
             <div class="overflow-x-auto" id="tasktbl" data-task="" >
                
                <div id="taskTable"></div>
            </div>
            <!-- close table -->
</div><!-- body -->

<!-- modal -->

<!-- Modal -->

<?= view('modal/masterActivityModal');?>

<!-- close Modal -->
<?= $this->endSection(); ?>
<?= $this->section('scripts') ?>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Moment.js (must be before daterangepicker.js) -->
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

<!-- Date Range Picker -->
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
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
        success: function(response) {
            if(response.success) {
                renderTable(response.task);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', status, error);
        }
    });
}
let currentPage = 1;
let rowsPerPage = 20;
let allData = [];
    function renderTable(tasks) {
        allData = tasks;
        let start = (currentPage -1) * rowsPerPage;
        let end = start + rowsPerPage;
       // let pagination =  tasks.slice(start, end);
        let pagination = allData.slice(start, end);
        let tableHtml = `
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">S/O </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activity Task</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                         <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
        `;

        pagination.forEach(function(task ,indx) {
            
            tableHtml += `
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">${start + indx+1}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${task.activity_title}</td>
                    <td class="px-6 py-4 ">${task.activity_description}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${task.task_title}</td>
                     <td class="px-6 py-4 whitespace-nowrap"><a onclick="openModal(${task.id})"  class="text-blue-600 hover:text-blue-800 mr-3">Edit</a></td>
                </tr>
            `;
        });

        tableHtml +=`</tbody>
        </table></div>`;

        let totalPages = Math.ceil( tasks.length / rowsPerPage);
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
    renderTable(allData);
}

// Pagination functions
function prevPage() {
    if (currentPage > 1) {
        currentPage--;
        renderTable(allData);
    }
}
function nextPage(totalPages) {
    if (currentPage < totalPages) {
        currentPage++;
        renderTable(allData);
    }
}

</script>



<?= $this->endSection() ;?>