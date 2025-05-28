<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
    <!-- titilebar -->
    <div class="flex items-center justify-between">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center mb-0">
                <h1 class="h3 mb-0"><?= $page ?? '' ?></h1>
                <div>
                    <a  href="<?= base_url('appointments/booking') ?>" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Book New Appointment
                    </a>
                </div>
            </div>
        </div>
    </div><!-- closee titilebar -->

    <!-- Modal -->
    <div id="categoryModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Add New Category</h3>
        <button onclick="closeModal()" class="text-gray-400 hover:text-gray-500">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 6 6 18"></path><path d="m6 6 12 12"></path>
            </svg>
        </button>
        </div>

        <!-- Form -->
        <form id="categoryForm" method="post" action="<?= base_url('category/save') ?>">
            <?= csrf_field() ;?>
            <div class="mb-4">
                <label for="categoryName" class="block text-sm font-medium text-gray-700 mb-1">Category Name</label>
                <input type="text" id="category" name="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter category name" required>
                 <div class="invalid-feedback" id="category_error"></div>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                <button id="submitBtn" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md flex items-center transition-colors"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-save mr-1"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>Save</button>
            </div>
        </form>
    </div>
    </div>



    <!-- body -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden p-4">
        
        <div class="flex flex-col md:flex-row gap-4 mb-6 d-none">
    
            <!-- Column 1: Search Input -->
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search text-gray-400">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.3-4.3"></path>
                </svg>
                </div>
                <input type="text" id="searchInput" placeholder="Search branch by name, or location..." class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
            </div>
            
            <!-- Column 2: Status Dropdown -->
            <div class="w-full md:w-48">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-filter text-gray-400">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    </div>
                    <select id="filerStatus" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none">
                    <option value="all">Account Status</option>
                    <option value="1">Active</option>
                    <!-- <option value="on leave">On Leave</option> -->
                    <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            </div>
            <!-- table -->
             <div class="overflow-x-auto">
                <div id="calendar"></div>
                <div id="clientsTable"></div>
            </div>
            <!-- close table -->
</div><!-- body -->
<?= $this->endSection(); ?>
<?= $this->section('scripts') ?>

    <script>
 document.addEventListener('DOMContentLoaded', function () {
      // This is the element you need to define
      var calendarEl = document.getElementById('calendar');

      var calendar = new FullCalendar.Calendar(calendarEl, {
         initialView: 'timeGridWeek',
        slotMinTime: "09:00:00", // Start at 9 AM
        slotMaxTime: "21:00:00", // End at 9 PM
        slotDuration: "01:00:00", // Optional: 1-hour slots
        initialView: 'timeGridDay', // daily view
         snapDuration: "00:10:00", 
           slotLabelInterval: "01:00:00", 
         allDaySlot: false,
        headerToolbar: {
            left: 'prev,next',
            center: 'title',
            right: 'timeGridDay,timeGridWeek,dayGridMonth,listWeek'
        },
         views: {
          listWeek: {
            buttonText: 'List View'  // Custom button label
          }
        },
        //plugins: ['timeGrid', 'list', 'interaction'],
         dayHeaderDidMount: function(info) {
            info.el.className = '';
            info.el.classList.add(
                'py-3', 'px-2', 'text-xs', 'bg-gray-50', 'text-left',
                'font-medium', 'uppercase', 'tracking-wider', 'border-0', 'w-20'
            );
            },
            eventDidMount: function(info) {
  // Remove default classes if needed
  info.el.classList.remove('fc-event'); // optional

  // Add your custom Tailwind classes
  info.el.classList.add(
    'p-1',
    'rounded',
    'text-green-800',
    'cursor-pointer',
    'hover:shadow-sm',
    'transition-shadow'
  );
},
        events: '<?=base_url('appointments/load');?>', 
        
        eventContent: function (arg) {
          let client = arg.event.extendedProps.client || '';
          let staff = arg.event.extendedProps.staff || '';
          let status = arg.event.extendedProps.status || '';
          let time = arg.event.extendedProps.time || '';
          let branch = arg.event.extendedProps.branch || '';

          let statusText = status == 1 ? 'Confirmed' : status == 2 ? 'Completed' : status == 3 ? 'Pending' : 'Declined';

          return {
            html: `
                    <div class="p-2 rounded border ${status == 1 ? 'bg-blue-100' : (status == 2 ? 'bg-green-100' : (status == 3 ? 'bg-red-100' : 'bg-red-100'))}  border-green-400 text-green-800 cursor-pointer hover:shadow-sm transition-shadow" style="font-size: 12px;">
          <div class="font-medium">${arg.event.title}</div>
          <div class="text-xs">Client: ${client}</div>
          <div class="text-xs">Staff: ${staff}</div>
          <div class="text-xs mt-1 capitalize">Time: ${time}</div>
          <div class="text-xs mt-1 capitalize">Status:  ${status == 1 ? 'Confirmed' : (status == 2 ? 'Completed' : (status == 3 ? 'Pending' : 'Declined'))}</div>
        </div>
            `
          };
        }
      });

      calendar.render();

      // Toggle buttons
      document.getElementById('gridViewBtn').addEventListener('click', function () {
        calendar.changeView('timeGridWeek');
      });

      document.getElementById('listViewBtn').addEventListener('click', function () {
        calendar.changeView('listWeek');
      });
    });
    </script>
<?= $this->endSection() ?>

