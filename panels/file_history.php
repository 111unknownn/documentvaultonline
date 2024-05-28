<?php

function getGitHistory($filePath) {
    chdir(dirname($filePath));
    $history = [];
    exec("git log --pretty=format:'%h - %an, %ar : %s' " . escapeshellarg(basename($filePath)), $output);
    foreach ($output as $line) {
        $history[] = $line;
    }
    return $history;
}

$filePath = isset($_GET['file']) ? $_GET['file'] : 'C:/xampp/htdocs/docuvault/panels/encryptedFiles/example.txt.enc';
$history = getGitHistory($filePath);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File History</title>
</head>
<body>
    <h1>File History</h1>
    <ul>
        <?php foreach ($history as $entry): ?>
            <li><?php echo htmlspecialchars($entry, ENT_QUOTES, 'UTF-8'); ?></li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
