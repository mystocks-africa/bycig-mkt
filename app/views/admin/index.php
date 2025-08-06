<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal</title>
    <link rel="stylesheet" href="/static/css/index.css">
    <script src="/static/js/modal.js"></script>
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
                <div onclick="openModal('admin-modal')" class="card" style="text-decoration: none; color: black;">
                    <h3 class="truncate"><?= $subject ?></h3>
                    <p><strong>Email:</strong> <span class="truncate"><?= $email ?></span></p>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No proposals found.</p>
        <?php endif; ?>
    </div>

    <div id="admin-modal" class="modal-overlay">
        <div class="modal-card">
            <div class="close" onclick="closeModal('admin-modal')">&times;</div>
            <h2>Admin Modal</h2>
            <p>yo</p>
        </div>
    </div>

</body>
</html>