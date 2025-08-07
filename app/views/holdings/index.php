<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Holdings</title>
    <link rel="stylesheet" href="/static/css/index.css">
    <script src="/static/js/holdings.js"></script>
</head>
<body>
    <div class="hero-section">
      <h1>Holdings</h1>
      <p>All associated assets for this portfolio.</p>
    </div>

    <div id="grid-container">
        <?php if (!empty($holdings)): ?>
            <?php foreach ($holdings as $holding): ?>
                <?php
                    $investor = $holding["investor"];
                    $stockName = $holding["stock_name"];
                    $stockTicker = $holding["stock_ticker"];  
                ?>
                <div class="card">
                    <p>Investor: <?= $investor ?></p> 
                    <p>Stock Name: <?= $stockName ?></p> 
                    <p>Stock Ticker: <?= $stockTicker ?></p> 
                    <?php if ($holding["investor"] == $session['email']): ?>
 s                    <?php endif ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No holdings found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
