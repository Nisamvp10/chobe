<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
    <!-- titilebar -->
    <div class="flex items-center justify-between">
        <div class="col-lg-12">
            <div class="d-flex gap-2 align-items-center mb-0">
                <h1 class="h3 mb-0"><?= $page ?? '' ?></h1>
                <?php if(haspermission('','create_project_unit')) : ?>
                    <div class="flex justify-end gap-2">
                          <div>
                    <button onclick="openModal()"  class="btn btn-primary" >
                        <i class="bi bi-plus-circle me-1"></i> Add New Project Unit
                    </button>
                </div>
                <div>
                    <button class="btn btn-primary" onclick="openModalbulk()">
                        <i class="bi bi-plus-circle me-1"></i> Bulk  Data Upload 
                    </button>
                </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div><!-- closee titilebar -->
<?= view('modal/projectUnit'); ?>
  



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
                <input type="text" id="searchInput" placeholder="Search Store name " class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
            </div>
            
            <!-- Column 2: Status Dropdown -->
            <div class="w-full md:w-48 hidden">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-filter text-gray-400">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    </div>
                    <select id="filerStatus" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none">
                    <option value="all">Project Unit Type</option>
                          <?php
                          if(!empty($stores)) {
                            foreach($stores as $units) {
                                ?>
                                    <option value="<?= $units['id'] ?>"><?= $units['name'] ?></option>
                                <?php
                            }
                          } ?>
                    </select>
                </div>
            </div>
            </div>
            <!-- table -->
             <div class="overflow-x-auto">
                <div id="projectTable"></div>
            </div>
            <!-- close table -->
</div><!-- body -->
<?= view('modal/unitbulModal'); ?>
<?= $this->endSection(); ?>
<?= $this->section('scripts') ?>
<script src="<?= base_url('public/assets/js/projectunit.js') ?>"></script>
<?= $this->endSection() ?>

