
function openModal(id = false) {
    toggleModal('projectUnitModal', true);

    let modal = $('#projectUnitModal');
    modal.find('.head').text(id ? 'Edit Project Unit' : 'Add Project Unit');
    let webForm = document.getElementById('projectUnitForm');
    webForm.querySelector('#store').value = '';
    webForm.querySelector('#projectId').value = '';
    webForm.querySelector('#old_name').value = '';
    webForm.querySelector('#oracle_code').value = '';
    webForm.querySelector('#polaris_code').value = '';
    webForm.querySelector('#rm_mail').value = '';
    webForm.querySelector('#contact_number').value = '';
    webForm.querySelector('#client').value = '';
    webForm.querySelector('#start_date').value = '';
    webForm.querySelector('#rm').value = '';
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
                    webForm.querySelector('#store').value = data.result.store;
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
                        allocated_to: data.result.allocated_to,
                        assigned_to: data.result.assigned_to
                    });
                    $('#rm').val(data.result.regional_manager_id).trigger('change');


                    let assignType = (data.result.assigned_type == 'permanently') ? 1 : 2;
                    let allocatedType = (data.result.allocated_type == 'permanently') ? 1 : 2;
                    webForm.querySelector('#allocatedType').value = allocatedType;
                    webForm.querySelector('#assignedType').value = assignType;
                }
            })
    }
}

$('#client').on('change', function () {

    let clientId = $(this).val();
    loadClientUsers(clientId);

});


function loadClientUsers(clientId, selected = {}) {

    $('#rm').html('<option>Loading...</option>');
    $('#assigned_to').html('<option>Loading...</option>');
    $('#allocated_to').html('<option>Loading...</option>');
    $('#store_manager').html('<option>Loading...</option>');

    if (!clientId) return;

    fetch(App.getSiteurl() + `api/clients/${clientId}/projects`)
        .then(res => res.json())
        .then(data => {

            // Reset dropdowns
            $('#rm').html('<option value="">Select RM</option>');
            $('#store_manager').html('<option value="">Select Store Manager</option>');
            $('#assigned_to').html('<option value="">Select Assigned User</option>');
            $('#allocated_to').html('<option value="">Select Allocated User</option>');

            // 🔹 RMs
            if (data.rms?.length) {
                data.rms.forEach(rm => {
                    $('#rm').append(
                        `<option value="${rm.id}">${rm.name}</option>`
                    );
                });
            }

            // 🔹 Store Managers
            if (data.store_managers?.length) {
                data.store_managers.forEach(manager => {
                    $('#store_manager').append(
                        `<option value="${manager.id}">${manager.name}</option>`
                    );
                });
            }

            // 🔹 Users
            if (data.users?.length) {
                data.users.forEach(user => {
                    $('#assigned_to, #allocated_to').append(
                        `<option value="${user.id}">${user.name}</option>`
                    );
                });
            }


            if (selected.rm) {
                $('#rm').val(selected.rm);
            }

            if (selected.store_manager) {
                $('#store_manager').val(selected.store_manager);
            }

            if (selected.assigned_to) {
                $('#assigned_to').val(selected.assigned_to);
            }

            if (selected.allocated_to) {
                $('#allocated_to').val(selected.allocated_to);
            }

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
    $.ajax({
        url: App.getSiteurl() + "project-unit/list",
        type: "post",
        data: { search: search, filter: filter },
        dataType: "json",
        success: function (response) {

            if (response.success) {
                renderunitTable(response.projects);
            }
        }
    });
}

function renderunitTable(projects) {
    let html = '';
    let count = 1;

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
        projects.forEach(project => {
            console.log(project)
            html += `
                            <tr class="hover:bg-gray-50 ${project.is_active == 0 ? 'bg-red-100 bg-opacity-50' : ''}"  >
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="text-sm font-medium text-gray-900">${count}</div>
                                    </div>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">${project.store}</div>
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
                                ${project.is_active == 1 ? `<a onclick="openModal('${project.encrypted_id}')" class="text-blue-600 hover:text-blue-800 mr-3">Edit</a>` : `<span data-id="${project.encrypted_id}" onclick="unlockCategory(this)" class="text-blue-600 hover:text-blue-800 mr-3 cursor-pointer "><i class="bi bi-lock"></i></span>`}
                                </td>
                            </tr>
                        `;
            count++;
        });


        html += `</tbody></table>`;
    }
    $('#projectTable').html(html);
}
//loadClients();

$('#searchInput').on('input', function () {
    let value = $(this).val();
    projects(value);
})
$('#filerStatus').on('change', function () {
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