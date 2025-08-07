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
      <p>Start by submitting a new proposal or exploring existing ones.</p>
    </div>

    <div id="grid-container">
        <?php if (!empty($clusterLeaders)): ?>
            <?php foreach ($clusterLeaders as $clusterLeader): ?>
                <?php
                    $clusterLeaderName = $clusterLeader["full_name"];  
                    $clusterLeaderEmail = $clusterLeader["email"];
                ?>
                <a href="/holdings?cluster_leader_email=<?= $clusterLeaderEmail ?>" class="card" style="text-decoration: none; color: black;">
                    <h3 class="truncate"><?= $clusterLeaderName ?>'s Portfolio</h3>
                    <p><strong>Email:</strong> <span class="truncate"><?= $clusterLeaderEmail ?></span></p>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No proposals found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
