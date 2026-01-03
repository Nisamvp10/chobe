<div id="assignModalverify" class="fixed inset-0 flex items-center justify-center bg-black/40 z-50 hidden">
  <div class="bg-white w-full max-w-md rounded-xl shadow-xl p-6 relative">

    <!-- Header -->
    <div class="flex justify-between items-start mb-4">
      <div>
        <h2 class="text-lg font-semibold text-gray-900">Auto Assign Task</h2>
        <p class="text-sm text-gray-500 mt-1">
         This task will be stored under the current Project Unit
        </p>
      </div>
      <button class="text-gray-400 hover:text-gray-600" data-close="assignModalverify">✕</button>
    </div>

    <!-- Assignment Options -->
    <div class="space-y-4 mt-6">

      <!-- Temporary -->
      <label class="assign-card">
        <input type="radio" name="assignment_mode" value="temporary" checked hidden>
        <div class="card-inner">
          <div class="radio-dot"></div>
          <div>
            <h4 class="font-medium text-gray-900">Temporary Tasks Only</h4>
            <p class="text-sm text-gray-500 mb-0">
              This option applies only to temporary tasks.
            </p>
          </div>
        </div>
      </label>

      <!-- Permanent -->
      <label class="assign-card">
        <input type="radio" name="assignment_mode" value="permanent" hidden>
        <div class="card-inner">
          <div class="radio-dot"></div>
          <div>
            <h4 class="font-medium text-gray-900">Permanent Tasks Only</h4>
            <p class="text-sm text-gray-500 mb-0">
              This option applies only to permanent tasks.
            </p>
          </div>
        </div>
      </label>

    </div>

    <!-- Footer -->
    <div class="flex justify-end gap-4 mt-8">
      <button
        class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 border-gray-50 border rounded-2"
       data-close="assignModalverify">
        Cancel
      </button>

      <button
        class="flex autoAssign items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-2 text-sm font-medium">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
             viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
        Confirm Assignment
      </button>
    </div>

  </div>
</div>
