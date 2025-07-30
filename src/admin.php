<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <script src="static/javascript/admin.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="static/css/admin.css" >
</head>
<body>
    <div id="content">
        <p id="loader">Loading and authenticating...</p>
    </div>
    <button onclick="handleSubmit('accept')">Accept proposal</button>
    <button onclick="handleSubmit('decline')">Decline proposal</button>
</body>
</html>