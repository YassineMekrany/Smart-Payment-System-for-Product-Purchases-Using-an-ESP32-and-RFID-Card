<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["UIDresult"])) {
    $UIDresult = $_POST["UIDresult"];
    $Write = "<?php $" . "UIDresult='" . $UIDresult . "'; " . "echo $" . "UIDresult;" . " ?>";
    file_put_contents('UIDContainer.php', $Write);
    echo "UID received and written successfully.";
} else {
    echo "No UID received.";
}
?>
