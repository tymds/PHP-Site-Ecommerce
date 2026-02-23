<?php
session_start();
require_once 'config/database.php';

$homeResult = $oursql->query("SELECT * FROM /*SQL_ARTICLE*/ ORDER BY /*SQL_ID*/ DESC");#ADD proper SQL query here

echo "Projet OK";
?>

<h1>Bienvenue sur projet E-PHP</h1>


<?php while($article = $homeResult->fetch_assoc()): ?>
    <article class="article">
        <h2><?php echo $article['/*SQL_TITLE*/']; ?></h2> <!-- Replace with actual column name -->
        <p><?php echo $article['/*SQL_CONTENT*/']; ?></p> <!-- Replace with actual column name -->
    </article>
<?php endwhile;


