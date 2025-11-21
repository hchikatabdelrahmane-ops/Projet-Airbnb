<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = "localhost";
$user = "root";
$password = "";
$database = "logementgit a";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

if (isset($_POST['ajouter'])) {
    $name = $_POST['name'];
    $picture_url = $_POST['picture_url'];
    $host_name = $_POST['host_name'];
    $price = $_POST['price'];
    $host_thumbnail_url = $_POST['host_thumbnail_url'];
    $city = $_POST['city'] ?? '';

    $sql_insert = "INSERT INTO listings (name, picture_url, host_name, price, host_thumbnail_url, city)
                   VALUES ('$name', '$picture_url', '$host_name', '$price', '$host_thumbnail_url', '$city')";
    $conn->query($sql_insert);
}

$par_page = 10; 
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$start = ($page - 1) * $par_page;

$tri_options = ['name','city','price','host_name'];
$tri = isset($_GET['tri']) && in_array($_GET['tri'], $tri_options) ? $_GET['tri'] : 'name';

$sql = "SELECT * FROM listings ORDER BY $tri LIMIT $start, $par_page";
$result = $conn->query($sql);

$sql_total = "SELECT COUNT(*) as total FROM listings";
$total = $conn->query($sql_total)->fetch_assoc()['total'];
$total_pages = ceil($total / $par_page);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Liste des logements</title>
    <style>
        body { font-family: Arial; max-width:900px; margin:20px auto; background:#f9f9f9; color:#333; }
        h1,h2 { text-align:center; }
        form { background:#fff; padding:15px; border:1px solid #ccc; margin:0 auto 20px; max-width:500px; }
        form input, form button, form select { width:100%; padding:8px; margin-top:5px; }
        div.logement { background:#fff; padding:15px; margin:0 auto 15px; max-width:500px; text-align:center; border:1px solid #ccc; }
        div.logement img { max-width:100%; height:auto; margin-top:10px; }
        .pagination { text-align:center; margin-top:20px; }
        .pagination strong { color:red; }
    </style>
</head>
<body>

<h1>Liste des logements</h1>

<h2>Ajouter une annonce</h2>
<form method="POST">
    <label for="name">Nom du logement :</label>
    <input type="text" id="name" name="name" required>

    <label for="picture_url">URL de l'image :</label>
    <input type="text" id="picture_url" name="picture_url" required>

    <label for="host_name">Nom du propriétaire :</label>
    <input type="text" id="host_name" name="host_name" required>

    <label for="price">Prix par nuit (€) :</label>
    <input type="number" id="price" name="price" required>

    <label for="host_thumbnail_url">Photo de l'hôte (URL) :</label>
    <input type="text" id="host_thumbnail_url" name="host_thumbnail_url">

    <label for="city">Ville :</label>
    <input type="text" id="city" name="city">

    <button type="submit" name="ajouter">Ajouter l'annonce</button>
</form>

<h2>Tri (/3)</h2>
<form method="GET">
    <label for="tri">Trier par :</label>
    <select id="tri" name="tri" onchange="this.form.submit()">
        <option value="name" <?= ($tri=='name')?'selected':'' ?>>Nom</option>
        <option value="city" <?= ($tri=='city')?'selected':'' ?>>Ville</option>
        <option value="price" <?= ($tri=='price')?'selected':'' ?>>Prix</option>
        <option value="host_name" <?= ($tri=='host_name')?'selected':'' ?>>Propriétaire</option>
    </select>
    <input type="hidden" name="page" value="<?= $page ?>">
</form>

<hr>

<?php
if ($result->num_rows > 0) {
    while ($l = $result->fetch_assoc()) {
        echo "<div class='logement'>";
        echo "<h2>" . htmlspecialchars($l['name']) . "</h2>";

        if (!empty($l['picture_url'])) {
            echo "<img src='" . htmlspecialchars($l['picture_url']) . "' alt='image logement'>";
        }

        if (!empty($l['host_thumbnail_url'])) {
            echo "<img src='" . htmlspecialchars($l['host_thumbnail_url']) . "'
                     width='80' style='border-radius:50%; margin-top:10px;' alt='photo hôte'>";
        }

        echo "<p>Hôte : " . htmlspecialchars($l['host_name']) . "</p>";
        echo "<p>Ville : " . (!empty($l['city']) ? htmlspecialchars($l['city']) : '-') . "</p>";
        echo "<p>Prix : " . htmlspecialchars($l['price']) . " €</p>";
        echo "</div>";
    }
} else {
    echo "<p style='text-align:center;'>Aucun logement trouvé.</p>";
}
?>

<div class="pagination">
<?php
for ($i = 1; $i <= $total_pages; $i++) {
    if ($i == $page) {
        echo "<strong>[$i]</strong> ";
    } else {
        echo "<a href='?page=$i&tri=$tri'>$i</a> ";
    }
}
?>
</div>

</body>
</html>

<?php
$conn->close();
?>
