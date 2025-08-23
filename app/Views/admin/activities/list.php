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
                    <a href="<?= base_url('task/create') ?>" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#activities">
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
            <div class="w-full md:w-48">
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

    function renderTable(tasks) {
        let tableHtml = `
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Master Task </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activity Task</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
        `;

        tasks.forEach(task => {
             progressBar = ` <div class="d-flex align-items-center mb-2">
                <div class="w-full justify-content-between itm-align-end bg-gray-200 rounded-full h-2">
                    <div class="h-2 rounded-full transition-all duration-500 ${(task.progress  < 50 ? 'bg-red-500' : (task.progress > 80 ? 'bg-green-500' :'bg-yellow-500'))} " style="width: ${task.progress}%"></div> 
                </div>
                <span class="text-xs text-gray-500 text-gray-900">  ${task.progress}%</span>
            </div>`;
            tableHtml += `
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">${task.title}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${task.activity_title}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${task.status}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${task.priority}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${progressBar}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${task.created_at}</td>
                </tr>
            `;
        });

        tableHtml += `
                </tbody>
            </table>
        `;

        $('#taskTable').html(tableHtml);
    }
}


</script>



<?= $this->endSection() ;?>