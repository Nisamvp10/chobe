function openModal(id = false) {
    toggleModal('activities', true);
    let mdal = $('#activities');
    mdal.find('.headd').text(id ? 'Edit Activity Task' : 'Add Activity Task');
    let webForm = document.getElementById('taskCreate');
    webForm.querySelector('#activityId').value = '';
    webForm.querySelector('#title').value = '';
    webForm.querySelector('#description').value = '';
    $('#taskId').val();
    if (id) {
        fetch(App.getSiteurl() + `api/get-activity/${id}`)
            .then(res => res.json())
            .then(data => {
                console.log(data.result.task_id);
                if (data.success) {
                    webForm.querySelector('#activityId').value = data.result.id;
                    webForm.querySelector('#title').value = data.result.activity_title;
                    webForm.querySelector('#description').value = data.result.activity_description;
                    $('#taskId').val(data.result.task_id ?? '').trigger('change');
                }
            })
    }
}


$('#taskCreate').on('submit', function (e) {

    let webForm = $('#taskCreate');
    e.preventDefault();
    let formData = new FormData(this);
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').empty();
    $('#submitBtn').prop('disabled', true).html(
        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
    );
    $.ajax({
        url: App.getSiteurl() + 'activities/save',
        method: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            if (response.success) {
                toastr.success(response.message);
                setTimeout(() => {
                    toggleCustomModal('activities', false);
                    webForm[0].reset();
                    allactivities();
                }, 1000);
            } else {
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
            toastr.error('An error occurred while saving Service');
        },
        complete: function () {
            // Re-enable submit button
            $('#submitBtn').prop('disabled', false).text('Save');
        }
    })
})

$('.activity-tasks').on('change', function () {
    allactivities();
    let activityTaskId = $(this).val();
    if (activityTaskId) {
        $.ajax({
            url: App.getSiteurl() + 'activitties/getstaffbytask',
            method: "POST",
            data: { ataskId: activityTaskId },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    renderStaffList(response.staffs);
                }
            }
        })
    }
})

function renderStaffList(staffs) {
    html = '';
    if (staffs.length > 0) {
        staffs.forEach(staff => {
            html += ` <div class="staff-wrapper border rounded-md p-3 flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <input type="checkbox" name="staff[]" class="staff-checkbox" data-id="1" value="${staff.id}" id="staff-1">
                            <label for="staff-1">${staff.name}</label>
                        </div>
                    </div>`;
        });
    } else {
        html = '<p>No Staff Found</p>'
    }
    $('#participantsactivityTasks').html(html);
}


document.addEventListener('change', async (e) => {
    if (e.target.closest('.masetrTask')) {
        let html = '';
        const id = e.target.value;
        $.ajax({
            url: App.getSiteurl() + 'master-task/get-activities',
            method: 'POST',
            data: { id: id },
            success: function (response) {
                if (response.success) {
                    response.activities.forEach(activity => {
                        html += `<option value="${activity.id}">${activity.title}</option>  `;
                    });
                    $('#activities').append(html);
                }
            }
        })
    }
})

$('#commentActivities').on('submit', function (e) {
    e.preventDefault();
    let formData = new FormData(this);
    let webForm = $('#commentActivities');
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').empty();
    $('#submitBtn').prop('disabled', true).html(
        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
    );
    $.ajax({
        url: App.getSiteurl() + 'comment-group-activities/save',
        method: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            if (response.success) {
                toastr.success(response.message);
                webForm[0].reset();
            } else {
                if (response.msg_errors) {
                    response.msg_errors.forEach(msg => {
                        toastr.error(msg);
                    });
                }
                else if (response.errors) {
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
        }, complete: function () {
            // Re-enable submit button
            $('#submitBtn').prop('disabled', false).text('Save');
        }
    })
})
