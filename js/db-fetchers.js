document.addEventListener("DOMContentLoaded", function () {
  fetch("phpp/dashboard/TeachEnrollCount.php")
    .then((response) => response.json())
    .then((data) => {
      console.log(data.total_enrollees);
      console.log(data.total_employees);
      document.getElementById("totalEnrollees").textContent =
        data.total_enrollees;
      document.getElementById("totalEmployees").textContent =
        data.total_employees;
    })
    .catch((error) => console.error("Error fetching data:", error));
});

document.addEventListener("DOMContentLoaded", function () {
  fetch("phpp/dashboard/db_pieGraph.php") // Ensure this path is correct
    .then((response) => response.json())
    .then((data) => {
      const totalSchoolsCanvas = document.getElementById("totSchools");
      const totalSchoolsContext = totalSchoolsCanvas.getContext("2d");

      // Check if a chart already exists, and destroy it before creating a new one
      const existingChart = Chart.getChart(totalSchoolsCanvas); // This will return the chart instance if it exists
      if (existingChart) {
        existingChart.destroy(); // Destroy the existing chart instance
      }

      // Create a new chart
      new Chart(totalSchoolsContext, {
        type: "doughnut",
        data: {
          labels: ["Public", "Private", "SUC"],
          datasets: [
            {
              data: [data.public, data.private, data.semi_private], // Use database values
              backgroundColor: ["#2E7D32", "#4CAF50", "#81C784"],
              borderWidth: 0,
            },
          ],
        },
      });
    })
    .catch((error) => console.error("Error fetching data:", error));
});
