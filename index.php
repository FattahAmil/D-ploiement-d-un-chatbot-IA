<?php


$urlApiMistral = getenv('urlApiMistral');
$apiKeyMistral = getenv('apiKey');
$userInput = $_POST['message'] ?? json_decode(file_get_contents('php://input'), true)['message'] ?? null;

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

// Préparation des messages (simulateur de chatbot SNCF)
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

if (isset($responseData['choices'][0]['message']['content'])) {
    echo $responseData['choices'][0]['message']['content'];
} else {
    echo "Erreur : Impossible d'extraire le contenu de la réponse.";
}

curl_close($ch);
?>
