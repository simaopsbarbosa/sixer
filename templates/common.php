<?php function drawHeader() { ?>
<header>
  <a href="../index.php">
    <img src="../assets/logo-w.png" id="logo" alt="sixer" />
  </a>
  <div class="searchbar">
    <a href="">
      <span class="categories-text">categories</span
      ><img class="icon" src="../assets/icons/dropdown.svg" alt="" />
    </a>
    <form action="search.php" method="get">
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
    <a href="signup.php">sign-up</a>
    <a href="login.php" class="simple-button">login</a>
  </div>
</header>
<?php } ?>
