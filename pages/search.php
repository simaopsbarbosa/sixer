<?php
require_once '../templates/common.php';
require_once '../database/service_class.php';

$services = [];
// Fetch all services (delisted = 0)
$db = Database::getInstance();
$stmt = $db->prepare('SELECT service_id FROM services_list WHERE service_delisted = 0');
$stmt->execute();
$service_ids = $stmt->fetchAll();
foreach ($service_ids as $row) {
    $services[] = $row['service_id'];
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/styles.css" />
    <link rel="stylesheet" href="../css/search.css" />
    <title>sixer - search results</title>
  </head>
  <body>
    <?php drawHeader(); ?>
    <main>
      <div class="search-results-container">
        <h2 class="search-results-title">
          <?= count($services) ?> results for<br /><span style="font-weight:bold; font-size: 1.3em">"Empty search"</span>
        </h2>
        <div class="search-cards">
          <?php
            require_once __DIR__ . '/../templates/service_card.php';
            foreach ($services as $service_id) {
              drawServiceCard($service_id);
            }
          ?>
        </div>
      </div>
    </main>
    <script>
      document.addEventListener("DOMContentLoaded", function () {
        const searchInput = document.querySelector(".searchbar input");
        const searchIcon = document.querySelector(
          '.searchbar a[href="search.php"]'
        );

        // Function to perform search
        function performSearch() {
          const searchTerm = searchInput.value.trim();
          if (searchTerm) {
            window.location.href = `search.php?q=${encodeURIComponent(
              searchTerm
            )}`;
          }
        }

        // Handle Enter key press
        searchInput.addEventListener("keypress", function (e) {
          if (e.key === "Enter") {
            performSearch();
          }
        });

        // Handle search icon click
        searchIcon.addEventListener("click", function (e) {
          e.preventDefault();
          performSearch();
        });
      });
    </script>
  </body>
</html>
