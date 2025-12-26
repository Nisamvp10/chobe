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
