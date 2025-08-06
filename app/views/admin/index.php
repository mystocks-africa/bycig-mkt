<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal</title>
    <link rel="stylesheet" href="/static/css/index.css">
</head>
<body>
    <div class="hero-section">
      <h1>Admin Portal</h1>
      <p>Manage your cluster's proposals.</p>
    </div>
    
    <div id="grid-container">
        <?php if (!empty($proposals)): ?>
            <?php foreach ($proposals as $proposal): ?>
                <?php
                    $subject = $proposal['subject_line'];
                    $email = $proposal['post_author'];
                ?>
                <a href="/proposals/details?post_id=<?= $postId ?>" class="card" style="text-decoration: none; color: black;">
                    <h3 class="truncate"><?= $subject ?></h3>
                    <p><strong>Email:</strong> <span class="truncate"><?= $email ?></span></p>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No proposals found.</p>
        <?php endif; ?>
    </div>
</body>
</html>