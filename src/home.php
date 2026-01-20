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

  $gebruikersnaam = $_SESSION['gebruikersnaam'];

?>




<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>bestellingen</title>
<link rel="stylesheet" href="css/homepg.css">
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

<div class = "h1"><h1>welkom <?php echo htmlspecialchars($_SESSION['gebruikersnaam']); ?></h1></div>

<div class= "home">
    
    

 <div class = "lagen_aantal">
    <h2>product aantal te laat</h2>
    <table border="0">
    <tr>
         
          <th>Naam</th>
          <th>locatienaam</th>
          <th>aantal</th>
    </tr>
   <?php

    $stmt = $conn->prepare("select producten.naam, locatieaantal.locatienaam, locatieaantal.aantal
        from locatieaantal
        inner join producten ON locatieaantal.type = producten.type
        where aantal < 7
        LIMIT 4;");
    $stmt->execute();
    $result = $stmt->get_result();
    
 
   if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['naam']) . "</td>";
        echo "<td>" . htmlspecialchars($row['locatienaam'] ?? '-') . "</td>";
        $aantal = $row['aantal'] ?? '-'; 
        $style = ($aantal < 5) ? "background-color: #f8d7da;" : "";
        echo "<td style='$style'>" . htmlspecialchars($aantal) . "</td>";
        echo "</tr>";
      }
   }

   ?>
 </table>
 </div>

 <div class = "recente_bestellingen">
    <h2>recente bestellingen</h2>
    <table border="0">
    <tr>
          <th>bestelnr</th>
          <th>productnaam</th>
          <th>locatienaam</th>
          <th>aantal</th>
          <th>bedrag</th>
          <th>besteldate</th>
          <th>aankomstdate</th>
    </tr>
   <?php

    $stmt = $conn->prepare("select bestelnr, productnaam, locatienaam,aantal, bedrag, besteldate, aankomstdate
      from bestellingen
      ORDER BY besteldate DESC
     LIMIT 4;");
    $stmt->execute();
    $result = $stmt->get_result();
    
 
   if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['bestelnr']) . "</td>";
        echo "<td>" . htmlspecialchars($row['productnaam']) . "</td>";
        echo "<td>" . htmlspecialchars($row['locatienaam']) . "</td>";
        echo "<td>" . htmlspecialchars($row['aantal']) . "</td>";
        echo "<td>" . htmlspecialchars($row['bedrag']) . "</td>";
        echo "<td>" . htmlspecialchars($row['besteldate']) . "</td>";
        echo "<td>" . htmlspecialchars($row['aankomstdate'] ?? '-') . "</td>";
        echo "</tr>";
      }
   }

   ?>
 </table>
 </div>




 <div class = "onbezorgd_bestellingen">
    <h2>nog niet bezorgd</h2>
    <table border="0">
    <tr>
          <th>bestelnr</th>
          <th>productnaam</th>
          <th>locatienaam</th>
          <th>besteldate</th>
    </tr>
   <?php

    $stmt = $conn->prepare("select bestelnr, productnaam, locatienaam, besteldate
      from bestellingen
      WHERE aankomstdate is NULL;");
    $stmt->execute();
    $result = $stmt->get_result();
    
 
   if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['bestelnr']) . "</td>";
        echo "<td>" . htmlspecialchars($row['productnaam']) . "</td>";
        echo "<td>" . htmlspecialchars($row['locatienaam']) . "</td>";
        echo "<td>" . htmlspecialchars($row['besteldate']) . "</td>";
        echo "</tr>";
      }
   }

   ?>
 </table>
 </div>

</div>