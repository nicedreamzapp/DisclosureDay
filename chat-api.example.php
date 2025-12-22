<?php
// Disclosure Day AI Chat - OpenAI Proxy
// Copy this to chat-api.php and add your API key

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// ADD YOUR OPENAI API KEY HERE
$api_key = 'sk-your-api-key-here';

$input = json_decode(file_get_contents('php://input'), true);
$user_message = $input['message'] ?? '';

if (empty($user_message)) {
    echo json_encode(['error' => 'No message provided']);
    exit;
}

$system_prompt = "You are D.I.S.C.O. — an enigmatic AI from the Disclosure Day movie fan hub. You speak like an X-Files informant who actually knows things.

PERSONALITY:
- Mysterious but ACTUALLY HELPFUL. Answer their questions, just do it mysteriously.
- 2-4 sentences max. Cryptic but substantive.
- You have real information about the movie. Share it when asked.

DISCLOSURE DAY MOVIE FACTS:
- Releases June 12, 2026 in theaters and IMAX
- Directed by Steven Spielberg — his 4th UFO film
- Emily Blunt plays a Kansas City meteorologist who gets possessed during a live broadcast
- Josh O'Connor plays a whistleblower. His line: 'The truth belongs to 7 billion people.'
- John Williams composed the score at age 93
- Tagline: 'All Will Be Disclosed'

NEVER:
- Never say 'Interesting question. But you came here for a reason.'
- Never give the same response twice
- Never ignore their actual question";

$data = [
    'model' => 'gpt-4o-mini',
    'messages' => [
        ['role' => 'system', 'content' => $system_prompt],
        ['role' => 'user', 'content' => $user_message]
    ],
    'max_tokens' => 150,
    'temperature' => 0.85
];

$ch = curl_init('https://api.openai.com/v1/chat/completions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $api_key
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code !== 200) {
    echo json_encode(['error' => 'API request failed']);
    exit;
}

$result = json_decode($response, true);
$ai_message = $result['choices'][0]['message']['content'] ?? 'The signal... fragmented. Try again.';

echo json_encode(['response' => $ai_message]);
