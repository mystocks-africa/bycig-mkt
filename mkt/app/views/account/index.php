<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account</title>
</head>
<body>
    <div class="hero-section">
        <h1>Account</h1>
        <p>View others account and build a network of like-minded people.</p>
    </div>
    
    <div class="account">
        <div class="account-user-info">
            <h1><?= $user["full_name"] ?></h1>
            <p class="small-gray-text"><strong>Email:</strong> <?= $user["email"] ?></p>
            <p class="small-gray-text"><strong>Role:</strong> <?= $user["role"] ?></p>
            <? if($user['role'] != "cluster_leader"): ?>
            <p class="small-gray-text"><strong>Balance:</strong> <?= $user["balance"] ?></p>
            <? endif; ?>
        </div>
        
        <div class="account-content">
            <?php if (empty($holdings)): ?>
                <p>No holdings found.</p>
            <?php else: ?>
                <?php foreach ($holdings as $index => $holding): ?>
                    <?php
                        $ticker = $holding['stock_ticker'];
                        $shares = $holding['shares'];
                        $fulfilled = $holding['fulfilled'];
                    ?>
                    <div class="card">
                        <h3 class="truncate"><?= $ticker ?></h3>
                        <p><strong>Shares:</strong> <?= $shares ?></p>
                        <p><strong>Fulfilled?:</strong> <span class="<?= $fulfilled ? 'fulfilled' : 'pending' ?>"><?= $fulfilled ? 'Fulfilled' : 'Pending' ?></span></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="/static/js/account.js"></script>
</body>
</html>