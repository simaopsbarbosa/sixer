<?php
require_once '../templates/common.php';
require_once '../database/service_class.php';

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$min_price = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? floatval($_GET['min_price']) : null;
$max_price = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? floatval($_GET['max_price']) : null;
$min_rating = isset($_GET['min_rating']) && $_GET['min_rating'] !== '' ? floatval($_GET['min_rating']) : null;
$max_rating = isset($_GET['max_rating']) && $_GET['max_rating'] !== '' ? floatval($_GET['max_rating']) : null;

// Server-side validation: ensure min_rating is not greater than max_rating
if ($min_rating !== null && $max_rating !== null && $min_rating > $max_rating) {
    $min_rating = $max_rating;
}

// Server-side validation: ensure min_price is not greater than max_price
if ($min_price !== null && $max_price !== null && $min_price > $max_price) {
    $min_price = $max_price;
}

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

// Get services list with possible rating filters
$db = Database::getInstance();

// If we have rating filters, we need a more complex query that joins with purchases table
if ($min_rating !== null || $max_rating !== null) {
    // Start with base SQL query for non-delisted services
    $join_sql = 'SELECT s.service_id, 
                COALESCE(AVG(CASE WHEN p.review_rating IS NOT NULL THEN p.review_rating ELSE NULL END), 0) as avg_rating,
                COUNT(CASE WHEN p.review_rating IS NOT NULL THEN 1 ELSE NULL END) as review_count
                FROM services_list s
                LEFT JOIN purchases p ON s.service_id = p.service_id
                WHERE s.service_delisted = 0 ';
    
    // Add other filters
    if ($q !== '') {
        $join_sql .= ' AND s.service_title LIKE ?';
    }
    if ($category !== '') {
        $join_sql .= ' AND s.service_category = ?';
    }
    if ($min_price !== null) {
        $join_sql .= ' AND s.service_price >= ?';
    }
    if ($max_price !== null) {
        $join_sql .= ' AND s.service_price <= ?';
    }
    
    // Group by service_id to get averages
    $join_sql .= ' GROUP BY s.service_id';
    
    // Apply rating filters in HAVING clause since they operate on aggregate functions
    $having_conditions = [];
    if ($min_rating !== null) {
        // Here we use COALESCE to treat NULL ratings as 0
        $having_conditions[] = 'COALESCE(avg_rating, 0) >= ?';
        $params[] = $min_rating;
    }
    if ($max_rating !== null) {
        $having_conditions[] = 'COALESCE(avg_rating, 0) <= ?';
        $params[] = $max_rating;
    }
    
    // Include all services, treating those without reviews as having a rating of 0
    if (!empty($having_conditions)) {
        // Modify HAVING clause to treat NULL avg_rating as 0 for filtering purposes
        $having_clauses = [];
        
        if ($min_rating !== null) {
            // If min rating is 0 or negative, include services with no reviews
            if (floatval($min_rating) <= 0) {
                $having_clauses[] = '(COALESCE(avg_rating, 0) >= ? OR avg_rating IS NULL)';
            } else {
                // Only include services with ratings above min_rating
                $having_clauses[] = 'COALESCE(avg_rating, 0) >= ?';
            }
        }
        
        if ($max_rating !== null) {
            // Include services with no reviews if max_rating is specified
            $having_clauses[] = 'COALESCE(avg_rating, 0) <= ?';
        }
        
        $join_sql .= ' HAVING ' . implode(' AND ', $having_clauses);
    }
    
    $stmt = $db->prepare($join_sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $service_ids = array_column($results, 'service_id');
} else {
    // Simple query without rating filters
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $service_ids = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
}

// Convert service IDs to the format expected by the rest of the code
$services = $service_ids;

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
              if ($min_rating !== null || $max_rating !== null) {
                $rating_range = '';
                if ($min_rating !== null && $max_rating !== null) {
                  $rating_range = $min_rating . ' - ' . $max_rating . ' stars';
                } elseif ($min_rating !== null) {
                  if (floatval($min_rating) <= 0) {
                    $rating_range = $min_rating . '+ stars (includes unrated)';
                  } else {
                    $rating_range = $min_rating . '+ stars';
                  }
                } else {
                  $rating_range = 'up to ' . $max_rating . ' stars';
                }
                $search_title .= ' with ' . $rating_range;
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
