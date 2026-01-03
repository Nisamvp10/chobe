function openAssignModal(){
    toggleModal('assignModalverify');
}

$(document).on('click', '.autoAssign', function () {

    const btn = $(this);

    if (!confirm("Are you sure you want to auto assign tasks to users?")) {
        return; 
    }

    btn.prop('disabled', true).text('Auto Assigning...');
    let assignmentMode = $('input[name="assignment_mode"]:checked').val();
    $.ajax({
        url: App.getSiteurl() + 'task/auto-assign-tasks',
        method: 'POST',
        data: { assignmentMode:assignmentMode},
        dataType: 'json',
        success: function (res) {
            if (res.success) {
                toastr.success(res.message);
                // optionally reload task list
                loadTask();
            } else {
                toastr.error(res.message);
            }
        },
        error: function () {
            alert('Server error. Try again.');
        },
        complete: function () {
            btn.prop('disabled', false).text('Auto Assign');
        }
    });
});
