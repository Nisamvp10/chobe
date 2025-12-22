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
        $id=$name=$email=$phone=$join_date=$profile=$notes='';
    }?>
 <!-- titilebar -->
 <div class="flex items-center justify-between">
    <div class="flex items-center gap-4">
        <a href="<?=base_url('appointments');?>" class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
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
    <form id="bookingForm" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div >
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <div class="relative">
                        <input type="hidden" name="clientId" value="<?=$id;?>" />
                        <div class="absolute inset-y-0 left-0 pl-3 mt-2 items-center pointer-events-none"><i class="bi bi-telephone text-xl text-gray-400"></i></div>
                        <input type="number" name="phone" id="phone" placeholder="Enter phone number" autocomplete="off" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter Client PH Number">
                        <div id="suggestions" class="absolute bg-white border border-gray-300 w-full z-1 rounded-2 hidden"></div>
                        <div class="invalid-feedback" id="phone_error"></div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Specialties</label>
                    <div id="serviceTags" class="flex flex-wrap gap-2 mb-2">
                    </div>
                    <!-- Dropdown + Add button -->
                    <div class="flex">
                        <select id="serviceDropdown" id="specialties" class="flex-1 py-2 px-3 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select a service</option>
                        <?php
                        if ($services) {
                            foreach ($services as $items) {
                            ?>
                                <option value="<?= $items['id'] ?>" data-spend="<?= $items['price'] ?>"><?= $items['name'] ?></option>
                            <?php
                                
                            }
                        } ?>
                        </select>

                        <button type="button" id="addServiceBtn" class="px-3 py-2 bg-primary-600 text-white rounded-r-lg hover:bg-blue-700 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus "><path d="M5 12h14"></path><path d="M12 5v14"></path></svg>
                        </button>
                        <div class="invalid-feedback" id="specialties_error"></div>
                    </div>
                      
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 capitalize">Staff</label>
                    <div class="relative">
                         <div class="absolute inset-y-0 left-0 pl-3 mt-2 items-center pointer-events-none"><i class="bi bi-person text-xl text-gray-400"></i></div>
                        <select id="staff" name="staff" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select a Staff</option>
                         <?php
                        if ($usersStaff) {
                            foreach ($usersStaff as $staff) {
                            ?>
                                <option value="<?= $staff['id'] ?>" ><?= $staff['name'] ?></option>
                            <?php
                                
                            }
                        } ?>
                        </select>
                        <div class="invalid-feedback" id="staff_error"></div>
                    </div>
                </div>
            </div>

            <!-- 2 -->
            <div class="space-y-4">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                   <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Booking Date</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 mt-2 items-center pointer-events-none"><i class="bi bi-calendar text-xl text-gray-400"></i></div>
                            <input type="date" value=""  name="booking_date"  id="booking_date" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter Hire Date">
                            <div class="invalid-feedback" id="booking_date_error"></div>
                        </div>
                    </div>
                     <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Booking Time</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 mt-2 items-center pointer-events-none"><i class="bi bi-calendar text-xl text-gray-400"></i></div>
                            <input type="time" value=""  name="booking_time" min="09:00" max="21:00"  id="booking_time" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter Hire Date">
                            <div class="invalid-feedback" id="booking_time_error"></div>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 capitalize">Booking Status</label>
                    <div class="responseive">
                        <select id="booking_status" name="booking_status" class="pl-3 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">">
                            <option value="">Select a Staff</option>
                            <option value="1">Confirm</option>
                            <option value="2">Completed</option>
                            <option value="3">Pending</option>
                            <option value="4">Decline</option>
                        </select>
                        <div class="invalid-feedback" id="booking_status_error"></div>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <div class="relative">
                        <div class="absolute top-3 left-3 pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text text-gray-400"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M10 9H8"></path><path d="M16 13H8"></path><path d="M16 17H8"></path></svg>
                        </div>
                        <textarea name="notes" rows="4" class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Add any additional notes about this client"><?=$notes;?></textarea>
                    </div>
                </div>
            </div><!-- close2 -->
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
<script src="<?=base_url('public/assets/js/booking.js');?>"></script>
<script>
    
        $('#bookingForm').on('submit', function(e) {
            let webForm = $('#bookingForm');
            e.preventDefault();
            let formData = new FormData(this);

            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').empty();

            $('#submitBtn').prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
            );

            $.ajax({
                url : '<?=base_url('booking/save');?>',
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
document.getElementById("booking_time").addEventListener("change", function () {
    const selectedTime = this.value;
    if (selectedTime < "09:00" || selectedTime > "21:00") {
        alert("Please select a time between 9:00 AM and 9:00 PM.");
        this.value = "";
    }
});

</script>
<?= $this->endSection();?>
