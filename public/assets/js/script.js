toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: "toast-top-right",
    timeOut: 3000
};

function toggleModal(modalId, show = true) {
  const modal = document.getElementById(modalId);
  if (!modal) return;
  
  if (show) {
    modal.classList.remove('hidden');
  } else {
    modal.classList.add('hidden');
  }
}

document.addEventListener('click', function (e) {
  // Close button clicked
  if (e.target.closest('[data-close]')) {
    const modalId = e.target.closest('[data-close]').getAttribute('data-close');
    toggleModal(modalId, false);
  }

  // Click outside modal content (backdrop)
  if (e.target.classList.contains('wrapModal')) {
    //e.target.classList.add('hidden');
  }
});

$(document).on('change','#projectUnit',function(){
  let clientId = $(this).val();
  let html ='';
  if(clientId) {
    $('#participants').html('<p>Loading...</p>');

    if (!clientId) return;

    fetch(App.getSiteurl()+`api/clients/${clientId}/tasks`)
        .then(res => res.json())
        .then(data => {
          if(data.success) {
            if(data.result.length > 0) {
              data.result.forEach(user => {
                  html +=`    <div class="staff-wrapper border rounded-md p-3 flex items-center justify-between">
                                <div class="flex items-center space-x-2 gap-2">
                                    <input type="checkbox" name="staff[]" ${(user.isAssigned ? 'checked' : '')}  ${(user.isTemp ? 'checked' : '')} class="staff-checkbox" data-id="${user.userId}" value="${user.userId}" id="staff-${user.userId}">
                                    <label for="staff-1">${user.name}</label>
                                </div>
                            </div>`;
              })
              $('#participants').html(html);

            }
          }
            // Reset dropdowns
           

        })
        .catch(err => {
            console.error('Error fetching data:', err);
        });
  }
})

function copyText(text) {
    navigator.clipboard.writeText(text).then(() => {
        toastr.success('client ID copied to clipboard');
    });
}