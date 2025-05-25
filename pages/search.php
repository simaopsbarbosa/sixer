<?php
require_once '../templates/common.php';
require_once '../database/service_class.php';

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$min_price = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? floatval($_GET['min_price']) : null;
$max_price = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? floatval($_GET['max_price']) : null;

$params = [];
$sql = 'SELECT service_id FROM services_list WHERE service_delisted = 0';

if ($q !== '') {
    $sql .= ' AND service_title LIKE ?';
    $params[] = '%' . $q . '%';
}
if ($category !== '') {
    $sql .= ' AND service_category = ?';
    $params[] = $category;
}
if ($min_price !== null) {
    $sql .= ' AND service_price >= ?';
    $params[] = $min_price;
}
if ($max_price !== null) {
    $sql .= ' AND service_price <= ?';
    $params[] = $max_price;
}

$db = Database::getInstance();
$stmt = $db->prepare($sql);
$stmt->execute($params);
$service_ids = $stmt->fetchAll();

$services = [];
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
          <?= count($services) ?> results for<br />
          <span style="font-weight:bold; font-size: 1.3em">
            <?php
              $search_title = $q ? '"' . htmlspecialchars($q) . '"' : 'All Services';
              if ($category) {
                $search_title .= ' in ' . htmlspecialchars($category);
              }
              if ($min_price !== null || $max_price !== null) {
                $price_range = '';
                if ($min_price !== null && $max_price !== null) {
                  $price_range = '$' . $min_price . ' - $' . $max_price;
                } elseif ($min_price !== null) {
                  $price_range = '$' . $min_price . ' and up';
                } else {
                  $price_range = 'up to $' . $max_price;
                }
                $search_title .= ' (' . $price_range . ')';
              }
              echo $search_title;
            ?>
          </span>
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
