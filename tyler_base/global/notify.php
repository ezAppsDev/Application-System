<?php
if(isset($_SESSION["errormsg"]) && $_SESSION["errormsg"] != NULL) {
    if ($_SESSION["errortype"] === "success") {
        $message = '<div class="alert alert-success" role="alert">'.$_SESSION['errormsg'].'</div>';
        $_SESSION["errormsg"] = NULL;
    } elseif ($_SESSION["errortype"] === "danger") {
        $message = '<div class="alert alert-danger" role="alert">'.$_SESSION['errormsg'].'</div>';
        $_SESSION["errormsg"] = NULL;
    } elseif ($_SESSION["errortype"] === "warning") {
        $message = '<div class="alert alert-warning" role="alert">'.$_SESSION['errormsg'].'</div>';
        $_SESSION["errormsg"] = NULL;
    } elseif ($_SESSION["errortype"] === "info") {
        $message = '<div class="alert alert-info" role="alert">'.$_SESSION['errormsg'].'</div>';
        $_SESSION["errormsg"] = NULL;
    }
}