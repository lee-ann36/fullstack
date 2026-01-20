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

    if (isset($_POST['toevoegen'])) {
    $productnaam = $_POST['productnaam'] ?? '';
    $inkoopprijs = $_POST['inkoopprijs'] ?? '';
    $aantal = $_POST['aantal'] ?? '';
    $fabriek = $_POST['fabriek'] ?? '';
    $nieuw_productnaam = $_POST['nieuw_productnaam'] ?? '';

    //prijs veranderen
    if(!empty($inkoopprijs) && !empty($productnaam)){
        $stmt = $conn->prepare("UPDATE producten SET inkoopprijs = ? WHERE naam = ?");
        $stmt->bind_param("ss",$inkoopprijs, $productnaam);
        $stmt->execute();

        echo '<div class="alert alert-success">
          product is gewijzigd<br>
          </div>';
        echo "<script>
         setTimeout(function() {
            window.location.href = 'http://localhost/fsvoorraad.php';
        }, 2000);
        </script>";
    } 
       //frabriek veranderen
    if(!empty($fabriek) && !empty($productnaam)){
        $stmt = $conn->prepare("UPDATE producten SET fabriek = ? WHERE naam = ?");
        $stmt->bind_param("ss",$fabriek, $productnaam);
        $stmt->execute();

        echo  $melding = '<div class="alert alert-success">
          product is gewijzigd<br>
          </div>';
        echo "<script>
         setTimeout(function() {
            window.location.href = 'http://localhost/fsvoorraad.php';
        }, 2000);
        </script>";
    } 

    $stmt->close();

    }
    
    //product verwijderen
    if (isset($_POST['verwijderen'])) {
      $productnaam = $_POST['productnaam'] ?? '';

     if(!empty($productnaam)){
        $stmt = $conn->prepare("DELETE FROM producten WHERE naam = ?");
        $stmt->bind_param("s", $productnaam);

        if ($stmt->execute()) {
      
        echo '<div class="alert alert-success">
          product is verwijderd<br>
          </div>';
        echo "<script>
         setTimeout(function() {
            window.location.href = 'http://localhost/fsvoorraad.php';
        }, 2000);
        </script>";}
    }

 }


?>


<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>producten toevoegen</title>
<link rel="stylesheet" href="/css/bijwerken.css">
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
  <h2>product bijwerken</h2>

  <label> voor welk product</label>
  <select name="productnaam" id="productnaam">
    <?php 
    $productnaam_result = $conn->query("SELECT naam FROM producten");
    while ($row = $productnaam_result->fetch_assoc()): ?>
    <option value="<?= htmlspecialchars($row['naam']) ?>">
    <?= htmlspecialchars($row['naam']) ?>
    <?php endwhile; ?>
  </select>

  <label>product naam bijwerken</label>
  <input type="text" id="nieuw_productnaam" name="nieuw_productnaam" >

  <label>prijs bijwerken</label>
  <input type="number" id="inkoopprijs" name="inkoopprijs" step="0.01" min="0">

  <label>fabrieks naam bijwerken</label>
  <input type="text" id="fabriek" name="fabriek">

  <button type="submit" name="toevoegen">product toevoegen</button>

 <p>---------------------------------------------------</p>
  <form method="post" onsubmit="return confirm('Weet je zeker dat je je product wilt verwijderen?')">
  <button  type="submit" name="verwijderen" >product verwijderen</button>
  </form>
</form>



</div>

</body>
</html>
