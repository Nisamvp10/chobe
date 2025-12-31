$('#client').on('change', function () {

    let clientId = $(this).val();

    $('#rm').html('<option>Loading...</option>');
    $('#assigned_to').html('<option>Loading...</option>');
    $('#allocated_to').html('<option>Loading...</option>');
    $('#store_manager').html('<option>Loading...</option>');

    if (!clientId) return;

    fetch(`api/clients/${clientId}/projects`)
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
                    $('#rm').append(`
                        <option value="${rm.id}">${rm.name}</option>
                    `);
                    
                });
            }

            // 🔹 Store Managers
            if (data.store_managers?.length) {
                data.store_managers.forEach(manager => {
                    $('#store_manager').append(`
                        <option value="${manager.id}">${manager.name}</option>
                    `);
                });
            }

            if (data.users?.length) {
                data.users.forEach(user => {
                    $('#assigned_to, #allocated_to').append(`
                        <option value="${user.id}">${user.name}</option>
                    `);
                });
            }

        })
        .catch(err => {
            console.error('Error fetching data:', err);
        });
});

function openModalbulk($id=false) {
    toggleModal('bulkUnit', true);
    $('#bulkUnit .head').text($id ? 'Edit Bulk Units' : 'Add Bulk Units');
}
$(document).on('submit', '#unitBulkForm', function (e) {

    e.preventDefault();
    let webForm = $(this);
    let formData = new FormData(this);
    let butn = $(webForm).find('button[type="submit"]');
    butn.attr('disabled',true).html('Processing..');
     $.ajax({
        url : App.getSiteurl() +'projectunit/bulk-upload',
        method:'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType : 'json',

        success:function(response)
        { 
            if(response.success) {
                 toastr.success(response.message);
                 butn.attr('disabled',false).html('Save Changes');
            }else{
                 toastr.error(response.message);
                 butn.attr('disabled',false).html('Save Changes');
            }
        },error:function(err){
            console.log(err);
        },completed:function(){
            butn.attr('disabled',false).html('Save Changes');
        }
    })
})
  document.getElementById('chooseFileBtn').addEventListener('click', function () {
        document.getElementById('staff_excel').click();
    });