<!-- Banner Modal -->
<div id="bulkUnit" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 wrapModal">
  <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[100vh] min-h-[80vh] overflow-y-auto">
    <div class="flex items-center justify-between p-6 border-b border-gray-200">
      <h2 class="text-2xl font-bold text-gray-900 head"></h2>
      <button data-close="bulkUnit" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
        ✕
      </button>
    </div>
    <div class="p-6">
         
        <form id="unitBulkForm" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="grid grid-cols-1 md:grid-cols-1 gap-6 border border-dotted text-center rounded-2 p-3">
                    <div class="space-y-4 bulkCard mb-3">
                        <div>
                            <label class="block cursor-pointer">
                                <!-- Hidden file input -->
                                <input type="file" id="staff_excel" name="staff_excel" accept=".xlsx,.xls" class="hidden">

                                <!-- Icon -->
                                <div class="mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-upload h-12 w-12 text-gray-400 mx-auto mb-4">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="17 8 12 3 7 8"></polyline>
                                        <line x1="12" x2="12" y1="3" y2="15"></line>
                                    </svg>
                                </div>

                                <!-- Title -->
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Upload Sales Data</h3>

                                <!-- Trigger Button -->
                                <button type="button" id="chooseFileBtn"
                                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 bg-primary text-primary bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white h-10 px-4 py-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-upload h-4 w-4 mr-2">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="17 8 12 3 7 8"></polyline>
                                        <line x1="12" x2="12" y1="3" y2="15"></line>
                                    </svg>
                                    Choose Excel File
                                </button>

                                <p class="text-gray-500 mt-2">Supported formats: .xlsx, .xls</p>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex mt-2 justify-end gap-3">
                        <button type="button"  class="border border-gray-300 px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors" data-close="bulkUnit">Cancel</button>
                        <?php if(haspermission(session('user_data')['role'],'create_staff') ) { ?>
                            <button type="submit" id="bulksubmitBtn" class="bg-blue-500 px-4 py-2 flex rounded-2 hover:bg-blue-400 text-white transition-colors items-center"> <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-save mr-1"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>Save</button>
                        <?php } ?>
                </div>
            </form>
    </div>
  </div>
</div>