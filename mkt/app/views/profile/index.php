<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="/static/css/index.css" >
    <script src="/static/js/profile.js"></script>
</head>
<body>
    <div class="hero-section">
        <h1>User Profile</h1>
        <p>Update or view your profile or view your holdings</p>
        <br>
        <div>
            <a id="info-tab" class="tab active" href="/profile?tab=info">Info</a>
            <a id="holdings-tab" class="tab" href="/profile?tab=holdings">Holdings</a>
            <a id="delete-user-tab" class="tab" href="/profile?tab=delete-user">Delete</a>
        </div>
    </div>

    <?php
    $fullName = $user['full_name'] ?? '';
    $email = $user['email'] ?? '';
    $currentClusterLeader = $user['cluster_leader'] ?? '';
    $balance = $user['balance'] ?? '';
    $role = $user['role'] ?? '';
    ?>
    <div id="user-info">
        <form action="/profile/update" method="POST">
            <?php if ($role === "cluster_leader"): ?>
                <label>Cluster leaders do not have balances.</label>
                <br>
            <?php else: ?>
                <label>Balance:</label>
                <h2>$<?= $balance ?></h2>
            <?php endif; ?>

            <label for="full-name">Full Name:</label>
            <input type="text" id="full-name" name="fullName" placeholder="Enter your full name" value="<?= $fullName ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" value="<?= $email ?>" required>

            <?php if (isset($clusterLeaders)): ?>
                <label for="cluster-leader">Cluster Leader:</label>
                <select id="cluster-leader" name="clusterLeader">
                    <option value="" <?= empty($currentClusterLeader) ? 'selected' : '' ?>>None</option>
                    <?php foreach ($clusterLeaders as $leader): 
                        $leaderEmail = $leader['email'];
                        $leaderName = $leader['full_name'];
                        $selected = ($leaderEmail === $currentClusterLeader) ? 'selected' : '';
                    ?>
                        <option value="<?= $leaderEmail ?>" <?= $selected ?>><?= $leaderName ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>

            <p class="small-gray-link">If you want to update your password, <a href="/auth/forgot-pwd">click here</a>.</p>

            <button type="submit">Update Profile</button>
        </form>
    </div>



    <div id="user-holdings">
        <div id="grid-container">
        <?php if (!empty($holdings)): ?>
                <?php foreach ($holdings as $holding): ?>
                    <?php
                        $id = $holding["id"];
                        $investor = $holding["investor"];
                        $stockTicker = $holding["stock_ticker"];
                        $stockName = $holding["stock_name"];
                        $proposalFile = $holding["proposal_file"];
                    ?>
                    <div key=<?= $id ?> class="card" style="text-decoration: none; color: black;">
                        <h3 class="truncate"><?= $stockName ?></h3>
                        <p><strong>Stock Ticker:</strong> <?= $stockTicker ?></p>
                        <p><strong>Investor:</strong> <?= $investor ?></p>
                        <p><strong>File:</strong> <a class="underline-text" href="/uploads/<?= $proposalFile ?>"">click here</a></p>

                        <form class="sell-btn-wrapper" action="/holdings/sell" method="POST">
                            <button class="sell-btn" type="submit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-delete-icon lucide-delete"><path d="M10 5a2 2 0 0 0-1.344.519l-6.328 5.74a1 1 0 0 0 0 1.481l6.328 5.741A2 2 0 0 0 10 19h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2z"/><path d="m12 9 6 6"/><path d="m18 9-6 6"/></svg>
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No holdings found.</p>
            <?php endif; ?>
        </div>   
    </div>

    <div id="delete-user">
        <div class="header-divider"></div>
        <h1>Delete Account</h1>
        <p class="warning-text">
            Deleting your account is <strong>permanent</strong> and cannot be undone.<br>
            All your profile data and holdings will be lost.
        </p>
        <button type="submit" class="delete-button" onclick="handleDeleteUser()">
                Delete My Account
        </button>
        <p class="cancel-text">
            Changed your mind? <a href="/profile?tab=info">Go back to profile</a>
        </p>
    </div>

    </div>
</body>
</html>
