<?php


// logUserAction
function logUserAction($action, $userId) {
    $logEntry = date('Y-m-d H:i:s') . " - User ID: $userId - Action: $action" . PHP_EOL;
    file_put_contents(__DIR__ . '/../logs/user_actions.log', $logEntry, FILE_APPEND);
}
