<!-- Banner Modal -->
<div id="careerModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 wrapModal">
  <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[100vh] min-h-[80vh] overflow-y-auto">
    <div class="flex items-center justify-between p-6 border-b border-gray-200">
      <h2 class="text-2xl font-bold text-gray-900 head"></h2>
      <button data-close="careerModal" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
        ✕
      </button>
    </div>
    <div class="p-6">
         <!-- Progress steps -->
    <div class="flex justify-center mb-6">
      <div class="flex items-center">
        <div id="step1circle" class="w-8 h-8 rounded-full bg-blue-600 text-white flex justify-center items-center">1</div>
        <div class="w-10 h-[2px] bg-blue-600"></div>
        <div id="step2circle" class="w-8 h-8 rounded-full bg-gray-300 text-gray-700 flex justify-center items-center">2</div>
        <div class="w-10 h-[2px] bg-gray-300"></div>
        <div id="step3circle" class="w-8 h-8 rounded-full bg-gray-300 text-gray-700 flex justify-center items-center">3</div>
      </div>
    </div>

        <form class="p-6 space-y-6" id="careerForm" enctype="multipart/form-data" method="post"> 
             <?= csrf_field() ?>
              <input type="hidden" name="itmId" id="edit_id" />
               <div id="step1" class="step">
                    <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
                        <div >
                        <div class="form-group ">
                            <label class="block text-gray-700 font-semibold mb-2">Upload  Image</label>
                            <input type="hidden" name="selected_image" id="selected_image">
                                    <button type="button" id="openUploader" data-folder="career" class="bg-gray-100 border px-4 py-2 rounded hover:bg-gray-200 w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent block w-full text-sm text-gray-700 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                                        Choose Image
                                    </button>
                                    <div id="selectedPreview" class="mt-3 hidden">
                                        <img src="" id="previewImg" class="w-full max-h-60 object-contain border rounded ">
                                    </div>
                                    <input type="file" name="file" id="imageInput" class="hidden " accept="image/*">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Job Title *</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 mt-2 items-center pointer-events-none"><i class="bi bi-briefcase text-xl text-gray-400"></i></div>
                                <input type="text" name="title"  id="title" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter Your JOB TITLE">
                                <div class="invalid-feedback" id="title_error"></div>
                            </div>
                        </div>
                         <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                             <div class="form-group mb-0">
                            <label for="supplier" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" name="status" id="status">
                                <option value="">Choose Status</option>
                                <option value="1">Urgent</option>
                                <option value="2">ASAP</option>
                                <option value="2">Immediate  Start</option>
                            </select>
                            <div class="invalid-feedback" id="status_error"></div>
                        </div>
                         <div class="form-group mb-0">
                            <label for="supplier" class="block text-sm font-medium text-gray-700 mb-2">Duration *</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" name="duration" id="duration">
                                <option value="">Choose Status</option>
                                <option value="Full Time">Full Time</option>
                                <option value="Part Time">Part Time</option>
                            </select>
                            <div class="invalid-feedback" id="duration_error"></div>
                        </div>

                         </div>
                       
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Short Note *</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 mt-2 items-center pointer-events-none"><i class="bi bi-pen text-xl text-gray-400"></i></div>
                                <textarea name="note" rows="3"  id="note" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter Your Job Short Note...."></textarea>
                                <div class="invalid-feedback" id="note_error"></div>
                            </div>
                        </div>

                        <div>
                            <div class="form-group ">
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2"> Description </label>
                                <textarea  id="description" rows="3" name="description" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter Description...."></textarea>
                                <div class="invalid-feedback" id="description_error"></div>
                            </div>
                            
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded" onclick="nextStep(2)">Next</button>
                    </div>
                </div> <!-- close set 1 -->
             <!-- Step 2 -->
                <div id="step2" class="step hidden">
                    <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
                        <div >
                            <label class="block text-sm font-medium text-gray-700 mb-1">Highlights Section Title</label>
                             <div class="absolute inset-y-0 left-0 pl-3 mt-2 items-center pointer-events-none"><i class="bi bi-briefcase text-xl text-gray-400"></i></div>
                            <input type="text" name="highlights_title" id="highlights_title" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="">
                            <div class="invalid-feedback" id="note_error"></div>
                        </div>
                        <div id="productWrapper">
                              <div class="border border-gray-200 rounded-lg p-4 new-row mt-2">
                                <div>
                                    <label class="block mb-2 text-sm font-semibold">Highlight Points</label>
                                    <div id="points">
                                        <input type="text" name="points[]" class="w-full border p-2 rounded mb-2" placeholder="Highlight point 1">
                                        <div class="invalid-feedback" id="points_error"></div>
                                    </div>
                                    <button type="button" onclick="addPoint()" class="text-blue-600 text-sm mb-4">+ Add Point</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-between mt-3">
                        <button type="button" class="border px-4 py-2 rounded" onclick="prevStep(1)">Previous</button>
                        <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded" onclick="nextStep(3)">Next</button>
                    </div>
                </div>
                <div id="step3" class="step hidden">
                    <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
                         <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 mt-2 items-center pointer-events-none"><i class="bi bi-briefcase text-xl text-gray-400"></i></div>
                                <input type="text" name="category" id="category" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter Your Job Category">
                                <div class="invalid-feedback" id="category_error"></div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">vacancy *</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 mt-2 items-center pointer-events-none"><i class="bi bi-star text-xl text-gray-400"></i></div>
                                <input type="text" name="vacancy"  id="vacancy" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter Your Job vacancy [01 Available]">
                                <div class="invalid-feedback" id="vacancy_error"></div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Job Code</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 mt-2 items-center pointer-events-none"><i class="bi bi-hash text-xl text-gray-400"></i></div>
                                <input type="text" name="job_code" id="jobcode" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter Your Job jobcode">
                                <div class="invalid-feedback" id="job_code_error"></div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Salary</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 mt-2 items-center pointer-events-none"><i class="bi bi-cash text-xl text-gray-400"></i></div>
                                <input type="text" name="salary" id="salary" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter Your Job Salary">
                                <div class="invalid-feedback" id="salary_error"></div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Apply On</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 mt-2 items-center pointer-events-none"><i class="bi bi-calendar text-xl text-gray-400"></i></div>
                                <input type="date" name="apply_on" id="apply_on" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter Your Apply On">
                                <div class="invalid-feedback" id="apply_on_error"></div>
                            </div>
                        </div>   
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Location *</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 mt-2 items-center pointer-events-none"><i class="bi bi-geo-alt text-xl text-gray-400"></i></div>
                                <input type="text"  name="location" id="location" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter Your Job Location">
                                <div class="invalid-feedback" id="location_error"></div>
                            </div>
                        </div>                   

                        <div class="flex justify-end space-x-4 pt-6 gap-3 border-t border-gray-200">
                            <button type="button" class="border px-4 py-2 rounded" onclick="prevStep(2)">Previous</button>
                            <button type="button" data-close="careerModal" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors !rounded-md">Cancel</button>
                            <button type="submit" id="submitBtn" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors !rounded-md">Save</button>
                        </div>
                    </div>
                </div>
        </form>
    </div>
  </div>
</div>
