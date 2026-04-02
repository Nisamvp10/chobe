toastr.options = {
  closeButton: true,
  progressBar: true,
  positionClass: "toast-top-right",
  timeOut: 3000
};

//$(document).ready(function () {
$('.common-select').select2({
  width: '100%',
  placeholder: 'Select',
  allowClear: true,
  selectionCssClass: "pl-3 pr-3 py-2 w-full border border-gray-300 h-auto rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"

});
//})



function openDeleteModal(e, modalId = false) {
  toggleModal(modalId, true);
  $('#confirmDelete').data('id', $(e).data('id'));
}

function toggleCustomModal(modalId, show = true) {
  const modal = document.getElementById(modalId);
  if (!modal) return;

  if (show) {
    modal.classList.remove('hidden');
  } else {
    modal.classList.add('hidden');
  }
}

// Global event delegation for closing modals
document.addEventListener('click', function (e) {
  // Close button clicked
  if (e.target.closest('[data-close]')) {
    const modalId = e.target.closest('[data-close]').getAttribute('data-close');
    toggleCustomModal(modalId, false);
  }

  // Click outside modal content (backdrop)
  if (e.target.classList.contains('wrapModal')) {
    //e.target.classList.add('hidden');
  }
});


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

$(document).on('change', '#projectUnit', function () {
  let clientId = $(this).val();
  let html = '';
  if (clientId) {
    $('#participants').html('<p>Loading...</p>');

    if (!clientId) return;

    fetch(App.getSiteurl() + `api/clients/${clientId}/tasks`)
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          if (data.result.length > 0) {
            data.result.forEach(user => {
              html += `    <div class="staff-wrapper border rounded-md p-3 flex items-center justify-between">
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

function copyText(text, e) {
  navigator.clipboard.writeText(text).then(() => {
    let msg = $(e).data('msg');
    toastr.success(msg);
  });
}