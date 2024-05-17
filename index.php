<?php
header('Content-Type: application/json');

// Load responses from JSON file based on the current state
function loadResponses($state) {
    $filename = 'responses_' . $state . '.json'; // Different files for different states
    if (file_exists($filename)) {
        $json = file_get_contents($filename);
        return json_decode($json, true);
    }
    return null;
}

function getChatbotResponse($input, $state) {
    $responses = loadResponses($state);
    if ($responses === null) {
        return "Sorry, I'm unable to find appropriate responses for this state.";
    }
    $input = strtolower($input);
    foreach ($responses as $key => $response) {
        if (strpos($input, $key) !== false) {
            return $response;
        }
    }
    return $responses['unknown'] ?? "I'm not sure how to help with that.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $userInput = $data['message'] ?? '';
    $currentState = $data['state'] ?? 'initial';  // Default to 'initial' state if none provided

    $response = getChatbotResponse($userInput, $currentState);
    echo json_encode(["response" => $response]);
} else {
    echo json_encode(["error" => "Only POST method is supported"]);
}
?>
