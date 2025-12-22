$('#phone').on('keyup', function () {
        let query = $(this).val();

        if (query.length >= 3) { 
            $.ajax({
                url: App.getSiteurl()+"clients/suggestPhone",
                method: "POST",
                data: { phone: query },
                success: function (res) {
                    let html = '';
                    if (res.length > 0) {
                        res.forEach(client => {
                            html += `<div class="suggest-item px-3 py-2 cursor-pointer hover:bg-gray-100" 
                                        data-phone="${client.phone}" 
                                        data-name="${client.name}">
                                        ${client.phone} - ${client.name}
                                     </div>`;
                        });
                        $('#suggestions').html(html).removeClass('hidden');
                    } else {
                        $('#suggestions').html('<div class="px-3 py-2 text-gray-400">No match found</div>').removeClass('hidden');
                    }
                }
            });
        } else {
            $('#suggestions').addClass('hidden').empty();
        }
    });

    // Handle click on a suggestion
    $(document).on('click', '.suggest-item', function () {
        const phone = $(this).data('phone');
        const name = $(this).data('name');

        $('#phone').val(phone);
        $('#client_name').val(name); // Optional if you have name field
        $('#suggestions').addClass('hidden').empty();
    });

    // Hide suggestion when clicking outside
    $(document).click(function (e) {
        if (!$(e.target).closest('#phone, #suggestions').length) {
            $('#suggestions').addClass('hidden').empty();
        }
    });

    // preferance 
    
const dropdown = document.getElementById('serviceDropdown');
const addBtn = document.getElementById('addServiceBtn');
const tagContainer = document.getElementById('serviceTags');
const submitButton = document.getElementById('submitBtn');

let totalSpend = 0;

function updateTotalSpend(amount) {
    totalSpend += amount;
    submitButton.textContent = `Save - Total: ${totalSpend}`;
}

  addBtn.addEventListener('click', () => {
    const value = dropdown.value;
   
    const selectedOption = dropdown.options[dropdown.selectedIndex];

    if (!value) return;

    const text = selectedOption.text;
    const spend = parseFloat(selectedOption.dataset.spend) || 0;

    dropdown.querySelector(`option[value="${value}"]`).remove();
    dropdown.value = "";

    const tag = document.createElement('div');
    tag.className = 'inline-flex items-center bg-blue-50 text-blue-700 rounded-full px-3 py-1 text-sm';
    tag.dataset.value = value;
    tag.dataset.spend = spend
    tag.innerHTML = `
      ${text}
      <button type="button" class="ml-1 text-blue-700 hover:text-blue-900 removeBtn" data-value="${value}" data-spend="${spend}" data-text="${text}">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x "><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg>
      </button>
      <input type="hidden" name="specialties[]" value="${value}">
    `;
    tagContainer.appendChild(tag);
    updateTotalSpend(spend)
  });

  // Remove tag and restore dropdown option
  tagContainer.addEventListener('click', (e) => {
    const btn = e.target.closest('.removeBtn');
    if (btn) {
      const tag = btn.closest('.inline-flex');
      const value = btn.dataset.value;
      const text = btn.dataset.text;
      const spend = parseFloat(btn.dataset.spend) || 0;
      // Remove tag
      tag.remove();

      // Add back to dropdown
      const option = document.createElement('option');
      option.value = value;
      option.textContent = text;
      option.setAttribute('data-spend',spend);
      dropdown.appendChild(option);
     
      // Optional: sort dropdown alphabetically
      const options = Array.from(dropdown.options).slice(1); // skip first (placeholder)
      options.sort((a, b) => a.text.localeCompare(b.text));
      options.forEach(option => dropdown.appendChild(option));
      updateTotalSpend(-spend);
    }
  });