<?php 
$success = mail('tomcarpenteremail@gmail.com','hello world','boop');
if (!$success) {
    $errorMessage = error_get_last()['message'];
}
?>