<?php require_once '../templates/common.php'; ?>
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
          5 results for<br /><span style="font-weight: bold; font-size: 1.3em"
            >"assembly programmer"</span
          >
        </h2>
        <div class="search-cards">
          <a href="service.php" class="search-card">
            <img src="../assets/images/assembly.jpg" alt="assembly programmer" />
            <div class="search-card-content">
              <div class="search-card-text">
                <span class="search-card-title">Peter Parker</span><br />
                <span class="search-card-desc"
                  >I will be your programmer for c plus plus and assembly
                  language</span
                >
              </div>
              <div class="search-card-stats">
                <div class="search-card-info">
                  <span class="search-card-delivery">1 week</span>
                  <span class="search-card-rating">4.2 ★★★★★</span>
                </div>
                <span class="search-card-price">from <span class="search-card-price-bold">6$</span></span>
              </div>
            </div>
          </a>
          <a href="service.php" class="search-card">
            <img src="../assets/images/assembly.jpg" alt="assembly programmer" />
            <div class="search-card-content">
              <div class="search-card-text">
                <span class="search-card-title">Barry Allen</span><br />
                <span class="search-card-desc"
                  >I will do masm nasm asm x86 8088 x88 8088 mips and arm for
                  very cheap!!!!</span
                >
              </div>
              <div class="search-card-stats">
                <div class="search-card-info">
                  <span class="search-card-delivery">1 week</span>
                  <span class="search-card-rating">4.1 ★★★★☆</span>
                </div>
                <span class="search-card-price">from <span class="search-card-price-bold">87$</span></span>
              </div>
            </div>
          </a>
          <a href="service.php" class="search-card">
            <img src="../assets/images/assembly.jpg" alt="assembly programmer" />
            <div class="search-card-content">
              <div class="search-card-text">
                <span class="search-card-title">Peter Griffin</span><br />
                <span class="search-card-desc"
                  >I will do cpp c sharp python java and assembly tasks and
                  projects</span
                >
              </div>
              <div class="search-card-stats">
                <div class="search-card-info">
                  <span class="search-card-delivery">2 weeks</span>
                  <span class="search-card-rating">4.3 ★★★★☆</span>
                </div>
                <span class="search-card-price">from <span class="search-card-price-bold">6$</span></span>
              </div>
            </div>
          </a>
          <a href="service.php" class="search-card">
            <img src="../assets/images/assembly.jpg" alt="assembly programmer" />
            <div class="search-card-content">
              <div class="search-card-text">
                <span class="search-card-title">Tony Stark</span><br />
                <span class="search-card-desc"
                  >I will program microcontrollers for you in assembly
                  language</span
                >
              </div>
              <div class="search-card-stats">
                <div class="search-card-info">
                  <span class="search-card-delivery">3 weeks</span>
                  <span class="search-card-rating">4.0 ★★★★☆</span>
                </div>
                <span class="search-card-price">from <span class="search-card-price-bold">7$</span></span>
              </div>
            </div>
          </a>
          <a href="service.php" class="search-card">
            <img src="../assets/images/assembly.jpg" alt="assembly programmer" />
            <div class="search-card-content">
              <div class="search-card-text">
                <span class="search-card-title">Bruce Banner</span><br />
                <span class="search-card-desc"
                  >I will unlock the power of assembly for your projects. Trust
                  me, I'm a doctor</span
                >
              </div>
              <div class="search-card-stats">
                <div class="search-card-info">
                  <span class="search-card-delivery">1 week</span>
                  <span class="search-card-rating">3.9 ★★★☆☆</span>
                </div>
                <span class="search-card-price">from <span class="search-card-price-bold">999$</span></span>
              </div>
            </div>
          </a>
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
