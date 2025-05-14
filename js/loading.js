document.addEventListener("DOMContentLoaded", function () {
  const loaderContainer = document.querySelector(".loader-container");
  const offlineMessage = document.querySelector(".offline-message");
  const content = document.querySelector(".content");

  function updateOnlineStatus() {
    if (navigator.onLine) {
      offlineMessage.style.display = "none";
      // Simulate loading time
      setTimeout(() => {
        loaderContainer.style.display = "none";
        content.style.display = "block";
        window.location.href = "index.php";
      }, 5000);
    } else {
      loaderContainer.style.display = "none";
      offlineMessage.style.display = "block";
      content.style.display = "none";
    }
  }

  // Show loader initially
  loaderContainer.style.display = "flex";

  // Listen for online/offline events
  window.addEventListener("online", updateOnlineStatus);
  window.addEventListener("offline", updateOnlineStatus);

  // Check initial status
  updateOnlineStatus();
});
