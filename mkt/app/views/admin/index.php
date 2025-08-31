<?php
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")
           . "://" . $_SERVER['HTTP_HOST'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal</title>
    <link rel="stylesheet" href="/static/css/index.css">
    <script src="/static/js/modal.js"></script>
    <script src="/static/js/admin.js"></script>
</head>
<body>
    <div class="hero-section">
      <h1>Admin Portal</h1>
      <p>Manage your cluster's proposals.</p>
    </div>
    
    <div id="grid-container">
        <?php if (!empty($proposals)): ?>
            <?php foreach ($proposals as $index => $proposal): ?>
                <?php
                    $postId = $proposal['post_id'];
                    $clusterLeaderEmail = $proposal['cluster_leader'];
                    $subject = $proposal['subject_line'];
                    $email = $proposal['post_author'];
                    $ticker = $proposal['stock_ticker'];
                    $name = $proposal['stock_name'];
                    $thesis = $proposal['thesis'];
                    $shares = $proposal['shares'];
                    $proposalFile = $proposal['proposal_file'];
                    $modalId = "admin-modal-$index"; // unique modal ID
                ?>
                <div onclick="openModal('<?= $modalId ?>')" class="card" style="text-decoration: none; color: black; cursor: pointer;">
                    <h3 class="truncate"><?= $subject ?></h3>
                    <p><strong>Email:</strong> <span class="truncate"><?= $email ?></span></p>
                </div>

                <div id="<?= $modalId ?>" class="modal-overlay" style="display: none;">
                    <div class="modal-card">
                        <div class="close" onclick="closeModal('<?= $modalId ?>')">&times;</div>
                        <h2><?= $subject ?></h2>
                        <p><strong>Email:</strong> <?= $email ?></p>
                        <p><strong>Ticker:</strong> <?= $ticker ?></p>
                        <p><strong>Name:</strong> <?= $name ?></p>
                        <p><strong>Thesis:</strong> <?= $thesis ?></p>
                        <p><strong>Shares:</strong> <?= $shares ?></p>
                        <p class="underline-text"><strong>File:</strong> <a href="<?= $baseUrl . "/uploads/" . $proposalFile ?>"><?= $proposalFile ?></a></p>

                        <div style="margin-top: 40px;">
                            <div class="side-by-side-btns">
                                <button onclick="handleUpdateStatus(<?= $postId ?>, '<?= $clusterLeaderEmail ?>', 'accept')">Accept</button>
                                <button onclick="handleUpdateStatus(<?= $postId ?>, '<?= $clusterLeaderEmail ?>', 'decline')">Decline</button>
                            </div>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No proposals found.</p>
        <?php endif; ?>
    </div>
</body>
</html>