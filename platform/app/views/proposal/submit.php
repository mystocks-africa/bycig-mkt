<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Proposal - MKT</title>
</head>
<body>
    <div class="hero-section">
        <h1>Submit proposal</h1>
        <p>This proposal will be sent to your cluster leader. If they agree to it, a holding will be added and you can purchase that holding whenever you wish.</p>
    <br>
    </div>
    <form method="post" enctype="multipart/form-data" action="/proposals/submit" novalidate>
        <label for="stock_ticker">Your Stock:</label>
        <select name="stock_ticker" id="custom_stock" required>
            <option value="" disabled selected>Select a stock</option>
            <?php foreach($supportedStocks as $ticker => $name): ?>
                <option value="<?= $ticker ?>" data-stock-name="<?= $name ?>">
                    <?= $name ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <input type="text" id="stock_name" name="stock_name" value="" required style="display:none;">

        <label for="subject_line">Subject Line:</label><br>
        <input type="text" id="subject_line" name="subject_line" maxlength="255" required>
        <br><br>

        <label for="thesis">1 Sentence Thesis:</label><br>
        <textarea id="thesis" name="thesis" maxlength="1000" required></textarea>
        <br><br>

        <label for="number">Amount of shares to be bought:</label><br>
        <input type="number" id="shares" name="shares" required>
        <br><br>

        <label for="proposal_file">Upload Proposal (PDF only, max 5MB):</label><br>
        <input type="file" id="proposal_file" name="proposal_file" accept="application/pdf" required>
        <br><br>

        <button type="submit">Submit Proposal</button>
    </form>

    <script src="/static/js/submit.js"></script>
</body>
</html>
