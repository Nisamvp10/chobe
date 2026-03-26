$('#branch').on('change', function () {
    let val = $(this).val();

    $.ajax({
        url: App.getSiteurl() + "branch-staff",
        method: "POST",
        data: { branch: val },
        success: function (res) {
            let html = '';
            if (res.branches.length > 0) {
                res.branches.forEach(client => {
                    html += `
                    <div class="staff-wrapper border rounded-md p-3 flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <input type="checkbox" name="staff[]" class="staff-checkbox" data-id="${client.id}" value="${client.id}" id="staff-${client.id}">
                            <label for="staff-${client.id}">${client.name} (${client.role})</label>
                        </div>
                        <select name="role[]" class="role-select hidden mt-2 md:mt-0 border rounded px-2 py-1 text-sm " data-id="${client.id}">
                            <option value="participant" selected>Participant</option>
                            <option value="team_leader">Team Leader</option>
                            <option value="team_coordinator">Team Coordinator</option>
                        </select>
                         <select name="personpriority[]" class="role-select hidden mt-2 md:mt-0 border rounded px-2 py-1 text-sm hidden d-none" data-id="${client.id}">
                            <option desabled value="">Select a Priority</option>
                            <option value="1">High</option>
                            <option selected value="2">Medium</option>
                            <option value="3">Low</option>
                        </select>
                    </div>`;
                });

                $('#participants').html(html).removeClass('hidden');
            } else {
                $('#participants').html('<div class="px-3 py-2 text-gray-400">No Participants found</div>').removeClass('hidden');
            }
        }
    });
});

// Show/hide role select on checkbox toggle
$(document).on('change', '.staff-checkbox', function () {
    const id = $(this).data('id');
    const isChecked = $(this).is(':checked');
    const $select = $(`.role-select[data-id="${id}"]`);

    if (isChecked) {
        $select.removeClass('hidden');
    } else {
        $select.addClass('hidden');
    }
});


$('#taskCreate').on('submit', function (e) {

    let webForm = $('#taskCreate');
    e.preventDefault();
    let formData = new FormData(this);
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').empty();
    $('#submitBtn').prop('disabled', true).html(
        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
    );
    let projectunits = [];
    $('.activity-checkbox:checked').each(function () {
        projectunits.push($(this).val());
    });
    formData.append('projectUnit', projectunits);
    $.ajax({
        url: App.getSiteurl() + 'task/save',
        method: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            if (response.success) {
                toastr.success(response.message);
                webForm[0].reset();
            } else {
                if (response.errors) {
                    if (response.errors.projectUnit) {
                        toastr.error(response.errors.projectUnit + 'Please select at least one project unit');
                    }
                    $.each(response.errors, function (field, message) {
                        $('#' + field).addClass('is-invalid');
                        $('#' + field + '_error').text(message);
                    })
                } else {
                    toastr.error(response.message);
                }
            }
        }, error: function () {
            toastr.error('An error occurred while saving Service');
        },
        complete: function () {
            // Re-enable submit button
            $('#submitBtn').prop('disabled', false).text('Save ');
        }
    })
})

document.addEventListener('click', async (e) => {
    if (e.target.closest('.masetrTask')) {
        let html = '';
        const id = e.target.value;
        $.ajax({
            url: App.getSiteurl() + 'master-task/get-projectunits',
            method: 'POST',
            data: { id: id },
            success: function (response) {
                if (response.success) {
                    $('#projectunitCount').text('(' + response.projectunits.length + ')');
                    response.projectunits.forEach(projectunit => {
                        //this data appends in task/create page form submit this tadat note get 
                        html += `
                        <div class="activity-wrapper border rounded-md p-3 flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <input type="checkbox" name="activity[]" class="activity-checkbox hidden" data-id="${projectunit.id}" value="${projectunit.id}" id="activity-${projectunit.id}">
                                <div class="w-[40px] h-[40px] bg-primary-light p-2 rounded-full flex items-center justify-center text-white"><input type="checkbox" name="projectunit[]" class="activity-checkbox w-[20px] h-[20px]" value="${projectunit.id}" id="projectunit-${projectunit.id}"></div>
                                <label for="activity-${projectunit.id}">${projectunit.store}</label>
                            </div>
                        </div>`;
                    });
                    $('#activities').html(html);
                }
                else {
                    $('#projectunitCount').text('(0)');
                }
            }
        })
    }
})