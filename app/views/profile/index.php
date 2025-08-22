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
        </div>
    </div>

    <?php
    $fullName = $user['full_name'] ?? '';
    $email = $user['email'] ?? '';
    $currentClusterLeader = $user['cluster_leader'] ?? '';
    ?>
    <div id="user-info">
        <form class="form-section" action="/profile/update" method="POST">
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
        Holding Section
    </div>
</body>
</html>
