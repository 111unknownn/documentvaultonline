<?php

function revertToVersion($commitHash, $filePath) {
    chdir(dirname($filePath));
    exec("git checkout " . escapeshellarg($commitHash) . " -- " . escapeshellarg(basename($filePath)));
}

// Example usage
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['commit_hash']) && isset($_POST['file_path'])) {
    $commitHash = $_POST['commit_hash'];
    $filePath = $_POST['file_path'];
    revertToVersion($commitHash, $filePath);
    echo "File reverted to version $commitHash.";
}

?>
