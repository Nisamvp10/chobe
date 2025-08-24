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
                        <option value="In_Progress">In Progress</option>
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

              window.loadReports = function (search = '', startDate = '', endDate = '') {
                let filer = $('#filerStatus').val();
                let projectUnitFilter = $('#projectUnitFilter').val();

                $.ajax({
                    url: "<?= site_url('reports/list') ?>",
                    type: "GET",
                    data: { search: search,filer:filer ,startDate:startDate,endDate:endDate,prounit:projectUnitFilter},
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            renderTable(response.result);
                        }
                    }
                });
            }

            function renderTable(result){
                let html = '';

                if (result.length === 0) {
                    html += `
                        <div class="text-center py-8">
                            <h3 class="text-lg font-medium text-gray-700">No Clients found</h3>
                            <p class="text-gray-500 mt-1"> <?=(!haspermission('','report') ? :'Try adjusting your search');?></p>
                        </div>
                    `;
                }else{
                    html += `
                        <table class="min-w-full divide-y divide-gray-200 border border-collapse">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project Unit</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Participates</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activity Tasks</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completion</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                    `;
                    result.forEach(report => {

                        const totalTasks = report.total_activities ? report.total_activities :0;
                        const completedTasks = report.completed_activities ? report.completed_activities : 0;
                        const percent = totalTasks > 0 ? Math.round((completedTasks / totalTasks) * 100): 0;
              
               // let joinedDate = new Date(client.join_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });

                        html += `
                            <tr class="hover:bg-gray-50">
                             <td class="px-2 py-2 whitespace-nowrap">
                                
                                    <div class="flex items-center">
                                        
                                        <div class="text-sm font-medium text-gray-900">${report.store ?? '-'}</div>
                                    </div>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                
                                    <div class="flex items-center">
                                        
                                        <div class="text-sm font-medium text-gray-900">${report.title}</div>
                                    </div>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">Master Task Participates ${report.total_task_staff}</div>
                                    <div class="text-sm text-gray-900">Activity Task Participates ${report.total_activity_staff ?? 0}</div>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">${report.total_activities ?? 0}</div>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">${report.master_task_status}</div>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                     <div class="d-flex align-items-center mb-2">
                                        <div class="w-full justify-content-between itm-align-end bg-gray-200 rounded-full h-2">
                                            <div class="h-2 rounded-full transition-all duration-500 ${(percent < 50 ? 'bg-red-500' : (percent > 80 ? 'bg-green-500' :'bg-yellow-500'))} " style="width: ${percent}%"></div> 
                                        </div>
                                        <span class="text-xs text-gray-500 text-gray-900">  ${report.completed_activities ?? 0} / ${report.total_activities ?? 0} ${percent}%</span>
                                    </div>
                                </td>

                            </tr>
                        `;
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
            $('#filerStatus,#projectUnitFilter').on('change',function(){
                let value = $('#searchInput').val();
                loadReports(value);
            })
        });
    </script>
<?= $this->endSection() ?>

