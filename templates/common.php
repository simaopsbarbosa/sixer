<?php 
require_once __DIR__ . '/../utils/session.php';
function drawHeader() { 
  $session = Session::getInstance();
  $loggedIn = $session->isLoggedIn();
  $user = $session->getUser();
?>
<header>
  <div>
    <a href="../index.php">
      <img src="../assets/logo-w.png" id="logo" alt="sixer" />
    </a>
    <!-- should only be visible is user is logged in (this is header alignment) -->
      <?php if ($loggedIn): ?>
        <div class="spacer" style="width: 10vw; display: inline-block;"></div>
      <?php endif; ?>
  </div>
  <div class="searchbar">
    <a href="#" id="filters-btn">
      <span class="filters-text">filters</span>
      <img class="icon" src="../assets/icons/dropdown.svg" alt="" />
    </a>
    <form action="search.php" method="get">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>" />
      <input
        type="text"
        name="q"
        id="main-search"
        placeholder="what do you need to get done?"
      />
      <button type="submit">
        <img class="icon" src="../assets/icons/search.svg" alt="search" />
      </button>
    </form>
  </div>
  <div class="account">
    <?php if (
      $loggedIn && isset($user['user_id'])
    ): ?>
      <a href="profile.php?id=<?= urlencode($user['user_id']) ?>" class="profile-link-with-pic">
        <p style="margin:0;">profile</p>
        <img src="../action/get_profile_picture.php?id=<?= urlencode($user['user_id']) ?>" alt="profile picture" class="header-profile-pic" />
      </a>
      <a href="create_service.php" class="simple-button">
        <span class="new-service-plus">+</span>
        <span class="new-service-text"> new service</span>
      </a>
      <a href="../action/logout.php" class="simple-button">sign out</a>
    <?php else: ?>
      <a href="signup.php">sign-up</a>
      <a href="login.php" class="simple-button">login</a>
    <?php endif; ?>
  </div>
</header>
<div id="filters-modal" class="filters-modal">
  <div class="filters-modal-content">
    <button id="close-filters" type="button">&times;</button>
    <h3>Filters</h3>
    <form id="filters-form">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>" />
      <label for="filter-category">Category:</label>
      <select id="filter-category" name="category">
        <!-- we should find a better way to do this -->
        <option value="">Any</option>
        <option value="e-commerce">E-commerce</option>
        <option value="design">Design</option>
        <option value="writing">Writing</option>
        <option value="translation">Translation</option>
        <option value="programming">Programming</option>
        <option value="marketing">Marketing</option>
        <option value="video">Video & Animation</option>
        <option value="music">Music & Audio</option>
        <option value="business">Business</option>
        <option value="lifestyle">Lifestyle</option>
        <option value="other">Other</option>
      </select>
      <br /><br />
      <label>Price:</label>
      <div class="price-fields">
        <input type="number" name="min_price" placeholder="Min" min="0" />
        <input type="number" name="max_price" placeholder="Max" min="0" />
      </div>
      <br /><br />
      <label for="filter-rating">Rating:</label>
      <select id="filter-rating" name="rating">
        <option value="">Any</option>
        <option value="5">5 stars</option>
        <option value="4">4 stars & up</option>
        <option value="3">3 stars & up</option>
      </select>
      <br /><br />
      <button type="submit" class="simple-button">Apply Filters</button>
    </form>
  </div>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var filtersBtn = document.getElementById('filters-btn');
    var filtersModal = document.getElementById('filters-modal');
    var closeFilters = document.getElementById('close-filters');
    if (filtersBtn && filtersModal && closeFilters) {
      filtersBtn.addEventListener('click', function(e) {
        e.preventDefault();
        filtersModal.style.display = 'flex';
      });
      closeFilters.addEventListener('click', function() {
        filtersModal.style.display = 'none';
      });
      filtersModal.addEventListener('mousedown', function(event) {
        if (event.target === filtersModal) {
          filtersModal.style.display = 'none';
        }
      });
    }
  });
</script>
<?php } ?>
