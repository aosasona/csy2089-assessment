document.addEventListener("DOMContentLoaded", function() {
  // Watch all items with [data-confirm] attribute for click events and ask for confirmation accordingly
  const confirmLinks = document.querySelectorAll("a[data-confirm]");
  confirmLinks.forEach(function(link) {
    link.addEventListener("click", function(e) {
      if (!confirm(link.dataset.confirm)) {
        e.preventDefault();
      }
    });
  });

  let checked = false;
  const selectAllPermissons = document.querySelector("#toggle-select-all-permissions");
  if (selectAllPermissons) {
    selectAllPermissons.addEventListener("click", function(e) {
      e.preventDefault();
      const checkboxes = document.querySelectorAll("[name='permissions[]']");
      checkboxes.forEach(function(checkbox) {
        checkbox.checked = !checked;
      });

      if (checked) {
        selectAllPermissons.textContent = "(select all)";
      } else {
        selectAllPermissons.textContent = "(deselect all)";
      }

      checked = !checked;
    });
  }
});
