<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proposal Details</title>
    <link rel="stylesheet" href="/static/css/index.css">
</head>
<body>
    <div id="proposal-container">
        <div class="card">
            <h3 class="truncate"><?= $proposal["subject_line"] ?></h3>
            <p><strong>Email:</strong> <span class="truncate"><?= $proposal["email"] ?></span></p>
            <p><strong>Stock:</strong> <span><?= $proposal["stock_name"] ?></span> (<span><?= $proposal["stock_ticker"] ?></span>)</p>
            <p><strong>Bid Price:</strong> $<?= $proposal["bid_price"] ?></p>
            <p><strong>Target Price:</strong> $<?= $proposal["target_price"] ?></p>
            <p><strong>Status:</strong> <?= $proposal["status"] ?></p>
            <p><strong>Thesis:</strong> <?= nl2br($proposal["thesis"]) ?></p>
            <p><strong>File:</strong> <?= $proposal["proposal_file"] ?></p>
        </div>
    </div>
</body>
</html>
