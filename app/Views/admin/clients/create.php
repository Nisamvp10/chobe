<?php $this->extend('layout/main') ;?>
<?= $this->section('content') ?>
<?php
    if (!empty($data)) {
        $id = encryptor($data['id']);
        $name = $data['name'];
        $email = $data['email'];
        $phone = $data['phone'];
        $join_date = $data['join_date'];
        $notes = $data['note'];
        $profile = $data['profile'];
    }else {
        $id=$name=$email=$phone=$profile=$notes='';
       // $join_date_formatted = date('Y-m-d', strtotime($join_date));
        $join_date = date('Y-m-d');
    }?>
 <!-- titilebar -->
 <div class="flex items-center justify-between">
    <div class="flex items-center gap-4">
        <a href="<?=base_url('clients');?>" class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left text-gray-500">
            <path d="m12 19-7-7 7-7"></path>
            <path d="M19 12H5"></path>
            </svg>
        </a> 
        <h1 class="h3 mb-0"><?= $page ?? '' ?></h1>
    </div>
</div><!-- closee titilebar -->

<!-- body -->
<div class="bg-white rounded-lg shadow-sm overflow-hidden p-4">
    <form id="clientForm" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div >
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <div class="relative">
                        <input type="hidden" name="clientId" value="<?=$id;?>" />
                        <div class="absolute inset-y-0 left-0 pl-3 mt-2 items-center pointer-events-none"><i class="bi bi-person text-xl text-gray-400"></i></div>
                        <input type="text" name="name" value="<?= $name ?>"  id="name" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter Client Name">
                        <div class="invalid-feedback" id="name_error"></div>
                    </div>
                </div>
                

                <!-- <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Profile / IMG URL</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 mt-2 items-center pointer-events-none"><i class="bi bi-image text-xl text-gray-400"></i></div>
                        <input type="file" name="file"  id="file" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter Password">
                        <div class="invalid-feedback" id="file_error"></div>
                    </div>
                </div> -->
               
            </div>

            <!-- 2 -->
            <div class="space-y-4">
                
             
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <div class="relative">
                        <div class="absolute top-3 left-3 pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text text-gray-400"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M10 9H8"></path><path d="M16 13H8"></path><path d="M16 17H8"></path></svg>
                        </div>
                        <textarea name="notes" rows="4" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Add any additional notes about this client"><?=$notes;?></textarea>
                    </div>
                </div>
            </div><!-- close2 -->
        </div>
        <div class="d-inline-block ">
             <div id="contactWrapper">
                    <div class="flex items-center gap-4 contact-row mt-2">
                        <!-- Email -->
                         <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Authorized Personnel</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 mt-2 items-center pointer-events-none">
                                    <i class="bi bi-file-person text-xl text-gray-400"></i>
                                </div>
                                <input type="text" name="authorized_personnel[]" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter Authorized Personnel">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 mt-2 items-center pointer-events-none">
                                    <i class="bi bi-envelope-at text-xl text-gray-400"></i>
                                </div>
                                <input type="email" name="email[]" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter Your Email">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 mt-2 items-center pointer-events-none">
                                    <i class="bi bi-telephone text-xl text-gray-400"></i>
                                </div>
                                <input type="number" name="phone[]" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter Your Phone">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <!-- Designation -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Designation</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 mt-2 items-center pointer-events-none">
                                    <i class="bi bi-file-person text-xl text-gray-400"></i>
                                </div>
                                <input type="text" name="designation[]" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter Designation">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <!-- Remove button -->
                        <button type="button" class="removeRow text-white text-xl font-bold btn btn-danger ">&times;</button>
                    </div>
                    </div>

                    <!-- Add new row button -->
                    <button type="button" id="addContactRow" class="mt-3 px-3 py-2 bg-blue-500 text-white rounded-lg">+ Add Contact</button>
        </div>
         <div class="flex mt-2 justify-end gap-3">
                <a href="<?=base_url('clients');?>" class="border border-gray-300 px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">Cancel</a>
                <?php if(haspermission('','create_client') ) { ?>
                    <button type="submit" id="submitBtn" class="bg-primary-600 px-4 py-2 flex rounded-md hover:bg-primary-700 text-white transition-colors items-center"> <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-save mr-1"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>Save</button>
                <?php } ?>
        </div>
    </form>
</div><!-- close body -->
<?= $this->endSection();?>
<?= $this->section('scripts'); ?>
<script>
    
        $('#clientForm').on('submit', function(e) {
            let webForm = $('#clientForm');
            e.preventDefault();
            let formData = new FormData(this);

            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').empty();

            $('#submitBtn').prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
            );

            $.ajax({
                url : '<?=base_url('client/save');?>',
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


// 
$(document).ready(function () {
    // Add new row
    $('#addContactRow').on('click', function () {
        let newRow = $('#contactWrapper .contact-row:first').clone();
        newRow.find('input').val(''); // clear values
        $('#contactWrapper').append(newRow);
    });

    // Remove row
    $(document).on('click', '.removeRow', function () {
        if ($('#contactWrapper .contact-row').length > 1) {
            $(this).closest('.contact-row').remove();
        } else {
            alert("At least one row is required.");
        }
    });
});

</script>
<?= $this->endSection();?>
