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
    <button class="plus-btn" onclick="goToSubmitProposal()">+</button>

    <div class="hero-section">
      <h1 id="greeting"></h1>
      <p>Start by submitting a new proposal or exploring investments.</p>
    </div>

    <div id="grid-container">
        <?php if (!empty($holdings)): ?>
            <?php foreach ($holdings as $holding): ?>
                <?php
                    $investor = $holding["investor"];
                    $stockTicker = $holding["stock_ticker"];
                    $stockName = $holding["stock_name"];
                    $bidPrice = $holding["bid_price"];
                    $targetPrice = $holding["target_price"];
                    $proposalFile = $holding["proposal_file"];
                ?>
                <a href="/proposal-file/<?= $proposalFile ?>" class="card" style="text-decoration: none; color: black;">
                    <h3 class="truncate"><?= $stockName ?> (<?= $stockTicker ?>)</h3>
                    <p><strong>Investor:</strong> <?= $investor ?></p>
                    <p><strong>Bid Price:</strong> $<?= $bidPrice ?></p>
                    <p><strong>Target Price:</strong> $<?= $targetPrice ?></p>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No holdings found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
