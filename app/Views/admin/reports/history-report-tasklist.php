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
                    <a id="downloadReport"  class="btn btn-primary" data-id="<?=encryptor($id ?? '')?>">
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
              <div class="w-full md:w-48 hidden">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-filter text-gray-400">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    </div>
                    <select id="projectUnitFilter" class="pl-10  pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none">
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
                 <div class="w-full md:w-48 hidden">
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
            
            <div class="w-full md:w-48 hidden">
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
                <div id="clientsTable" class="max-h-[70vh] overflow-y-auto overflow-x-auto custom-scroll-wrapper"></div>
            </div>
            <div id="loadingText" class="text-center flex items-center justify-center gap-2"  style="display:none; padding:10px; font-weight:bold;">
               <i class="bi bi-spinner bi-spin"></i> Loading data...
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
<script src="<?=base_url('public/assets/js/reports.js') ;?>" ></script>
    <script>
         $(document).ready(function() {

            window.loadReports = function (search = '',startDate = '',endDate = '',range = 'today') {
            let filer = $('#filerStatus').val();
            let projectUnitFilter = $('#projectUnitFilter').val();
            let projectFilter = $('#projectFilter').val();

            $.ajax({
                url: "<?= site_url('history-report/list') ?>",
                type: "POST",
                data: {
                    search: search,
                    filter: filer,
                    startDate: startDate,
                    endDate: endDate,
                    prounit: projectUnitFilter,
                    project: projectFilter,
                    range: range,
                    taskId: '<?=encryptor($id ?? '')?>'
                },
                dataType: "json",
                beforeSend: function() {
                    $('#loadingText').show();
                },
                success: function(response) {
                    if (response.status) {
                        renderTable(response);
                    }
                },
                complete: function() {
                    $('#loadingText').hide();
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
        result.result.forEach(function(item) {
           const comments = item.activity_comments || [];
            const commentCount = comments.length;
            const commentId = 'comments_' + item.activity_id;

            let genDate = item.taskDate;

            // 🔹 Generate comments HTML dynamically
            let commentsHtml = '';

            comments.forEach(function(c) {
                commentsHtml += `
                <div class="bg-white rounded-lg p-2 border border-gray-200 hover:border-blue-300 transition-colors">
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex items-center gap-2">
                           <div class="relative rounded-full overflow-hidden flex items-center justify-center w-10 h-10 text-xs border-2 bg-blue-100 border-white">
                                    <span class="text-blue-600 font-medium">${c.comment_by.charAt(0)}</span>
                            </div>
                            <span class="font-medium text-gray-900">${c.comment_by ?? 'User'}</span>
                        </div>
                        <span class="text-xs text-gray-500">${c.comment_date ?? ''}</span>
                    </div>
                    <p class="text-gray-700 text-sm ml-10 mb-2">${c.comment ?? ''}</p>
                </div>
                `;
            });

            html += `
            <div>
                <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden transition-all duration-200 hover:shadow-lg mb-3">

                    <div class="mb-3 p-3">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <p class="text-2xl font-bold text-gray-800 mb-1">${item.activity_title}</p>
                                <p class="text-gray-600 mb-2">${item.activity_description}</p>
                            </div>
                            <div class="px-3 py-1 capitalize rounded-full text-xs font-medium border ${item.activity_status == 'Pending' ? 'bg-amber-100 text-amber-800 border-amber-200' : item.activity_status == 'In_Progress' ? 'bg-blue-100 text-blue-800 border-blue-200' : 'bg-green-100 text-green-800 border-green-200'}">
                                ${item.activity_status}
                            </div>
                        </div>
                        <div class="flex items-center gap-4 text-sm text-gray-500 mb-2"><div class="flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar "><path d="M8 2v4"></path><path d="M16 2v4"></path><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M3 10h18"></path></svg><span>${genDate}</span></div><div class="flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-square "><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg><span>${commentCount} comments</span></div></div>
                        <button onclick="toggleComments('${commentId}',this)" class="flex items-center gap-2 text-blue-600 hover:text-blue-700 font-medium transition-colors"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down "><path d="m6 9 6 6 6-6"></path></svg>View Comments</button>
                    </div>
                    

                    <!-- COMMENTS SECTION -->
                    <div id="${commentId}" class="comment-box border-t border-gray-200 bg-gray-50 p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">
                            Comments (${commentCount})
                        </h4>

                        <div class="space-y-3 max-h-96 overflow-y-auto pr-2">
                            ${commentsHtml || '<p class="text-gray-500">No comments</p>'}
                        </div>
                    </div>

                </div>
            </div>
            `;
        });
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

        let stage = 0;

$('#clientsTable').on('scroll', function () {

    let scrollTop = $(this).scrollTop();
    let height = $(this).innerHeight();
    let scrollHeight = this.scrollHeight;

    if (scrollTop + height >= scrollHeight - 50) {

        stage++;

        // if (stage == 1) loadReports('', '', '', '3days');
        // if (stage == 2) loadReports('', '', '', 'week');
        // if (stage == 3) loadReports('', '', '', 'month');
        // if (stage == 4) loadReports('', '', '', '3month');
        // if (stage == 5) loadReports('', '', '', '6month');
        // if (stage == 6) loadReports('', '', '', '365days');

    }

});
function toggleComments(id,e) {
    const el = document.getElementById(id);
    el.classList.toggle('show');
    if(el.classList.contains('show')){
      $(e).innerText = "Hide Comments"
    }else{
         $(e).textContent ="View Comments";
    }
}

$(document).on('click','#downloadReport',function(){
    let id = $(this).data('id');
    window.location.href = "<?= site_url('history-report/download') ?>" + "/" + id;
})
    </script>
<?= $this->endSection() ?>

