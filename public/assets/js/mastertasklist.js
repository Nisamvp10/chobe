masterTaskList();

function masterTaskList() {
    let search = $('#searchInput').val();
    $.ajax({
        url: App.getSiteurl() + "master-tasks/list",
        type: "post",
        data: { search: search },
        dataType: "json",
        success: function (response) {
            if (response.success) {
                renderMasterTaskTable(response.data);
            }
        }
    });
}

function renderMasterTaskTable(data) {
    let tableBody = $('#masterTaskTableBody');
    let html = '';
    if (data.length == 0) {
        html += `<div class="col-12 text-center">No Data Found</div>`;
    } else {
        html += `
          <table class="min-w-full divide-y divide-gray-200 border bg-gray-100">
                <thead>
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">S.NO</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th> 
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th> 
                    </tr>
                </thead>
        <tbody class="bg-white divide-y divide-gray-200">`;
        let i = 1;
        data.forEach(element => {
            html += `<tr>
                        <td class="px-2 py-2 whitespace-nowrap" >${i}</td>
                        <td class="px-2 py-2 whitespace-nowrap" >${element.title}</td>
                        <td class="px-2 py-2 whitespace-nowrap" >${element.description}</td>
                        <td class="px-2 py-2 whitespace-nowrap" >
                            <a href="${App.getSiteurl()}master-task/edit/${element.id}" class="btn btn-primary btn-sm">Edit</a>
                            <a href="javascript:void(0)" class="btn btn-danger btn-sm" onclick="deleteMasterTask('${element.id}')">Delete</a>
                        </td>
                    </tr>`;
            i++;
        });
    }
    html += `</tbody></table>`;
    tableBody.html(html);
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
        url: App.getSiteurl() + 'master-task/save',
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
function deleteMasterTask(id) {
    if (confirm('Are you sure you want to delete this master task?')) {
        $.ajax({
            url: App.getSiteurl() + 'master-task/delete/' + id,
            method: 'POST',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message);
                    masterTaskList();
                } else {
                    toastr.error(response.message);
                }
            }
        })
    }
}