<?php $this->extend('layout/main') ;?>
<?= $this->section('content') ?>
<?php
    if (!empty($data)) {
        $id = encryptor($data['id']);
        $name = $data['name'];
        $email = $data['email'];
        $phone = $data['phone'];
        $position = $data['position'];
        $hire_date = $data['hire_date'];
        $booking_status = $data['booking_status'];
        $erole = $data['role'];
        $ebranch = $data['store_id'];
        $profile = $data['profileimg'];
    }else {
        $id=$name=$email=$phone=$position=$hire_date=$booking_status=$erole=$ebranch=$profile ='';
    }?>
 <!-- titilebar -->
 <div class="flex items-center justify-between">
    <div class="col-lg-12">
        <div class="d-flex justify-content-between align-items-center mb-0">
            <a href="<?=base_url('staff/create');?>" class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left text-gray-500">
                <path d="m12 19-7-7 7-7"></path>
                <path d="M19 12H5"></path>
                </svg>
            </a> 
            <h1 class="h3 mb-0 text-left"><?= $page ?? '' ?></h1>
            <?php
            if(haspermission(session('user_data')['role'],'create_task')) { ?>
                <div>
                    <a href="<?= base_url('staff/create') ?>" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Add Team
                    </a>
                </div>
            <?php } ?>
        </div>
    </div>
</div><!-- closee titilebar -->

<!-- body -->
<div class="bg-white rounded-lg shadow-sm overflow-hidden p-4">
    <form id="staffForm" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
            <div class="space-y-4 bulkCard mb-3">
                <div>
                    <label class="block cursor-pointer">
                        <!-- Hidden file input -->
                        <input type="file" id="staff_excel" name="staff_excel" accept=".xlsx,.xls" class="hidden">

                        <!-- Icon -->
                        <div class="mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-upload h-12 w-12 text-gray-400 mx-auto mb-4">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" x2="12" y1="3" y2="15"></line>
                            </svg>
                        </div>

                        <!-- Title -->
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Upload Sales Data</h3>

                        <!-- Trigger Button -->
                        <button type="button" id="chooseFileBtn"
                            class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 bg-primary text-primary bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white h-10 px-4 py-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-upload h-4 w-4 mr-2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" x2="12" y1="3" y2="15"></line>
                            </svg>
                            Choose Excel File
                        </button>

                        <p class="text-gray-500 mt-2">Supported formats: .xlsx, .xls</p>
                    </label>
                </div>
            </div>
        </div>

         <div class="flex mt-2 justify-end gap-3">
                <a href="<?=base_url('staff');?>" class="border border-gray-300 px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">Cancel</a>
                <?php if(haspermission(session('user_data')['role'],'create_staff') ) { ?>
                    <button type="submit" id="submitBtn" class="bg-primary-600 px-4 py-2 flex rounded-md hover:bg-primary-700 text-white transition-colors items-center"> <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-save mr-1"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>Save</button>
                <?php } ?>
        </div>
    </form>
</div><!-- close body -->
<?= $this->endSection();?>
<?= $this->section('scripts'); ?>
<script>
       $('#staffForm').on('submit', function(e) {
        e.preventDefault();

        let webForm = $('#staffForm');
        let fileInput = $('#staff_excel');
        let formData = new FormData(this);

        // Clear previous errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').empty();

        // Validation before send
        if (!fileInput.val()) {
            toastr.error('Please select an Excel file.');
            return;
        }

        $('#submitBtn').prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Uploading...'
        );

        $.ajax({
            url: '<?= base_url('staff-upload/uploadExcel'); ?>',
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.success) {
                    if (response.inserted && Array.isArray(response.inserted)) {
                        response.inserted.forEach(function(msg) {
                            toastr.success(msg);
                        });
                    } else {
                        toastr.success(response.message);
                    }
                    webForm[0].reset();
                } else {
                    if (response.errors) {
                        $.each(response.errors, function(field, message) {
                            $('#' + field).addClass('is-invalid');
                            $('#' + field + '_error').text(message);
                        });
                    } else {
                        toastr.error(response.message);
                    }
                }
            },
            error: function() {
                toastr.error('An error occurred while uploading the file.');
            },
            complete: function() {
                $('#submitBtn').prop('disabled', false).text('Upload File');
            }
        });
    });


    document.getElementById('chooseFileBtn').addEventListener('click', function () {
        document.getElementById('staff_excel').click();
    });
</script>
<?= $this->endSection();?>
