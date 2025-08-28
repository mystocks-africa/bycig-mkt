<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BYCIG Stock Proposal Submission</title>
    <link rel="stylesheet" href="/static/css/index.css" >
</head>
<body>
    <div class="hero-section">
        <h1>Submit proposal</h1>
        <p>This proposal will be sent to your cluster leader. If they agree to it, a holding will be added and a limit order for 5 days will be placed.</p>
    <br>
    </div>
    <form method="post" enctype="multipart/form-data" action="/proposals/submit" novalidate>
        <label for="customStock">Your Stock Ticker:</label>
        <input type="text" name="stock_ticker" placeholder="e.g. AAPL, TSLA" maxlength="10">
        <br><br>

        <label for="stock_name">Stock Name:</label><br>
        <input type="text" id="stock_name" name="stock_name" maxlength="255" required>
        <br><br>

        <label for="subject_line">Subject Line:</label><br>
        <input type="text" id="subject_line" name="subject_line" maxlength="255" required>
        <br><br>

        <label for="thesis">1 Sentence Thesis:</label><br>
        <textarea id="thesis" name="thesis" maxlength="1000" required></textarea>
        <br><br>

        <label for="bid_price">Bid Price (where you want us to buy at):</label><br>
        <input type="number" id="bid_price" name="bid_price" step="0.01" min="0" required>
        <br><br>

        <label for="target_price">Target Price (must be ≥ Bid Price):</label><br>
        <input type="number" id="target_price" name="target_price" step="0.01" min="0" required>
        <br><br>

        <label for="number">Amount of shares to be bought:</label><br>
        <input type="number" id="shares" name="Shares" required>
        <br><br>

        <label for="proposal_file">Upload Proposal (PDF only, max 5MB):</label><br>
        <input type="file" id="proposal_file" name="proposal_file" accept="application/pdf" required>
        <br><br>

        <button type="submit">Submit Proposal</button>
    </form>
</body>
</html>
