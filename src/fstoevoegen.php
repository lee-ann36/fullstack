<?php
session_start();

$servername = "mysql";
$username = "root";
$password = "password";

try {
    $conn = new mysqli($servername, $username, $password, "tools4ever");
} catch (mysqli_sql_exception) {
    echo "Could not connect";
    exit();
}

if (!isset($_SESSION['gebruikersnaam'])) {
 header("Location: fslogin.php");
 exit();
}

$melding = '';
// producten toevoegen
if (isset($_POST['toevoegen'])) {
    $type = $_POST['type'];
    $naam = $_POST['naam'];
    $fabriek = $_POST['fabriek'];
    $inkoopprijs = $_POST['inkoopprijs'];

    // Check of producten al bestaat
    $check = $conn->prepare("SELECT type FROM producten WHERE type = ?");
    $check->bind_param("s", $type);
    $check->execute();
    $check->store_result();

    //product toevoegen
    if ($check->num_rows > 0) {
        echo "<p style='color:red;er is iets mis met de gegevens van dit product .</p>";
    } else {
        $stmt = $conn->prepare("INSERT INTO producten (type, naam, fabriek, inkoopprijs) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $type, $naam, $fabriek, $inkoopprijs);
        $stmt->execute();
        echo  $melding = '<div class="alert alert-success">
          product is toegevoegd<br>
          </div>';
        echo "<script>
         setTimeout(function() {
            window.location.href = 'http://localhost/fsvoorraad.php';
        }, 2000);
        </script>";
      
        $stmt->close();
    }
    $check->close();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>producten toevoegen</title>
<link rel="stylesheet" href="/css/fstoevoegen.css">
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

<form method="POST" class="pt">
  <h2>producten toevoegen</h2>

  <label>type</label>
  <input type="text" id="type" name="type" required>

  <label>naam</label>
  <input type="text" id="naam" name="naam" required>

  <label>fabrieks naam</label>
  <input type="text" id="fabriek" name="fabriek" required>

  <label>inkoopprijs</label>
  <input type="number" id="inkoopprijs" name="inkoopprijs" step="0.01" min="0" required>


  <button type="submit" name="toevoegen">product toevoegen</button>
</form>
    
</body>
</html>