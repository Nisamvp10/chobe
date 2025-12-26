

<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<style>
/* wrapper for each level */
.org-level {
    position: relative;
}

/* vertical line from parent card to children */
.org-children::before {
    content: '';
    position: absolute;
    top: 0;
    left: 50%;
    width: 2px;
    height: 41px;
    background-color: #cbd5e1;
    transform: translateX(-50%);
}

/* horizontal line connecting siblings */
.org-level::before {
    content: '';
    position: absolute;
    top: 0;
    left: 10%;
    right: 10%;
    height: 2px;
    background-color: #cbd5e1;
}

/* remove horizontal line if only one child */
.org-level.single::before {
    display: none;
}
</style>
    <!-- titilebar -->
    <div class="flex items-center justify-between">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center mb-0">
                <h1 class="h3 mb-0"><?= $page ?? '' ?></h1>
                <?php if(haspermission('','role_master')) : ?>
                <div>
                    <button onclick="openModal()" href="<?= base_url('branch/create') ?>" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Add New Role
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div><!-- closee titilebar -->

    <!-- body -->
<div class="bg-white rounded-lg shadow-sm overflow-hidden p-4">
   <div class="flex flex-col md:flex-row gap-4 mb-6">
      <!-- Column 1: Search Input -->
      <div class="flex-1 relative">
         <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search text-gray-400">
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
               <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-filter text-gray-400">
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
      <div id="orgChart"></div>
   </div>
   <!-- close table -->
</div>
<!-- body -->

<?= view('modal/rolemasterModal'); ?>

<?= $this->endSection(); ?>
<?= $this->section('scripts') ?>
    <script>
         projects();
          // modal
        function openModal(id=false) {
        let modal = $('#categoryModal');
        toggleModal('categoryModal',true);
        modal.find('.head').text('Add New Role');
        const webForm = document.getElementById('rolemasterForm');
        webForm.querySelector('#role').value="";
        webForm.querySelector('#parent_id').value="";
        $('#roleid').val('');
        if(id) {
             $('#roleid').val(id);
            fetch(App.getSiteurl() +'edit-rolemaster/'+`${id}`)
            .then(res => res.json())
            .then(result =>{
                if(result) { 
                    webForm.querySelector('#role').value=result.name;
                    webForm.querySelector('#parent_id').value=result.parent_id;
                }
            })
        }

    }
        //$(document).ready(function() {

            function projects(search = '') {
                let filter = $('#filerStatus').val();
                $.ajax({
                    url: "<?= site_url('rolemaster/list') ?>",
                    type: "GET",
                    data: { search: search,filter:filter },
                    dataType: "json",
                    success: function(response) {
                        
                        if (response.success) {
                            renderMsaterTable(response.projects);
                        }
                    }
                });
            }

  function renderMsaterTable(projects) {

    if (!projects || projects.length === 0) {
        $('#orgChart').html(`
            <div class="text-center py-8">
                <h3 class="text-lg font-medium text-gray-700">No Data Found</h3>
            </div>
        `);
        return;
    }

    // Group employees by parent_id
    const map = {};
    projects.forEach(p => {
        const key = p.parent_id ?? 0;
        if (!map[key]) map[key] = [];
        map[key].push(p);
    });

    function buildLevel(parentId = 0) {
        if (!map[parentId]) return '';

        let html = `<div class="org-level flex justify-center gap-10 mt-10 relative      ${map[parentId].length === 1 ? 'single' : ''}">`;

        map[parentId].forEach(emp => {
            html += `
                <div class="flex flex-col items-center relative">

                    <!-- CARD -->
                    <div class="bg-white flex gap-2 border rounded-xl shadow items-center px-2 py-2 min-w-auto text-center relative">
                        ${
                            emp.avatar
                            ? `<img src="${emp.avatar}" class="w-10 h-10 rounded-full mx-auto mb-2">`
                            : `<div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-0">
                                 <span class="font-bold text-orange-600 ml-0">
                                    ${emp.name.charAt(0).toUpperCase()}
                                 </span>
                               </div>`
                        }
                        <span class="font-semibold text-sm">${emp.name}</hspan>
                        <div class="absolute right-2 top-2 flex gap-2">
                            <span class="cursor-pointer" onclick="openModal('${emp.encrypted_id}')"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen "><path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.375 2.625a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4Z"></path></svg></span>
                            <span class="cursor-pointer" onclick="deleteRole(this)"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash "><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path></svg></span>
                        </div>
                    </div>

                    <!-- CHILDREN -->
                   <div class="org-children relative">
                        ${buildLevel(emp.id)}
                    </div>
                </div>
            `;
        });

        html += `</div>`;
        return html;
    }

    $('#orgChart').html(buildLevel());
}


            //loadClients();

            $('#searchInput').on('input',function(){
                let value = $(this).val();
                projects(value);
            })
            $('#filerStatus').on('change',function(){
                let value = $('#searchInput').val();
                projects(value);
            })
       
            $('#rolemasterForm').on('submit', function(e) {
                let webForm = $('#rolemasterForm');
                e.preventDefault();

                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').empty();

                $('#submitBtn').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
                );

                $.ajax({
                    url : '<?=base_url('rolemaster/save');?>',
                    method:'POST',
                    data: $(this).serialize(),
                    dataType : 'json',
                    success:function(response)
                    { 
                        if(response.success){
                            toastr.success(response.message);
                            webForm[0].reset();
                            projects();
                        }else{
                            if(response.errors){
                                $.each(response.errors,function(field,message)
                                {
                                    $('#'+ field).addClass('is-invalid');
                                    $('#' + field + '_error').text(message);
                                })
                            }else{
                                 toastr.error(response.message);
                            }
                        }
                    },error: function() {
                        toastr.error('An error occurred while saving Project');
                    },
                    complete: function() {
                        // Re-enable submit button
                        $('#submitBtn').prop('disabled', false).text('Save ');
                    }
                })
            })
    
//})
function unlockCategory(e){
        if(confirm('are you sure ! You want to Unlock Category'))
        {
            $(e).prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Unlocking...'
            );
            let id = $(e).data('id');
            $.ajax({
                url : '<?=base_url('project/unlock');?>',
                method:'POST',
                data: {id:id},
                dataType : 'json',
                success:function(response)
                {
                    if(response.success)
                    {
                        toastr.success(response.message);
                          setTimeout(function() {
                           projects();
                        }, 3000)
                        
                    }else{
                        toastr.error(response.message);
                    }
                }
            })
        }
    }
    </script>
<?= $this->endSection() ?>

