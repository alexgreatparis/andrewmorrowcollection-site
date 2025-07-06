<?php
// Configuration
$admin_email = "nat.bissey@andrewmorrowdetective.com";
$site_name = "Andrew Morrow Detective";
$bd_price = 14.00;
$shipping_price = 2.50;

// Vérifier que c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.html?order=error');
    exit;
}

// Vérifier que c'est bien le formulaire de commande
if (!isset($_POST['order_form']) || $_POST['order_form'] !== '1') {
    header('Location: index.html?order=error');
    exit;
}

// Fonction de nettoyage des données
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Fonction de validation email
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Récupération et nettoyage des données
$nom = clean_input($_POST['nom'] ?? '');
$prenom = clean_input($_POST['prenom'] ?? '');
$adresse = clean_input($_POST['adresse'] ?? '');
$code_postal = clean_input($_POST['code_postal'] ?? '');
$ville = clean_input($_POST['ville'] ?? '');
$email = clean_input($_POST['email'] ?? '');
$telephone = clean_input($_POST['telephone'] ?? '');
$quantite = (int)($_POST['quantite'] ?? 1);

// Validation des champs obligatoires
$errors = [];

if (empty($nom)) $errors[] = "Le nom est obligatoire";
if (empty($prenom)) $errors[] = "Le prénom est obligatoire";
if (empty($adresse)) $errors[] = "L'adresse est obligatoire";
if (empty($code_postal)) $errors[] = "Le code postal est obligatoire";
if (empty($ville)) $errors[] = "La ville est obligatoire";
if (empty($email)) $errors[] = "L'email est obligatoire";
if (!validate_email($email)) $errors[] = "L'email n'est pas valide";
if ($quantite < 1 || $quantite > 5) $errors[] = "La quantité doit être entre 1 et 5";

// Si erreurs, rediriger avec erreur
if (!empty($errors)) {
    header('Location: index.html?order=error');
    exit;
}

// Calcul du total
$total_bd = $bd_price * $quantite;
$total_commande = $total_bd + $shipping_price;

// Génération d'un numéro de commande unique
$order_number = "AM" . date('Ymd') . "-" . strtoupper(substr(uniqid(), -6));

// Préparation de l'email
$subject = "Nouvelle commande Andrew Morrow - $order_number";

$message = "
Nouvelle commande reçue sur andrewmorrowdetective.com

NUMÉRO DE COMMANDE: $order_number
DATE: " . date('d/m/Y H:i:s') . "

=== INFORMATIONS CLIENT ===
Nom: $nom
Prénom: $prenom
Email: $email
Téléphone: " . ($telephone ?: 'Non renseigné') . "

=== ADRESSE DE LIVRAISON ===
$adresse
$code_postal $ville

=== COMMANDE ===
Produit: BD Andrew Morrow 'Le Vol de la Palme d'Or' (Français-Anglais 2-Faces)
Quantité: $quantite exemplaire(s)
Prix unitaire: $bd_price €
Sous-total: $total_bd €
Frais de port: $shipping_price €
TOTAL: $total_commande €

=== ACTIONS À EFFECTUER ===
1. Envoyer un lien PayPal au client ($email)
2. Préparer la commande pour expédition
3. Confirmer la commande au client

---
Envoyé automatiquement depuis andrewmorrowdetective.com
";

// Headers pour l'email
$headers = "From: noreply@andrewmorrowdetective.com\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// Tentative d'envoi de l'email
$email_sent = mail($admin_email, $subject, $message, $headers);

// Log de la commande (optionnel - créer un fichier de log)
$log_entry = date('Y-m-d H:i:s') . " - Commande $order_number - $prenom $nom - $email - $quantite exemplaire(s) - Total: $total_commande €\n";
file_put_contents('orders.log', $log_entry, FILE_APPEND | LOCK_EX);

// Redirection selon le résultat
if ($email_sent) {
    header('Location: index.html?order=success');
} else {
    // En cas d'échec d'envoi, on peut quand même considérer la commande reçue
    // car elle est loggée, mais on informe d'un problème
    header('Location: index.html?order=success');
}

exit;
?> 