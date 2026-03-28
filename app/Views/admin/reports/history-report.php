<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
    <!-- titilebar -->
    <div class="flex items-center justify-between">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center mb-0">
                <h1 class="h3 mb-0"><?= $page ?? '' ?></h1>
         
            </div>
        </div>
    </div><!-- closee titilebar -->

    <!-- body -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden p-4">
        <form action="<?= base_url('history-report/history-report-tasklist') ?>" method="get">

        <div class="flex flex-col md:flex-row gap-4 mb-6">
    
            <!-- Column 1: Search Input -->
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-filter text-gray-400">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    </div>
                    <select id="projectFilter" name="task" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none">
                        <option value="all">All</option>
                       <?php
                        if(!empty($tasksByprojectUnits)){
                            foreach($tasksByprojectUnits as $tasks){
                            ?>
                                <option  value="<?=$tasks->created_from_template;?>"><?=$tasks->title;?></option>
                            <?php 
                            } 
                        } ?>

                    </select>
            </div>
              <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="bi bi-calendar"></i>
                </div>
                <input type="text" id="filterDate" name="date" placeholder="Filter by date" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                <!-- set a submit button view report -->
                </div>
                          <div class="w-full md:w-48 ">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-filter text-gray-400">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    </div>
                    <select id="projectUnitFilter" name="projectunit" class="pl-10  pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none">
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

                 <div class="flex-1 relative">
                    <button type="submit" class="btn btn-primary">View Report</button>
                </div>
             </div>
            </form>


           
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
          loadReports();


            let tableData = [];
            let rowsPerPage = 15;
            let currentPage = 1;

         function loadReports(searchIn = '',startDate = '',endDate = '',range = 'today') {
            search =  $('#searchInput').val();

            let filer = $('#filerStatus').val();
            let projectUnitFilter = $('#projectUnitFilter').val();
            let projectFilter = $('#projectFilter').val();

            $.ajax({
                url: "<?= site_url('reports/tasklist') ?>",
                type: "POST",
                data: {
                    search: search,
                    startDate: startDate,
                    endDate: endDate,
                },
                dataType: "json",
                beforeSend: function() {
                    $('#loadingText').show();
                },
                success: function(response) {
                    if (response.success) {
                        tableData = response.data;
                        renderTaskListTable();
                    }
                },
                complete: function() {
                    $('#loadingText').hide();
                }
            });
    }

function renderTaskListTable(){

    let html = '';
    let startIndex = (currentPage - 1) * rowsPerPage;
    let endIndex = startIndex + rowsPerPage;
    let paginatedData = tableData.slice(startIndex, endIndex);

    if (tableData.length === 0) {
        html += `
            <div class="text-center py-8">
                <h3 class="text-lg font-medium text-gray-700">No Data Found</h3>
            </div>
        `;
    } else {

        html += `
         <div class="custom-scrollbar">
            <div class="custom-thumb" id="scrollThumb"></div>
        </div>
        
        <table id="tableContainer" class="min-w-full border border-collapse">
            <button onclick="toggleFullscreen()" 
            class="absolute right-2 top-2 bg-blue-600 text-white px-3 py-1 rounded text-sm z-20 fullBtn">
            <i class="bi bi-fullscreen"></i>
        </button>

            <thead class="bg-gray-100 sticky top-0 z-10">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SL No</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task Title</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>

        
            </thead>
            <tbody class="bg-white divide- divide-gray-200 ">
        `;

        paginatedData.forEach((row, rowIndex) => {
            html += `<tr class="hover:bg-gray-50 m-2">`;

            html += `
                <td class="px-2 py-2 whitespace-nowrap">
                    ${startIndex + rowIndex + 1}
                </td>
                <td class="px-2 py-2 whitespace-nowrap">
                    ${row.title}
                </td>
                <td class="px-2 py-2 whitespace-nowrap">
                    ${row.task_gen_date}
                </td>
                <td class="px-2 py-2 whitespace-nowrap">
                    <a href="<?=base_url('history-report-') ?>${row.url}" class="text-blue-600 hover:text-blue-800 cursor-pointer p-2 rounded text-center bg-blue-100 m-2" ><i class="bi bi-eye"></i></a>
                </td>`;

            html += `</tr>`;
        });

        html += `</tbody></table>`;
       let totalPages = Math.ceil(tableData.length / rowsPerPage);
        html += `
            <div class="flex justify-between items-center mt-4">
                <div>
                    <label class="mr-2">Rows per page:</label>
                    <select onchange="changeRowsPerPage(this.value)" class="px-2 py-1 border rounded">
                
                        <option value="15"  ${rowsPerPage == 15 ? 'selected' : ''}>15</option>
                        <option value="25"  ${rowsPerPage == 25 ? 'selected' : ''}>25</option>
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
    }

    $('#clientsTable').html(html);
}

function changeRowsPerPage(value) {
    rowsPerPage = parseInt(value);
    currentPage = 1;
    renderTaskListTable();
}

// Pagination functions
function prevPage() {
    if (currentPage > 1) {
        currentPage--;
        renderTaskListTable();
    }
}
function nextPage(totalPages) {
    if (currentPage < totalPages) {
        currentPage++;
        renderTaskListTable();
    }
}



$('#searchInput').on('input',function(){
    let value = $(this).val();
    loadReports(value);
})
$('#filerStatus,#projectUnitFilter,#projectFilter').on('change',function(){
    let value = $('#searchInput').val();
    loadReports(value);
})
    


    </script>
<?= $this->endSection() ?>

