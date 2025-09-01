<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up - MKT</title>
</head>
<body>
  <div class="hero-section">
    <h1>Sign up</h1>
    <p>If you don't have an account, register here</p>
    <br>
  </div>

  <form action="/auth/signup" method="post">
    <label for="full_name">Full Name:</label>
    <input type="text" id="full_name" name="full_name"><br><br>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email"><br><br>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password"><br><br>

    <label for="confirm_password">Confirm Password:</label>
    <input type="password" id="confirm_password" name="confirm_password"><br><br>

    <label for="cluster_leader">Your Cluster Leader:</label>
    <select id="cluster_leader" name="cluster_leader">
      <option value="">None</option>
      <?php foreach ($clusterLeaderEmails as $leader): ?>
        <option value="<?= $leader ?>">
          <?= $leader ?>
        </option>
      <?php endforeach; ?>
    </select><br><br>

    <button type="submit">Sign up</button>
  </form>
</body>
</html>
