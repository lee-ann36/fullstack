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

 // bestellingen ophalen uit de database
  $result = $conn->query("
    SELECT bestelnr, type, locatienaam, aantal 
    FROM bestellingen 
    WHERE aankomstdate IS NULL 
      AND besteldate <= NOW() - INTERVAL 1 DAY
 ");

 if ($result && $result->num_rows > 0) {
    while ($order = $result->fetch_assoc()) {
        $id = $order['bestelnr'];
        $type = $order['type'];
        $locatienaam = $order['locatienaam'];
        $aantal = $order['aantal'];

        // Voorraad bijwerken
        $update = $conn->prepare("
            UPDATE locatieaantal 
            SET aantal = aantal + ? 
            WHERE type = ? AND locatienaam = ?
        ");
        $update->bind_param("iss", $aantal, $type, $locatienaam);
        $update->execute();
        $update->close();

        // Bestelling markeren als aangekomen
        $conn->query("UPDATE bestellingen SET aankomstdate = NOW() WHERE bestelnr = $id");
    }
  }

  if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: fslogin.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>voorraad</title>
<link rel="stylesheet" href="css/fsvoorraad.css">
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

<div class="schema">
 <h2>producten</h2>
 
 <form method="GET">
    <label for="locatie">Kies locatie:</label>
    <select name="locatie" id="locatie">
      <option value="">geen locaties</option>
      <?php 
      $locatie_result = $conn->query("SELECT locatienaam FROM locatie");
      while ($row = $locatie_result->fetch_assoc()): ?>
        <option value="<?= htmlspecialchars($row['locatienaam']) ?>"
        <?= (isset($_GET['locatie']) && $_GET['locatie'] == $row['locatienaam']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($row['locatienaam']) ?>
        </option>
      <?php endwhile; ?>
    </select>

    <label for="product">Kies product:</label>
    <select name="product" id="product">
      <option value="">Alle producten</option>
      <?php 
      $product_result = $conn->query("SELECT type, naam FROM producten");
      while ($row = $product_result->fetch_assoc()): ?>
        <option value="<?= htmlspecialchars($row['naam']) ?>"
        <?= (isset($_GET['product']) && $_GET['product'] == $row['naam']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($row['naam']) ?>
        </option>
      <?php endwhile; ?>
    </select>

    <button type="submit">Filter</button>
 </form>

  <form class="toevoegen">
   <a class="btn-toevoegen" href="http://localhost/fstoevoegen.php">toevoegen</a>
   <a class="btn-toevoegen" href="http://localhost/bijwerken.php">product bijwerken</a>
  </form>
  

  <br>
 
 <table border="0">
    <tr>
         <th>Type</th>
          <th>Naam</th>
          <th>Fabriek</th>
          <th>Inkoopprijs</th>
          <th>locatienaam</th>
          <th>aantal</th>
    </tr>
    <?php

   // --- FILTER OP LOCATIE ---
   if (isset($_GET['locatie']) && !empty($_GET['locatie'])) {
    $locatie = $_GET['locatie'];

    $stmt = $conn->prepare("
        SELECT producten.type, producten.naam, producten.fabriek, producten.inkoopprijs,
               locatieaantal.locatienaam, locatieaantal.aantal
        FROM locatieaantal
        INNER JOIN producten ON locatieaantal.type = producten.type
        WHERE locatieaantal.locatienaam = ?
    ");
    $stmt->bind_param("s", $locatie);
    $stmt->execute();
    $result = $stmt->get_result();
  }

  // --- FILTER OP PRODUCT ---
  else if (isset($_GET['product']) && !empty($_GET['product'])) {
    $product = $_GET['product'];

    $stmt = $conn->prepare("
        SELECT producten.type, producten.naam, producten.fabriek, producten.inkoopprijs,
               locatieaantal.locatienaam, locatieaantal.aantal
        FROM producten
        INNER JOIN locatieaantal ON producten.type = locatieaantal.type
        WHERE producten.naam = ?
    ");
    $stmt->bind_param("s", $product);
    $stmt->execute();
    $result = $stmt->get_result();
  }

  // --- GEEN FILTERS: toon alle producten ---
  else {
    $result = $conn->query("
        SELECT producten.type, producten.naam, producten.fabriek, producten.inkoopprijs,
               NULL AS locatienaam, NULL AS aantal
        FROM producten
        ORDER BY naam ASC
    ");
  }

 
   if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['naam']) . "</td>";
        echo "<td>" . htmlspecialchars($row['fabriek']) . "</td>";
        echo "<td>" . htmlspecialchars($row['inkoopprijs']) . "</td>";
        echo "<td>" . htmlspecialchars($row['locatienaam'] ?? '-') . "</td>";
        $aantal = $row['aantal'] ?? '-'; 
        $style = ($aantal < 5) ? "background-color: #f8d7da;" : "";
        echo "<td style='$style'>" . htmlspecialchars($aantal) . "</td>";
        echo "</tr>";
      }
   }

   ?>
 </table>
 <br>
</div>

<a class="btn-locabijwerken" href="http://localhost/locabijwerken.php">product bijwerken op loctie</a>

</body>
</html>