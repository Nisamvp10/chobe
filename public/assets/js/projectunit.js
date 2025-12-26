$('#client').on('change', function () {

    let clientId = $(this).val();
     $('#rm').html('loading...');
    if (!clientId) return;

    fetch(`api/clients/${clientId}/projects`)
        .then(res => res.json())
        .then(data => {

            // Clear dropdowns
            $('#rm').html('<option value="">Select RM</option>');
            $('#store_manager').html('<option value="">Select Store Manager</option>');

            // RMs
            if (data.rms && data.rms.length > 0) {
                data.rms.forEach(rm => {
                    $('#rm').append(`
                        <option value="${rm.id}">
                            ${rm.name}
                        </option>
                    `);
                });
            }

            // Store Managers
            if (data.rms && data.rms.length > 0) {
                data.rms.forEach(manager => {
                    $('#store_manager').append(`
                        <option value="${manager.id}">
                            ${manager.name}
                        </option>
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