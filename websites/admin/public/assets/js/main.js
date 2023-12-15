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
});
