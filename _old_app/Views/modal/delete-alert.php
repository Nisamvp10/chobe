  <!-- Modal -->
    <div id="deleteAlertModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Alert</h3>
        <button data-close="deleteAlertModal" class="text-gray-400 hover:text-gray-500">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 6 6 18"></path><path d="m6 6 12 12"></path>
            </svg>
        </button>
        </div>

        <!-- Form -->
        <div class="modal-body">
            <p id="deleteTaskMessage">Are you sure you want to delete this .</p>
            <p id="deleteTaskMessage"> </p>
        </div>
        <div class="modal-footer flex gap-2">
            <button type="button" class="btn btn-secondary" data-close="deleteAlertModal">Cancel</button>
            <button type="dutton" class="btn btn-danger" onclick="confirmDelete(this)" data-id="" id="confirmDelete">Yes</button>
        </div>
    </div>
    </div>