
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
        url : App.getSiteurl()+'activities/save',
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
