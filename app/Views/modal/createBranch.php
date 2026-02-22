  <!-- Modal -->
    <div id="categoryModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Add New Project</h3>
        <button onclick="closeModal()" class="text-gray-400 hover:text-gray-500">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 6 6 18"></path><path d="m6 6 12 12"></path>
            </svg>
        </button>
        </div>

        <!-- Form -->
        <form id="projectForm" method="post" action="<?= base_url('project/save') ?>">
            <?= csrf_field() ;?>
            <div class="mb-4">
                <label for="categoryName" class="block text-sm font-medium text-gray-700 mb-1">Project Name</label>
                <input type="text" id="project" name="project" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter category name" required>
                 <div class="invalid-feedback" id="project_error"></div>
            </div>
            <div class="mb-4">
                <label for="client" class="block text-sm font-medium text-gray-700 mb-1">Client</label>
                <select id="client" name="client" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">Select Client</option>
                    <?php 
                    if(!empty($clients)){
                        foreach($clients as $client): ?>
                            <option value="<?= $client['id'] ?>"><?= $client['name'] ?></option>
                        <?php endforeach; 
                    }?>
                </select>
                 <div class="invalid-feedback" id="client_error"></div>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                <button id="submitBtn" class="bg-blue-500 hover:bg-blue-400 text-white px-4 py-2 rounded-2 flex items-center transition-colors"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-save mr-1"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>Save</button>
            </div>
        </form>
    </div>
    </div>