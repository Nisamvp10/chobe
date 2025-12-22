
<div class="modal fade" id="projectUnitModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered hadow-xl w-full max-w-2xl  max-w-3xl flex flex-col">
    <div class="modal-content ">
      <div class="modal-header">
        <h5 class="modal-title">Create New Activity</h5>
        <p></p>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="bg-white rounded-lg  overflow-hidden p-2">
            <form id="projectUnitForm" method="post">
                 <?= csrf_field() ?>
                <div class="grid grid-cols-1 gap-4 pb-4">

                   <div >
                    <label class="block text-sm font-medium text-gray-700 mb-1">Store Name</label>
                    <div class="relative">
                        <input type="hidden" name="projectId" value="" />
                        <div class="absolute inset-y-0 left-0 pl-3 mt-2 items-center pointer-events-none"><i class="bi bi-shop-window text-xl text-gray-400"></i></div>
                        <input type="text" name="store" value=""  id="store" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter Your Store Name">
                        <div class="invalid-feedback" id="store_error"></div>
                    </div>
                </div>

                     <div >
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact Person Name</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 mt-2 items-center pointer-events-none"><i class="bi bi-person text-xl text-gray-400"></i></div>
                        <input type="text" name="rmname" value=""  id="rmname" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Contact Person Name">
                        <div class="invalid-feedback" id="rmname_error"></div>
                    </div>
                </div>
                   <div >
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Id</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 mt-2 items-center pointer-events-none"><i class="bi bi-envelope text-xl text-gray-400"></i></div>
                        <input type="email" name="rm_mail" value=""  id="rm_mail" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter RM EMail ID">
                        <div class="invalid-feedback" id="rm_mail_error"></div>
                    </div>
                </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Polaris Code</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 mt-2 items-center pointer-events-none"><i class="bi bi-hash text-xl text-gray-400"></i></div>
                            <input type="number" name="polaris_code" value=""  id="polaris_code" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter Polaris ">
                            <div class="invalid-feedback" id="polaris_code_error"></div>
                        </div>
                    </div>

                     <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Oracle Code</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 mt-2 items-center pointer-events-none"><i class="bi bi-hash text-xl text-gray-400"></i></div>
                            <input type="number" name="oracle_code" value=""  id="oracle_code" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter Oracle Code">
                            <div class="invalid-feedback" id="oracle_code_error"></div>
                        </div>
                    </div>
                                            
                    <div class="w-full">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Unit Type</label>
                        <div class="responseive">
                        <select id="status" name="status" class="pl-3 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select a Type</option>
                            <option value="Stores">Stores</option>
                            <option value="Suppliers">Suppliers</option>
                            <option value="Managers">Managers</option>
                            <option value="Companies">Companies</option>
                        </select>
                        <div class="invalid-feedback" id="status_error"></div>
                    </div>
                    </div>

                </div>
            
                    <div class="mt-8 flex justify-end gap-3">
                            <a  data-bs-dismiss="modal" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">Cancel</a>
                            <button id="submitBtn" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md flex items-center transition-colors"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-save mr-1"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>Save</button>
                    </div>
            </form>
        </div>
      </div>
      
    </div>
  </div>
</div>
