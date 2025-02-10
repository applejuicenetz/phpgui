<?php
session_start();

if(!empty($_GET['api_code']))
{
    if($_GET['api_code'] == "nub")
    {
        echo json_encode(['newUploadCount' => $_SESSION['aj']['notification']['uploads']]);
    }
}
else
{
    echo"EROOR";
}