<?php
include '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['filename']) && isset($_POST['content'])) {
        $filename = 'upload/encrypted_file/' . $_POST['filename']; // Adjust the path as needed
        $content = $_POST['content'];

        try {
            // Load the existing Word file
            $phpWord = \PhpOffice\PhpWord\IOFactory::load($filename);

            // Modify the document content with the edited content
            // Assuming you want to replace the entire content with the edited content
            $phpWord->getSections()[0]->getElements()[0]->setSectionContent($content);

            // Save the modified document back to the file
            $xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
            $xmlWriter->save($filename);

            echo 'success';
        } catch (\Exception $e) {
            echo 'error: ' . $e->getMessage();
        }
    } else {
        echo 'error';
    }   
}

?>
