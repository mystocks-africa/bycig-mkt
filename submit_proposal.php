<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>BYCIG Stock Proposal Submission</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="static/javascript/submit_proposal.js"></script>
    <link rel="stylesheet" href="static/css/index.css" >
</head>
<body id="submit-proposal-body">
    <h1>Submit Your Stock Proposal</h1>
    <form method="post" enctype="multipart/form-data" action="json-api/submit_proposal.php">
        <label for="email">Email Address:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label>Choose your Cluster Leader:</label>
        <select id="leaderSelect" name="cluster_leader_id">
            <option value="">Select your leader</option>
        </select>
        <br><br>

        <input type="radio" id="useSelect" name="stock_option" value="select" checked onchange="toggleStockInput()">
        <label for="useSelect">Choose from list:</label>
        <select id="stockSelect" name="stock_ticker">
            <option value="">Loading stocks...</option>
        </select>
        <br><br>

        <input type="radio" id="useCustom" name="stock_option" value="custom" onchange="toggleStockInput()">
        <label for="useCustom">Can't find your ticker? Enter a custom one:</label>
        <input type="text" id="customStock" name="stock_ticker" placeholder="e.g. AAPL, TSLA" disabled>
        <br><br>

        <button id="fetchNewStockBtn" type="button" onclick="fetchNewStockBatch()">Fetch more stocks</button>
        <br><br>

        <label for="stock_name">Stock Name:</label><br>
        <input type="text" id="stock_name" name="stock_name" maxlength="255" required><br><br>

        <label for="subject_line">Subject Line:</label><br>
        <input type="text" id="subject_line" name="subject_line" maxlength="255" required><br><br>

        <label for="thesis">1 Sentence Thesis:</label><br>
        <textarea id="thesis" name="thesis" maxlength="1000" required></textarea><br><br>

        <label for="bid_price">Bid Price (where you want us to buy at):</label><br>
        <input type="number" id="bid_price" name="bid_price" step="0.01" min="0" required><br><br>

        <label for="target_price">Target Price (must be ≥ Bid Price):</label><br>
        <input type="number" id="target_price" name="target_price" step="0.01" min="0" required><br><br>

        <label for="proposal_file">Upload Proposal (PDF only, max 5MB):</label><br>
        <input type="file" id="proposal_file" name="proposal_file" accept="application/pdf" required><br><br>

        <button type="submit">Submit Proposal</button>
    </form>
</body>
</html>
