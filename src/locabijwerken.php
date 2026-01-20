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
    header("Location: fsinlog.php");
    exit();
   }

    if (!isset($_SESSION['locatienaam'])) {
    header("Location: fsvoorraad.php");
    exit();
    } 

  
  

    $locatie = $_SESSION['locatienaam'];

    if (isset($_POST['verwijderen'])) {
        $productnaam = $_POST['productnaam'] ?? '';

      $stmt = $conn->prepare("SELECT type FROM producten WHERE naam = ?");
      $stmt->bind_param("s", $productnaam);
      $stmt->execute();
      $stmt->bind_result($type);
      $stmt->fetch();
      $stmt->close();

      // producten aanpassen voor een locatie
      if(!empty($productnaam)){
        $stmt = $conn->prepare("DELETE FROM locatieaantal WHERE type = ? And locatienaam = '$locatie'");
        $stmt->bind_param("s", $type);

        if ($stmt->execute()) {
      
        echo '<div class="alert alert-success">
          product is verwijderd<br>
          </div>';
        echo "<script>
         setTimeout(function() {
            window.location.href = 'http://localhost/fsvoorraad.php';
        }, 2000);
        </script>";}
    }}

    if (isset($_POST['toevoegen'])) {
      $aantal = $_POST['aantal'] ?? '';
      $productnaam = $_POST['productnaam'] ?? '';

      $stmt = $conn->prepare("SELECT type FROM producten WHERE naam = ?");
      $stmt->bind_param("s", $productnaam);
      $stmt->execute();
      $stmt->bind_result($type);
      $stmt->fetch();
      $stmt->close();

      if(!empty($aantal) && !empty($productnaam)){
        $stmt = $conn->prepare("UPDATE locatieaantal SET aantal = ? WHERE type = ?");
        $stmt->bind_param("is",$aantal, $type);
        $stmt->execute();

        echo '<div class="alert alert-success">
          product aantal is gewijzigd<br>
          </div>';
        echo "<script>
         setTimeout(function() {
            window.location.href = 'http://localhost/fsvoorraad.php';
        }, 2000);
        </script>";
        } 
    }




?>


<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>producten toevoegen</title>
<link rel="stylesheet" href="/css/locabijwerken.css">
</head>

<body>
<div class="nav-bar">
 <p class="logo">Tools4Ever</p>
 <ul>
  <li><a href="http://localhost/home.php">home</a></li>
  <li><a href="http://localhost/fsvoorraad.php">voorraad</a></li>
  <li><a href="http://localhost/bestelling.php">bestellen</a></li>
  <li><a href="http://localhost/medewerker.php">medewerkers</a></li>
  <li><form method="get">
  <input type="hidden" name="logout" value="true">
  <button type="submit">Uitloggen</button>
  </form></li>
  </ul>
</div>

<div class ="BT">
  <form method="POST" class="bestellen">
 <h2>Product bijwerken van <?= htmlspecialchars($locatie) ?></h2>

  <label> voor welk product</label>
  <select name="productnaam" id="productnaam">
    <?php 
    $productnaam_result = $conn->query("SELECT producten.naam
     FROM locatieaantal
     INNER JOIN producten ON locatieaantal.type = producten.type 
     where locatienaam = '$locatie'");
    while ($row = $productnaam_result->fetch_assoc()): ?>
    <option value="<?= htmlspecialchars($row['naam']) ?>">
    <?= htmlspecialchars($row['naam']) ?>
    <?php endwhile; ?>
  </select>

  <label>product aantal bijwerken</label>
  <input type="number" id="aantal" name="aantal" step="1" min="0">

  <button type="submit" name="toevoegen">product bijwerken</button>

 <p>---------------------------------------------------</p>
  
  <button  type="submit" name="verwijderen" >product verwijderen</button>
 
</form>



</div>

</body>
</html>
