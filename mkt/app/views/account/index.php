<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account</title>
</head>
<body>
    <div class="hero-section">
        <h1>Account</h1>
        <p>View others account and build a network of like-minded people.</p>
    </div>
    <div class="account">
        <section class="account-user-info">
            <h1><?= $user["full_name"] ?></h1>
            <p class="small-gray-text"><?= $user["email"] ?></p>
            <div class="flex-col-wrapper">
                <a class="big-bold-black-text">Personal Info</a>
                <a class="big-bold-black-text">Holdings</a>
            </div>
        </section>
        <section class="account-content">
            Select a section to view details
        </section>
    </div>
</body>
</html>