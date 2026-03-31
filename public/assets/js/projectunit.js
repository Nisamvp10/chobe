$('.rmSelect').select2({
    width: '100%',
    placeholder: 'Select RM',
    allowClear: true,
    selectionCssClass: "pl-3 pr-3 py-2 w-full border border-gray-300 h-auto rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"

});

$('.smSelect').select2({
    width: '100%',
    placeholder: 'Select Store Manager',
    allowClear: true,
    selectionCssClass: "pl-3 pr-3 py-2 w-full border border-gray-300 h-auto rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
});

$('.allocatedToSelect').select2({
    width: '100%',
    placeholder: 'Select Allocated User',
    allowClear: true,
    selectionCssClass: "pl-3 pr-3 py-2 w-full border border-gray-300 h-auto rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
});

$('.assignedToSelect').select2({
    width: '100%',
    placeholder: 'Select Assigned User',
    allowClear: true,
    selectionCssClass: "pl-3 pr-3 py-2 w-full border border-gray-300 h-auto rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
});

$('.projectSelect').select2({
    width: '100%',
    placeholder: 'Select Project',
    allowClear: true,
    selectionCssClass: "pl-3 pr-3 py-2 w-full border border-gray-300 h-auto rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
});

function openModal(id = false) {
    toggleModal('projectUnitModal', true);

    let modal = $('#projectUnitModal');
    modal.find('.head').text(id ? 'Edit Project Unit' : 'Add Project Unit');
    let webForm = document.getElementById('projectUnitForm');
    webForm.querySelector('#name').value = '';
    webForm.querySelector('#projectId').value = '';
    webForm.querySelector('#old_name').value = '';
    webForm.querySelector('#oracle_code').value = '';
    webForm.querySelector('#polaris_code').value = '';
    webForm.querySelector('#rm_mail').value = '';
    webForm.querySelector('#contact_number').value = '';
    webForm.querySelector('#client').value = '';
    webForm.querySelector('#start_date').value = '';
    // webForm.querySelector('#rm').value = '';
    webForm.querySelector('#store_manager').value = '';
    webForm.querySelector('#allocated_date').value = '';
    webForm.querySelector('#assigned_to').value = '';
    webForm.querySelector('#assigned_date').value = '';
    webForm.querySelector('#allocatedType').value = 1;
    webForm.querySelector('#assignedType').value = 2;
    webForm.querySelector('#allocated_to').value = '';

    if (id) {

        fetch(App.getSiteurl() + `api/project-units/${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    webForm.querySelector('#name').value = data.result.store;
                    webForm.querySelector('#old_name').value = data.result.oldstore_name;
                    webForm.querySelector('#oracle_code').value = data.result.oracle_code;
                    webForm.querySelector('#polaris_code').value = data.result.polaris_code;
                    webForm.querySelector('#rm_mail').value = data.result.rm_mail;
                    webForm.querySelector('#contact_number').value = data.result.contact_number;
                    webForm.querySelector('#client').value = data.result.client_id;
                    webForm.querySelector('#start_date').value = data.result.start_date;
                    webForm.querySelector('#rm').value = data.result.rm_mail;
                    webForm.querySelector('#allocated_date').value = data.result.allocated_date;
                    webForm.querySelector('#assigned_to').value = '';
                    webForm.querySelector('#assigned_date').value = data.result.assigned_date;
                    webForm.querySelector('#projectId').value = data.result.id;

                    loadClientUsers(data.result.client_id, {
                        rm: data.result.regional_manager_id,
                        store_manager: data.result.manager_id,

                    });
                    // $('#rm').val(data.result.regional_manager_id).trigger('change');
                    //$('#store_manager').val(data.result.manager_id).trigger('change');
                    $('#allocated_to').val(data.result.allocated_to).trigger('change');
                    $('#assigned_to').val(data.result.assigned_to).trigger('change');
                    $('#project').val(data.result.project_id).trigger('change');


                    let assignType = (data.result.assigned_type == 'permanently') ? 1 : 2;
                    let allocatedType = (data.result.allocated_type == 'permanently') ? 1 : 2;
                    webForm.querySelector('#allocatedType').value = allocatedType;
                    webForm.querySelector('#assignedType').value = assignType;
                    //data-close="projectUnitModal" click automatic close the modal

                }
            })
    }
}

$('#client').on('change', function () {

    let clientId = $(this).val();
    loadClientUsers(clientId);

});

function loadClientUsers(clientId, selected = {}) {

    $('#store_manager').html('<option>Loading...</option>');

    if (!clientId) return;

    fetch(App.getSiteurl() + `api/clients/${clientId}/projects`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(res => res.json())
        .then(data => {

            let rmOptions = '<option value="">Select RM</option>';
            let managerOptions = '<option value="">Select Store Manager</option>';

            /* ------------------------
               Build RM Options
            ------------------------ */

            if (data.rms?.length) {
                data.rms.forEach(rm => {
                    rmOptions += `<option value="${rm.id}">${rm.name}</option>`;
                });
            }

            /* ------------------------
               Build Store Manager Options
            ------------------------ */

            if (data.store_managers?.length) {
                data.store_managers.forEach(manager => {
                    managerOptions += `<option value="${manager.id}">${manager.name}</option>`;
                });
            }

            /* ------------------------
               Update DOM once
            ------------------------ */

            $('#rm').html(rmOptions);
            $('#store_manager').html(managerOptions);

            /* ------------------------
               Set selected values
            ------------------------ */

            if (selected.rm) {
                $('#rm').val(selected.rm);
            }

            if (selected.store_manager) {
                $('#store_manager').val(selected.store_manager);
            }

            /* ------------------------
               Refresh Select2
            ------------------------ */

            $('#rm').trigger('change');
            $('#store_manager').trigger('change');

        })
        .catch(err => {
            console.error('Error fetching data:', err);
        });
}




function openModalbulk($id = false) {
    toggleModal('bulkUnit', true);
    $('#bulkUnit .head').text($id ? 'Edit Bulk Units' : 'Add Bulk Units');
}
$(document).on('submit', '#unitBulkForm', function (e) {

    e.preventDefault();
    let webForm = $(this);
    let formData = new FormData(this);
    let butn = $(webForm).find('button[type="submit"]');
    butn.attr('disabled', true).html('Processing..');
    $.ajax({
        url: App.getSiteurl() + 'projectunit/bulk-upload',
        method: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',

        success: function (response) {
            if (response.success) {
                toastr.success(response.message);
                butn.attr('disabled', false).html('Save Changes');
            } else {
                toastr.error(response.message);
                butn.attr('disabled', false).html('Save Changes');
            }
        }, error: function (err) {
            console.log(err);
        }, completed: function () {
            butn.attr('disabled', false).html('Save Changes');
        }
    })
})
document.getElementById('chooseFileBtn').addEventListener('click', function () {
    document.getElementById('staff_excel').click();
});


// here
projects();
// modal

//$(document).ready(function() {

function projects(search = '') {
    let filter = $('#filerStatus').val();
    search = $('#searchInput').val();
    let project = $('#filerProject').val();
    $.ajax({
        url: App.getSiteurl() + "project-unit/list",
        type: "post",
        data: { search: search, filter: filter, project: project },
        dataType: "json",
        success: function (response) {

            if (response.success) {
                renderunitTable(response.projects);
            }
        }
    });
}

let currentPage = 1;
let rowsPerPage = 15;
let allData = [];

function renderunitTable(projects) {
    let html = '';
    let count = 1;
    allData = projects;
    let start = (currentPage - 1) * rowsPerPage;
    let end = start + rowsPerPage;
    let pagination = allData.slice(start, end);

    if (projects.length === 0) {
        html += `
                        <div class="text-center py-8">
                            <h3 class="text-lg font-medium text-gray-700">No Projects found</h3>
                            <p class="text-gray-500 mt-1">Try adjusting your search</p>
                        </div>
                    `;
    } else {
        html += `
                        <table class="min-w-full divide-y divide-gray-200 border bg-gray-100">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">S.NO</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Old Store</th> 
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Oracle code</th> 
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Polaris code</th> 
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"> mail </th> 
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"> RM </th> 
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"> SM </th> 
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"> PH </th> 
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                    `;
        pagination.forEach((project, index) => {
            html += `
                            <tr class="hover:bg-gray-50 ${project.is_active == 0 ? 'bg-red-100 bg-opacity-50' : ''}"  >
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="text-sm font-medium text-gray-900">${start + index + 1}</div>
                                    </div>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">${project.store} - [${project.clientName}]</div>
                                </td>
                                 <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">${project.oldstore_name}</div>
                                </td>
                               
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">#${project.oracle_code}</div>
                                </td>
                                 <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">#${project.polaris_code}</div>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">${project.rm_mail}</div>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">${project.rm}</div>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">${project.manager}</div>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">${project.contact_number}</div>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap text-right text-sm font-medium"> 
                                <a href="#" class="text-blue-600 hover:text-blue-800 mr-3" onclick="deleteProject('${project.encrypted_id}')"><i class="bi bi-trash"></i> </a>
                                ${project.is_active == 1 ? `<a onclick="openModal('${project.encrypted_id}')" class=" hover:text-blue-800 mr-3 ${project.allocated_to == 0 || project.assigned_to == 0 ? '!text-red-600' : '!text-blue-600'} ">Edit</a>` : `<span data-id="${project.encrypted_id}" onclick="unlockCategory(this)" class="text-blue-600 hover:text-blue-800 mr-3 cursor-pointer "><i class="bi bi-lock"></i></span>`}
                                </td>
                            </tr>
                        `;
            count++;
        });


        html += `</tbody></table>`;
        let totalPages = Math.ceil(projects.length / rowsPerPage);
        html += `
            <div class="flex justify-between items-center mt-4">
                <div>
                    <label class="mr-2">Rows per page:</label>
                    <select onchange="changeRowsPerPage(this.value)" class="px-2 py-1 border rounded">
                        <option value="15"  ${rowsPerPage == 15 ? 'selected' : ''}>15</option>
                        <option value="25"  ${rowsPerPage == 25 ? 'selected' : ''}>25</option>
                        <option value="50"  ${rowsPerPage == 50 ? 'selected' : ''}>50</option>
                        <option value="100" ${rowsPerPage == 100 ? 'selected' : ''}>100</option>
                    </select>
                </div>
                <div>
                    <button onclick="prevPage()" ${currentPage === 1 ? 'disabled' : ''} class="px-3 py-1 bg-gray-200 rounded disabled:opacity-50">Prev</button>
                    <span class="mx-2">Page ${currentPage} of ${totalPages}</span>
                    <button onclick="nextPage(${totalPages})" ${currentPage === totalPages ? 'disabled' : ''} class="px-3 py-1 bg-gray-200 rounded disabled:opacity-50">Next</button>
                </div>
            </div>`;
    }
    $('#projectTable').html(html);
}

// Change rows per page
function changeRowsPerPage(value) {
    rowsPerPage = parseInt(value);
    currentPage = 1;
    renderunitTable(allData);
}

// Pagination functions
function prevPage() {
    if (currentPage > 1) {
        currentPage--;
        renderunitTable(allData);
    }
}
function nextPage(totalPages) {
    if (currentPage < totalPages) {
        currentPage++;
        renderunitTable(allData);
    }
}

//loadClients();

$('#searchInput').on('input', function () {
    let value = $(this).val();
    projects(value);
})
$('#filerStatus,#filerProject').on('change', function () {
    let value = $('#searchInput').val();
    projects(value);
})

$('#projectUnitForm').on('submit', function (e) {
    let webForm = $('#projectUnitForm');
    e.preventDefault();

    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').empty();

    $('#submitBtn').prop('disabled', true).html(
        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
    );

    $.ajax({
        url: App.getSiteurl() + 'project-unit/save',
        method: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                $('#submitBtn').prop('disabled', false).html('save');
                toastr.success(response.message);
                toggleCustomModal('projectUnitModal', false);
                webForm[0].reset();
                projects();
            } else {
                $('#submitBtn').prop('disabled', false).html('save');
                if (response.errors) {
                    $.each(response.errors, function (field, message) {
                        $('#' + field).addClass('is-invalid');
                        $('#' + field + '_error').text(message);
                    })
                } else {
                    toastr.error(response.message);
                }
            }
        }, error: function () {
            toastr.error('An error occurred while saving Project');
        },
        complete: function () {
            // Re-enable submit button
            $('#submitBtn').prop('disabled', false).text('Save ');
        }
    })
})

//})
function unlockCategory(e) {
    if (confirm('are you sure ! You want to Unlock Category')) {
        $(e).prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Unlocking...'
        );
        let id = $(e).data('id');
        $.ajax({
            url: App.getSiteurl() + 'project/unlock',
            method: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message);
                    setTimeout(function () {
                        projects();
                    }, 3000)

                } else {
                    toastr.error(response.message);
                }
            }
        })
    }
}
function deleteProject(id) {
    if (confirm('are you sure ! You want to Delete Project')) {
        let html = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Deleting...';
        $(this).html(html);
        $.ajax({
            url: App.getSiteurl() + 'project-unit/delete',
            method: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message);
                    setTimeout(function () {
                        projects();
                    }, 3000)

                } else {
                    toastr.error(response.message);
                }
            }
        })
    }
}