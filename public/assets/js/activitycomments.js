document.addEventListener('submit', function (e) {

    if (!e.target.classList.contains('comment-form')) return;

    e.preventDefault();

    const form = e.target;
    const taskId = form.dataset.taskId;
    const activityId = form.dataset.activityId;
    const textarea = form.querySelector('.comment-text');
    const comment = textarea.value.trim();
    const submitBtn = form.querySelector('button[type="submit"]');

    if (!comment) {
        alert('Comment cannot be empty');
        return;
    }

    submitBtn.disabled = true;

    $.ajax({
        url: App.getSiteurl() + 'activity/save-comment',
        method: 'POST',
        data: {
            taskId: taskId,
            activityId: activityId,
            comment: comment
        },
        dataType: 'json',
        success: function (res) {

            if (res.success) {
                textarea.value = '';
                loadTask();
               toastr.success(res.message);
            } else {
                toastr.error('Failed to save comment');
            }
        },
        error: function () {
            alert('Server error');
        },
        complete: function () {
            submitBtn.disabled = false;
        }
    });

});
