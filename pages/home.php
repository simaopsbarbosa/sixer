<?php require_once '../templates/common.php'; ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/styles.css" />
    <title>sixer</title>
  </head>

  <body class="main-background">
    <?php drawHeader(); ?>
    <main>
      <h1>hire talent. sell skills.</h1>
      <p>keep it simple.</p>
      <div class="main-buttons">
        <button class="simple-button light" id="work-done-btn">i need work done</button>
        <button class="simple-button" onclick="window.location.href='create_service.php'">i'm a freelancer</button>
      </div>
    </main>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const workBtn = document.getElementById('work-done-btn');
        const searchInput = document.getElementById('main-search');
        if (workBtn && searchInput) {
          workBtn.addEventListener('click', function () {
            searchInput.focus();
            searchInput.select();
            searchInput.style.transition = 'box-shadow 0.3s cubic-bezier(.4,1.5,.6,1)';
            searchInput.style.boxShadow = '0 0 0 1px #fff, 0 0 1px 1px #fff8'; // white highlight
            setTimeout(() => {
              searchInput.style.boxShadow = '';
            }, 900);
          });
        }
      });
    </script>
  </body>
</html>
