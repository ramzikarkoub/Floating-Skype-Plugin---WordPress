document.addEventListener("DOMContentLoaded", function () {
  const tabs = document.querySelectorAll(".nav-tab");
  const tabContents = document.querySelectorAll(".settings-tab");

  tabs.forEach((tab) => {
    tab.addEventListener("click", function (e) {
      e.preventDefault();
      tabs.forEach((t) => t.classList.remove("nav-tab-active"));
      tabContents.forEach((content) => (content.style.display = "none"));

      this.classList.add("nav-tab-active");
      document.querySelector(this.getAttribute("href")).style.display = "block";
    });
  });

  // Show the first tab by default
  tabs[0].click();
});
