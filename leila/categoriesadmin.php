<?php
require_once 'variables.php';
require_once 'tools.php';

session_start();
if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] != "admin") die ("Bitte <a href='login.php'>anmelden</a>");

if (isset($_POST['addtopcategory'])){

	$connection = new mysqli($db_hostname, $db_username, $db_password, $db_database);
	if ($connection->connect_error) die($connection->connect_error);
	
	$categoryname = sanitizeMySQL($connection, $_POST['categoryname']);
	$error = isempty($categoryname, "Kategoriename");
	
	if ($error == ""){
		$query = "INSERT INTO categories (name) VALUES ('$categoryname')" ;
		$result = $connection->query($query);
		if (!$result) die ("Database query error" . $connection->error);
	}		
}

if (isset($_POST['addsubcategory'])){

	$connection = new mysqli($db_hostname, $db_username, $db_password, $db_database);
	if ($connection->connect_error) die($connection->connect_error);

	$categoryname = sanitizeMySQL($connection, $_POST['topcategory']);
	$subcategoryname = sanitizeMySQL($connection, $_POST['subcategoryname']);

	$error = isempty($categoryname, "Kategoriename");
	$error .= isempty($subcategoryname, "Subkategoriename");
	
	if ($error == ""){
		$query = "INSERT INTO categories (ischildof, name) VALUES ('$categoryname','$subcategoryname')" ;
		$result = $connection->query($query);
		if (!$result) die ("Database query error" . $connection->error);
	}
}

if (isset($_POST['deletecategories'])){
	$connection = new mysqli($db_hostname, $db_username, $db_password, $db_database);
	if ($connection->connect_error) die($connection->connect_error);
	
	if (isset($_POST['subcategory'])) {
		$subcat = sanitizeMySQL($connection, $_POST['subcategory']);
		$query = "DELETE FROM categories WHERE category_id = $subcat";
		echo "subcat " . $_POST['subcategory'];
		echo $query;
		$result = $connection->query($query);
		if (!$result) die ("Database delete error" . $connection->error);
	} else {
		// enabled cascading delete in mySQL
		$topcat = sanitizeMySQL($connection, $_POST['topcategory']);
		$query = "DELETE FROM categories WHERE category_id = $topcat";
		echo $query;
		$result = $connection->query($query);
		if (!$result) die ("Database delete error" . $connection->error);
	}

}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Kategorie Administration</title>
	<link rel="stylesheet" href="leila.css" type="text/css">
</head>
<body>
<?php include 'menu.php';?>

<div id='content'>

<?php if (isset($error) && $error != "") echo "<div class='errorclass'>Fehler: $error </div>";
?>

<h1> Kategorien verwalten</h1>
<h3>Top Kategorie hinzuf&uuml;gen</h3>
<form method="post" action="categoriesadmin.php">
	<input type="hidden" name="addtopcategory" value="true">
	<input type="text" name="categoryname">
	<input type="submit" value="Kat hinzuf&uuml;gen">
</form>

<h3>Unterkategorie hinzuf&uuml;gen</h3>
<form method="post" action="categoriesadmin.php">
	<input type="hidden" name="addsubcategory" value ="true">
	<select name="topcategory" size="1">
		<?php gettopcategories(); ?>
	</select>
	<input type="text" name="subcategoryname">
	<input type="submit" value="Sub Kat hinzuf&uuml;gen">
</form>



<h3>Kategorie l&ouml;schen</h3>
<form method="post" action="categoriesadmin.php">
	<select name="topcategory" size="1">
		<?php gettopcategories(); ?>
	</select>
	<?php 
	if (isset($_POST['getsubcategories'])){
		echo '<select name ="subcategory" size="1">';
		getsubcategories($_POST['topcategory']);
		echo '</select>';
	} 
	
	?>
	<input type="submit" name="getsubcategories" value="Sub Kat anzeigen">
	<input type="submit" name="deletecategories" value="Kat l&ouml;schen">
	
</form>
</div>
</body>
</html>

