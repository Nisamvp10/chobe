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
                    <select id="projectFilter" name="task" class="pl-10 common-select pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none">
                        <option value="all">Tasks</option>
                       <?php
                        if(!empty($tasksByprojectUnits)){
                            foreach($tasksByprojectUnits as $tasks){
                            ?>
                                <option  value="<?=$tasks->id;?>"><?=$tasks->title;?></option>
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
                <div class="flex-1 relative">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-filter text-gray-400">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    </div>
                    <select id="projectUnitFilter" name="projectunit" class="pl-10 common-select  pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none">
                        <option value="all">Project Units</option>
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
       

    </script>
<?= $this->endSection() ?>

