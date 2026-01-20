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
    header("Location: fsinlog.php");
    exit();
}

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: home.php"); 
    exit();
}



//medewerker toevoegen
if (isset($_POST['gebruiker_toevoegen'])) {
    $email = $_POST['email1'];
    $wachtwoord = password_hash($_POST['wachtwoord'], PASSWORD_DEFAULT);
    $rol = $_POST['rol'];

    // Check of gebruiker al bestaat
    $check = $conn->prepare("SELECT email FROM medewerkers WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<p style='color:red;'>Deze gebruiker bestaat al.</p>";
    } else {
        $stmt = $conn->prepare("INSERT INTO medewerkers (email, password, rol) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $wachtwoord, $rol);
        
        if ($stmt->execute()) {
      
        echo '<div class="alert alert-success">
          medewerker is toegevoegd<br>
          </div>';
        echo "<script>
         setTimeout(function() {
            window.location.href = 'http://localhost/fsvoorraad.php';
        }, 2000);
        </script>";}
        $stmt->close();
    }
    
    $check->close();
}

// Wachtwoord wijzigen
if (isset($_POST['wijzig_wachtwoord'])) {
    $email = $_POST['email2'];
    $nieuw_wachtwoord = password_hash($_POST['nieuw_wachtwoord'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE medewerkers SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $nieuw_wachtwoord, $email);
     
    if ($stmt->execute()) {
      
        echo '<div class="alert alert-success">
          wachtwoord is gewijzigd<br>
          </div>';
        echo "<script>
         setTimeout(function() {
            window.location.href = 'http://localhost/fsvoorraad.php';
        }, 2000);
        </script>";}
    $stmt->close();
}

// Gebruiker verwijderen
if (isset($_POST['verwijder'])) {
    $email = $_POST['email3'];
    $stmt = $conn->prepare("DELETE FROM medewerkers WHERE email = ?");
    $stmt->bind_param("s", $email);
     
    if ($stmt->execute()) {
      
        echo '<div class="alert alert-success">
          medewerker is verwijderd<br>
          </div>';
        echo "<script>
         setTimeout(function() {
            window.location.href = 'http://localhost/fsvoorraad.php';
        }, 2000);
        </script>";}
    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Beheerderspaneel</title>
    <link rel="stylesheet" href="css/medewerkers.css">
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


<h2>Gebruiker toevoegen</h2>
<form method="POST">
    <input type="text" name="email1" placeholder="gebruikersnaam" required>
    <input type="text" name="wachtwoord" placeholder="Wachtwoord" required>
    <label for="locatie">rol</label>
    <select name="rol" id="rol">
       <option value="medewerker">medewerker</option>
       <option value="admin">admin</option>
    </select>
    <button type="submit" name="gebruiker_toevoegen">Toevoegen</button>
</form>

<h2>Wachtwoord wijzigen</h2>
<form method="POST">
    <input type="text" name="email2" placeholder="gebruikersnaam" required>
    <input type="text" name="nieuw_wachtwoord" placeholder="Nieuw wachtwoord" required>
    <button type="submit" name="wijzig_wachtwoord">Wijzig</button>
</form>

<h2>Gebruiker verwijderen</h2>
<form method="POST">
    <input  type="text" name="email3" placeholder="gebuikersnaam" required>
    <button type="submit" name="verwijder">Verwijderen</button>
</form>


</body>
</html>