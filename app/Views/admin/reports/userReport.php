<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
    <!-- titilebar -->
    <div class="flex items-center justify-between">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center mb-0">
                <h1 class="h3 mb-0"><?= $page ?? '' ?></h1>
                <?php
                if(haspermission('','report_download')) { ?>
                <div>
                    <a id="downloadReport"  class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Download Report
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
              <div class="flex-1 relative">
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
                    <select id="projectUnitFilter" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none">
                        <option value="all">All</option>
                       <?php
                        if(!empty($projectUnits)){
                            foreach($projectUnits as $unit){
                            ?>
                                <option  value="<?=$unit['id'];?>"><?=$unit['store'];?></option>
                            <?php 
                            } 
                        } ?>

                    </select>
                </div>
            </div>
            <!-- project Filter -->
                 <div class="w-full md:w-48">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-filter text-gray-400">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    </div>
                    <select id="projectFilter" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none">
                        <option value="all">All</option>
                       <?php
                        if(!empty($projectsList)){
                            foreach($projectsList as $project){
                            ?>
                                <option  value="<?=$project['id'];?>"><?=$project['project'];?></option>
                            <?php 
                            } 
                        } ?>

                    </select>
                </div>
            </div>
            
            <div class="w-full md:w-48">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-filter text-gray-400">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    </div>
                    <select id="filerStatus" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none">
                        <option value="all">All</option>
                        <option value="Pending">Pending</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>
            </div>
            </div>
            <!-- table -->
             <div class="overflow-x-auto">
                <div id="clientsTable"></div>
            </div>
            <!-- close table -->
</div><!-- body -->
<?= view('modal/commentModal') ;?>

<?= $this->endSection(); ?>


<?= $this->section('scripts') ?>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Moment.js (must be before daterangepicker.js) -->
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

<!-- Date Range Picker -->
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script src="<?=base_url('public/assets/js/userReport.js') ;?>" ></script>
    <script>
        $(document).ready(function() {

              window.loadReports = function (search = '', startDate = '', endDate = '') {
                
                let filer = $('#filerStatus').val();
                let projectUnitFilter = $('#projectUnitFilter').val();
                let projectFilter = $('#projectFilter').val();

                $.ajax({
                    url: "<?= site_url('user-report/userReportList') ?>",
                    type: "GET",
                    data: { search: search,filter:filer ,startDate:startDate,endDate:endDate,prounit:projectUnitFilter,project:projectFilter},
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            renderTable(response);
                        }
                    }
                });
            }

            
function renderTable(result){

    let html = '';

    if (result.result.length === 0) {
        html += `
            <div class="text-center py-8">
                <h3 class="text-lg font-medium text-gray-700">No Data Found</h3>
            </div>
        `;
    } else {

        html += `
        <table class="min-w-full divide-y divide-gray-200 border border-collapse">
            <thead class="bg-gray-100">
                <tr>`;

        result.headers.forEach(header => {
            html += `
                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                    ${header}
                </th>`;
        });

        html += `
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
        `;

        result.result.forEach((row, rowIndex) => {

            html += `<tr class="hover:bg-gray-50">`;

            row.forEach((col, colIndex) => {

                if(colIndex < 8){

                    html += `
                        <td class="px-4 py-2 text-sm text-gray-900">
                            ${col ?? ''}
                        </td>`;

                } else {

                    let commentText = col?.comment ?? 'Nill';
                    let activityId   = col?.activityId ?? '';
                    let taskId       = col?.taskId ?? '';

                    html += `
                        <td class="px-4 py-2 text-sm text-gray-900">
                            <div class="editable-cell">

                                <span data-activity-id="${activityId}" data-task-id="${taskId}" data-commentText="${commentText === 'Nill' ? '' : commentText}" class="${activityId ? 'comment-text' : ''} cursor-pointer hover:text-blue-600">
                                    ${commentText}
                                </span>
                            </div>
                        </td>`;
                }

            });

            html += `</tr>`;
        });

        html += `</tbody></table>`;
    }

    $('#clientsTable').html(html);
}


            loadReports();

            $('#searchInput').on('input',function(){
                let value = $(this).val();
                loadReports(value);
            })
            $('#filerStatus,#projectUnitFilter,#projectFilter').on('change',function(){
                let value = $('#searchInput').val();
                loadReports(value);
            })
        });

        // Click text → show input





// Save edit
$('#commentForm').on('submit',function(e){
    e.preventDefault();
    formData = new FormData(this);
    let taskId = formData.get('taskId');
    let activityId = formData.get('activityId');
    let comment = formData.get('comment');
    let webForm = $('#commentForm');

    if(!comment){
        toastr.error('Comment is required');
        return;
    }

    $.ajax({
            url: App.getSiteurl() + 'activity/save-comment',
            method: 'POST',
            data: {
                taskId: taskId,
                activityId: activityId,
                comment: comment
            },
            dataType: 'json',
            success: function (res) {

                if (res.success) {
                toastr.success(res.message);
                loadReports();
                webForm[0].reset();

                } else {
                    toastr.error('Failed to save comment');
                }
            },
            error: function () {
                alert('Server error');
            },
            complete: function () {
                submitBtn.disabled = false;
            }
        });

    });


    </script>
<?= $this->endSection() ?>

