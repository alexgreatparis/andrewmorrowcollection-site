<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "POST fonctionne !";
} else {
    echo "Méthode : " . $_SERVER['REQUEST_METHOD'];
}
?> 