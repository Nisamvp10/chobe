function openAssignModal() {
    toggleModal('assignModalverify');
}

$(document).on('click', '.autoAssign', function () {

    const btn = $(this);

    if (!confirm("Are you sure you want to auto assign tasks to users?")) {
        return;
    }

    let assignmentMode = $('input[name="assignment_mode"]:checked').val();

    btn.prop('disabled', true).html(
        '<span class="spinner-border spinner-border-sm"></span> Auto Assigning...'
    );

    // Show progress bar
    $('#assignProgressWrapper').show();

    let progress = 0;

    // Fake progress animation while server works
    let progressInterval = setInterval(function () {

        progress += 5;

        if (progress >= 90) {
            clearInterval(progressInterval);
        }

        $('#assignProgressBar')
            .css('width', progress + '%')
            .text(progress + '%');

    }, 300);


    $.ajax({
        url: App.getSiteurl() + 'task/auto-assign-tasks',
        method: 'POST',
        data: { assignmentMode: assignmentMode },
        dataType: 'json',

        success: function (res) {

            clearInterval(progressInterval);

            // Complete progress
            $('#assignProgressBar')
                .css('width', '100%')
                .text('100%');

            setTimeout(function () {

                if (res.success) {
                    toastr.success(res.message);
                    toggleCustomModal('assignModalverify', false);
                    loadTask();
                } else {
                    toastr.error(res.message);
                }

                // Reset
                $('#assignProgressWrapper').hide();
                $('#assignProgressBar').css('width', '0%').text('0%');

            }, 500);
        },

        error: function () {
            toastr.error('Server error. Try again.');
        },

        complete: function () {
            btn.prop('disabled', false).text('Auto Assign');
        }

    });

});