<?= $this->extend('layout/main') ?>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<?= $this->section('content') ?>

<!-- titilebar -->
<div class="flex items-center justify-between">
    <div class="col-lg-12">
        <div class="d-flex justify-content-between align-items-center mb-0">
            <h1 class="h3 mb-0"><?= $page ?? '' ?></h1>
            <?php
            if (haspermission(session('user_data')['role'], 'create_task')) { ?>
                <div>
                    <a href="<?= base_url('task/create-master-task') ?>" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Add New Master Task
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
    </div>
    <!-- table -->
    <div class="overflow-x-auto" id="masterTbl">
        <div class="flex h-full gap-6 p-0">

        </div>
        <div id="masterTaskTableBody"></div>
    </div>
    <!-- close table -->
</div><!-- body -->

<!-- modal -->

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="deleteTaskMessage">Are you sure you want to delete this Task? This action cannot be undone.</p>
                <p id="deleteTaskMessage">This is Demo version </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="dutton" class="btn btn-danger" id="confirmDeleteTask">Delete</button>
            </div>
        </div>
    </div>
</div>


<!-- open -->
<!-- close screen -->
<!-- close Modal -->
<?= view('modal/masterTaskModal') ?>
<?= $this->endSection(); ?>
<?= $this->section('scripts') ?>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Moment.js (must be before daterangepicker.js) -->
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

<!-- Date Range Picker -->
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />


<script src="<?= base_url('public/assets/js/mastertasklist.js') ?>"></script>
<!-- <script src="<?= base_url('public/assets/js/task.js'); ?>" ></script> -->


<?= $this->endSection(); ?>