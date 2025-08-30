<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proposal Submissions</title>
    <script src="/static/js/index.js"></script>
    <link rel="stylesheet" href="/static/css/index.css">
</head>
<body>
    <a href="/proposals/submit">
        <button class="plus-btn">+</button>
    </a>

    <div class="hero-section">
      <h1 id="greeting"></h1>
      <p>Start by submitting a new proposal or exploring investments.</p>
    </div>

    <div id="grid-container">
      <?php if (!empty($holdings)): ?>
            <?php foreach ($holdings as $holding): ?>
                <?php
                    $id = $holding["id"];
                    $investor = $holding["investor"];
                    $stockTicker = $holding["stock_ticker"];
                    $stockName = $holding["stock_name"];
                ?>
                <div key=<?= $id ?> class="card" style="text-decoration: none; color: black;">
                    <h3 class="truncate"><?= $stockName ?></h3>
                    <p><strong>Stock Ticker:</strong> <?= $stockTicker ?></p>
                    <p><strong>Investor:</strong> <?= $investor ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No holdings found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
