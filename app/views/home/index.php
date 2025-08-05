<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proposal Submissions</title>
    <link rel="stylesheet" href="/static/css/index.css">
    <script>
        function goToSubmitProposal() {
            window.location.href = '/proposals/submit';
        }
    </script>
</head>
<body>
    <button id="plus-btn" onclick="goToSubmitProposal()">+</button>

    <div id="grid-container">
        <?php if (!empty($proposals)): ?>
            <?php foreach ($proposals as $proposal): ?>
                <?php
                    $postId = htmlspecialchars($proposal['post_id'], ENT_QUOTES, 'UTF-8');
                    $subject = htmlspecialchars($proposal['subject_line'], ENT_QUOTES, 'UTF-8');
                    $email = htmlspecialchars($proposal['email'], ENT_QUOTES, 'UTF-8');
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
