<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>



        <div class="flex items-center justify-between">
            <div class="col-lg-12">
                <div class="d-flex justify-content-between align-items-center mb-0">
                    <h1 class="h3 mb-0">Settings</h1>
                    <div>
                        <a href="<?= base_url('dashboard/tasks/create') ?>" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i> New Task
                        </a>
                    </div>
                </div>
            </div>
        </div><!-- closee titilebar -->

                <!-- Tabs on the Left -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden ">
            <div class="d-flex">
                <div class="w-64 border-r border-gray-200 p-4 bg-gray-50">
                    <nav class="space-y-1" id="settingsTabs" role="tablist" aria-orientation="vertical">
                       
                        <button class="w-full flex items-center space-x-3 px-3 py-2 rounded-md transition-colors  text-primary-700 active" id="general-tab" data-bs-toggle="pill" data-bs-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true">
                            <span class="flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-building "><rect width="16" height="20" x="4" y="2" rx="2" ry="2"></rect><path d="M9 22v-4h6v4"></path><path d="M8 6h.01"></path><path d="M16 6h.01"></path><path d="M12 6h.01"></path><path d="M12 10h.01"></path><path d="M12 14h.01"></path><path d="M16 10h.01"></path><path d="M16 14h.01"></path><path d="M8 10h.01"></path><path d="M8 14h.01"></path></svg>
                            </span>
                            <span>General</span>
                        </button>

                        <!-- Email Config Tab -->
                        <button  class="w-full flex items-center space-x-3 px-3 py-2 rounded-md transition-colors hover:bg-primary-50 hover:text-primary-700" id="email-tab" data-bs-toggle="pill" data-bs-target="#emailtab" type="button" role="tab" aria-controls="email" aria-selected="false">
                            <span class="flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mail "><rect width="20" height="16" x="2" y="4" rx="2"></rect><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path></svg>
                            </span>
                            <span>Email Config</span>
                        </button>
                    </nav>
                </div>
                    <!-- Content -->
                <div class="flex-1 p-6">
                    <div class="space-y-6 tab-content">

                    <div class="tab-pane fade fade show active mt-0" id="general" role="tabpanel" aria-labelledby="general-tab">
                        <h2 class="text-lg font-medium text-gray-800 mb-4">General Settings</h2>
                        <form class="space-y-4" id="settingsForm" >
                            <?= csrf_field() ;?>
                            <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Business Name</label>
                            <input type="text" name="company_name" value="<?= getappdata('company_name') ?>" id="company_name" class="border rounded px-3 py-2 w-full" placeholder="Enter business name">
                            <div class="invalid-feedback" id="company_name_error"></div>
                            </div>
                            <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <input type="text" name="address" value="<?= getappdata('address') ?>" id="address" class="border rounded px-3 py-2 w-full" placeholder="Enter address">
                            <div class="invalid-feedback" id="address_error"></div>
                            <div class="grid grid-cols-2 mt-2 gap-4">
                                <div>
                                    <input type="text"  value="<?= getappdata('city') ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-300" placeholder="City" name="city">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <input type="text"  value="<?= getappdata('state') ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:right:2 focus:ring-primary-300" placeholder="State" name="state">
                                    <input type="text"  value="<?= getappdata('zip_code') ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:right:2 focus:ring-primary-300" placeholder="Zip Code" name="zip_code">

                                </div>
                            </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                <input type="text"  value="<?= getappdata('phone') ?>" name="phone" id="phone"  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-300" placeholder="Country code with Number">
                                <div class="invalid-feedback" id="phone_error"></div>
                            </div>
                            <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email"   value="<?= getappdata('email') ?>"class="border rounded px-3 py-2 w-full" name="email" id="email" placeholder="Enter email">
                            <div class="invalid-feedback" id="email_error"></div>
                            </div>
                            <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Change Logo</label>
                            <input type="file" class="border rounded px-3 py-2 w-full">
                            </div>
                            <div class="flex justify-end"><button id="submitBtn" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md flex items-center transition-colors"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-save mr-1"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>Save Changes</button></div>
                        </form>
                        </div>

                        <!-- Email Config Tab Content -->
                        <div class="tab-pane fade mt-0" id="emailtab" role="tabpanel" aria-labelledby="email-tab">

                        <h2 class="text-lg font-medium text-gray-800 mb-4 ">Email Configuration</h2>
                        <form class="space-y-4">
                            <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Host</label>
                            <input type="text" class="border rounded px-3 py-2 w-full" placeholder="smtp.example.com">
                            </div>
                            <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Port</label>
                            <input type="text" class="border rounded px-3 py-2 w-full" placeholder="587">
                            </div>
                            <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Username</label>
                            <input type="text" class="border rounded px-3 py-2 w-full" placeholder="user@example.com">
                            </div>
                            <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Password</label>
                            <input type="password" class="border rounded px-3 py-2 w-full" placeholder="Password">
                            </div>
                            <button class="bg-primary-700 text-white px-4 py-2 rounded hover:bg-primary-800">Save</button>
                        </form>
                        </div>
                    
                    </div>
                </div>
                <!-- close content -->
            </div>
        </div>





<?= $this->endSection() ?>

<?= $this->section('scripts'); ?>
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script> -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.js"></script> -->

    <script>
    $(document).ready(function() {
        // Configure toastr
      

        $('#settingsForm').on('submit', function(e) {
            e.preventDefault();
            
            // Reset previous error states
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').empty();
            
            // Disable submit button
            $('#submitBtn').prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
            );

            $.ajax({
                url: '<?= base_url('settings/save') ?>',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $('#settingsForm')[0].reset();
                    } else {
                        if (response.errors) {
                            // Display validation errors
                            $.each(response.errors, function(field, message) {
                                $('#' + field).addClass('is-invalid');
                                $('#' + field + '_error').text(message);
                            });
                            toastr.error('Please check the form for errors');
                        } else {
                            toastr.error(response.message);
                        }
                    }
                },
                error: function() {
                    toastr.error('An error occurred while saving settings');
                },
                complete: function() {
                    // Re-enable submit button
                    $('#submitBtn').prop('disabled', false).text('Save Settings');
                }
            });
        });
    });
    </script>


<?= $this->endSection() ?>
