<?php
session_start();
    $servername = "mysql";
    $username = "root";
    $password = "password";

    try {
      $conn = new mysqli($servername, $username, $password, "tools4ever");
      if ($conn->connect_error) {
        error_log($conn->connect_error);
        exit("Connection DB failed");
      }
    } catch (Exception $e) {
      error_log($e);
      exit("Connection DB failed");
    }

    if (!isset($_SESSION['gebruikersnaam'])) {
    header("Location: fslogin.php");
    exit();
}


 $melding = '';

 if (isset($_POST['toevoegen'])) {
    $productnaam = $_POST['productnaam'];
    $locatienaam = $_POST['locatienaam'];
    $aantal = $_POST['aantal'];

   // product zoeken
    $stmt = $conn->prepare("SELECT type, inkoopprijs FROM producten WHERE naam = ?");
    $stmt->bind_param("s", $productnaam);
    $stmt->execute();
    $stmt->bind_result($type, $inkoopprijs);
    $stmt->fetch();
    $stmt->close();

   $bedrag = $aantal * $inkoopprijs;

   // controllen
    if (empty($productnaam) || empty($locatienaam) || $aantal <= 0) {
      echo "<p style='color:red;'> Er is iets mis met de ingevoerde gegevens.</p>";
    } else {
        // Product toevoegen aan bestellingen
        $stmt = $conn->prepare("INSERT INTO bestellingen (productnaam, type, locatienaam, aantal, bedrag ,besteldate) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssssi", $productnaam, $type, $locatienaam, $aantal, $bedrag);
        $stmt->execute();
       echo      // Succesmelding met het bedrag
        $melding = '<div class="alert alert-success">
          Bestelling geplaatst!<br>
          Product: <strong>'.htmlspecialchars($productnaam).'</strong><br>
          Type: <strong>'.htmlspecialchars($type).'</strong><br>
          Locatie: <strong>'.htmlspecialchars($locatienaam).'</strong><br>
          Aantal: <strong>'.$aantal.'</strong><br>
          Totaalprijs: <strong>€'.$bedrag.'</strong>
          </div>';
       echo "<script>
        setTimeout(function() {
          window.location.href = 'http://localhost/fsvoorraad.php';
      }, 9000);
      </script>";
      
      $stmt->close();
    }
  }

?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>bestellingen</title>
<link rel="stylesheet" href="bestelling.css">
</head>

<body>
<div class="nav-bar">
 <p class="logo">Tools4Ever</p>
 <ul>
  <li><a href="http://localhost/home.php">home</a></li>
  <li><a href="http://localhost/fsvoorraad.php">voorraad</a></li>
  <li><a href="http://localhost/bestelling.php">bestellen</a></li>
  <li><a href="http://localhost/medewerker.php">medewerkers</a></li>
  </ul>
</div>

<div class ="BT">
  <form method="POST" class="bestellen">
  <h2>besteling plaatsen</h2>

  <label> voor welk product</label>
  <select name="productnaam" id="productnaam">
    <?php 
    $productnaam_result = $conn->query("SELECT naam FROM producten");
    while ($row = $productnaam_result->fetch_assoc()): ?>
    <option value="<?= htmlspecialchars($row['naam']) ?>">
    <?= htmlspecialchars($row['naam']) ?>
    <?php endwhile; ?>
  </select>

  <label>voor welke locatie</label>
  <select name="locatienaam" id="locatienaam">
      <?php 
      $locatie_result = $conn->query("SELECT locatienaam FROM locatie");
      while ($row = $locatie_result->fetch_assoc()): ?>
      <option value="<?= htmlspecialchars($row['locatienaam']) ?>">
      <?= htmlspecialchars($row['locatienaam']) ?>
      <?php endwhile; ?>
  </select>

  <label>aantal</label>
  <input type="number" id="aantal" name="aantal" step="1" min="0" required>

  <button type="submit" name="toevoegen">product toevoegen</button>
  </form>
</div>


</body>
</html>