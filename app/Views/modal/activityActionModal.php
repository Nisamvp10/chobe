<!-- Modal -->
<div id="taskModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-100 hidden overflow-y-auto" onclick="closeTaskModal(event)">
   <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl h-[90vh] overflow-y-auto">
      <!-- head 123123-->
      <div class="p-4 border-b flex justify-between items-center">
         <h2 class="text-xl font-semibold text-gray-800 modal-title"></h2>
         <div class="flex space-x-2">
            <button onclick="showStep(1)" class="p-1.5 rounded-md text-gray-500 hover:bg-gray-100" title="View History">
               <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock ">
                  <circle cx="12" cy="12" r="10"></circle>
                  <polyline points="12 6 12 12 16 14"></polyline>
               </svg>
            </button>
            <button onclick="showStep(2)" class="modal-action-btn p-1.5 rounded-md text-gray-500 hover:bg-gray-100"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-square "><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg></button>
            <?php if(hasPermission('','task_edit')) {?>
            <!-- <button onclick="toggleEditForm()" class="p-1.5 rounded-md text-gray-500 hover:bg-gray-100"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen "><path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.375 2.625a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4Z"></path></svg></button> -->
            <?php } if(hasPermission('','task_delete')) {?>
            <button  class="p-1.5 rounded-md text-gray-500 hover:bg-red-100 hover:text-red-600 delete-task"  data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="deleteTask(this)">
               <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash ">
                  <path d="M3 6h18"></path>
                  <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                  <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
               </svg>
            </button>
            <?php } ?>
             <button onclick="showStep(3)" class="p-1.5 rounded-md text-gray-500 hover:bg-gray-100" title="View History">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"     stroke-linecap="round"
     stroke-linejoin="round"      class="lucide lucide-message-circle">  <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21 l1.9-5.7a8.38 8.38 0 0 1-.9-3.8
           8.5 8.5 0 0 1 4.7-7.6
           8.38 8.38 0 0 1 3.8-.9h.5
           a8.48 8.48 0 0 1 8 8v.5z"/>
</svg>

            </button>

            <button onclick="closeTaskModal()" class="p-1.5 rounded-md text-gray-500 hover:bg-gray-100">
               <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x ">
                  <path d="M18 6 6 18"></path>
                  <path d="m6 6 12 12"></path>
               </svg>
            </button>
         </div>
      </div>
      <!-- close Head -->
       <div class="step1 step-active">
      <div class="flex-1 overflow-y-auto p-4 space-y-4" id="taskDetails">
         <div class="flex items-center gap-2">
            <span class="px-2 py-1 rounded-full text-xs text-white modal-priority font-medium   "></span>
            <span class="text-sm text-gray-500">Created on <span class="modal-date"></span></span>
         </div>
         <p class="text-gray-700 modal-desc">Description</p>
         <!-- <p class="text-gray-700 ">Branch : <span class="modal-branch"></span></p> -->
         <p class="text-gray-700">Status: <span class="modal-status capitalize"></span></p>
        
         <div id="documents"></div>
         <p class="text-sm text-gray-500">Duration <span class="modal-duration"></span></p>
         <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Assigned Staff:</h3>
            <div class="space-y-2">
               <div id="modal-profiles" class="mt-1 grid grid-cols-2 gap-2 "></div>
            </div>
         </div>
      </div>
      </div>

      <!-- history -->
       <div class="step2">
      <div class="w-full p-6 overflow-y-auto border-l  h-full flex flex-col" id="taskHistory">
         <div class="space-y-4">
              <div id="taskreplaysec"></div>
         </div>
      </div>
    <div id="replyForm" class="w-full p-6 overflow-y-auto border-l  h-80 flex flex-col">
         <form class="mb-4" id="replyTaskForm">
            <div class="flex space-x-2">
               <textarea placeholder="Enter your reply..." name="replay" class="flex-1 min-h-[100px] p-3 border rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            </div>
            <div class="flex justify-end space-x-2 mt-2">
               <button id="replaysubmitBtn" type="submit" class="flex items-center space-x-1 px-3 py-1 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-send">
                     <path d="M22 2 11 13"></path>
                     <path d="M22 2 15 22 11 13 2 9 22 2z"></path>
                  </svg>
                  <span>Send</span>
               </button>
            </div>
         </form>
      </div>
      </div>
      <!-- history -->
      <!-- Reply Form -->

      <!-- close  replay form -->
      <!-- from  -->
      <!-- Task Edit Panel -->
       <div class="step3">
      <div  class="w-full p-6 overflow-y-auto border-l  h-full flex flex-col h-screen" >
         <h2 class="text-xl font-semibold mb-4">Commets</h2>
         <div id="commentSection" class="w-full"></div>
         </div>
         
        
      <!-- close edit form -->
   </div>
   </div>
</div>