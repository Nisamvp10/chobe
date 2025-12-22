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
                        <select name="role[]" class="role-select hidden mt-2 md:mt-0 border rounded px-2 py-1 text-sm" data-id="${client.id}">
                            <option value="participant" selected>Participant</option>
                            <option value="team_leader">Team Leader</option>
                            <option value="team_coordinator">Team Coordinator</option>
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


$('#taskCreate').on('submit', function(e) {

    let webForm = $('#taskCreate');
    e.preventDefault();
    let formData = new FormData(this);
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').empty();
    $('#submitBtn').prop('disabled', true).html(
        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
    );
    $.ajax({
        url : App.getSiteurl()+'task/save',
        method:'POST',
        data: formData,
        contentType: false,
        processData: false,
        success:function(response)
        { 
            if(response.success){
                toastr.success(response.message);
                webForm[0].reset();
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
            toastr.error('An error occurred while saving Service');
        },
        complete: function() {
            // Re-enable submit button
            $('#submitBtn').prop('disabled', false).text('Save Branch');
        }
    })
})
