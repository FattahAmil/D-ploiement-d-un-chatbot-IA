<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

// Pour les requêtes de prévalidation CORS (préflight)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(204);
    exit();
}
// Définir le chemin du fichier de log
$log_file = '/var/log/inoui/access.log';

// Obtenir l'horodatage actuel
$timestamp = date('Y-m-d H:i:s');

// Obtenir l'adresse IP du client
$client_ip = $_SERVER['REMOTE_ADDR'];

// Variables d'environnement
$urlApiMistral = getenv('urlApiMistral');
$apiKeyMistral = getenv('apiKey');


$userInput = $_POST['message'] ?? json_decode(file_get_contents('php://input'), true)['message'] ?? null;

// Vérification des variables d'environnement
if (!$urlApiMistral) {
    echo "Erreur : La variable d'environnement 'urlApiMistral' est manquante.";
    exit();
}

if (!$apiKeyMistral) {
    echo "Erreur : La variable d'environnement 'apiKey' est manquante.";
    exit();
}

if (!$userInput) {
    echo "Erreur : Aucun message utilisateur n'a été fourni.";
    exit();
}

$messages = [
    ["role" => "system", "content" => "Tu es Inoui, un assistant virtuel de la SNCF. Réponds toujours de manière claire et utile sur les horaires, tarifs, billets, services et infos pratiques de la SNCF. Sois poli et professionnel, avec une petite touche chaleureuse."],
    ["role" => "user", "content" => $userInput],
];

// Configuration de la requête
$data = [
    "model" => "mistral-large-latest",
    "messages" => $messages,
    "temperature" => 0.7,
];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $urlApiMistral . "/v1/chat/completions");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
    'Authorization: Bearer ' . $apiKeyMistral,
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'Erreur cURL : ' . curl_error($ch);
    exit();
}

http_response_code(curl_getinfo($ch, CURLINFO_HTTP_CODE));

$responseData = json_decode($response, true);

// Enregistrer les logs de la requête et de la réponse
$log_entry = "[" . $timestamp . "] " . $client_ip . " - " . $userInput . " - " . json_encode($responseData) . "\n";
file_put_contents($log_file, $log_entry, FILE_APPEND);

// Retourner la réponse sous forme de JSON
if (isset($responseData['choices'][0]['message']['content'])) {
    echo $responseData['choices'][0]['message']['content'];
} else {
    echo "Erreur : Impossible d'extraire le contenu de la réponse.";
}

curl_close($ch);
?>
