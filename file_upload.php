<?php    
    ob_start();
    session_start();
    
    $user_id = $_SESSION["user_id"];
    $username = $_SESSION['username'];
    $permission_level = $_SESSION["permission_level"];
    if(!$user_id){
        header("Location: https://admin.authenticmerch.com");
    }
    else{
        //add_activity($user_id,"Visited Dashboard");
    }
    error_reporting( E_ALL );
    ini_set( "display_errors", 1 );
    include_once("includes/inc-dbc-conn.php");


    
?>
 <html>
    <head>
    <title>Upload Attachments - RCHQ Admin Area</title>
    <meta name="robots" content="noindex,nofollow"/>
    <link rel="stylesheet" type="text/css" href="/styles/dropzone.css" />
    <script type="text/javascript" src="/scripts/dropzone.js"></script>
    <?php
        include_once("includes/inc-html-header.php");
    ?>
    </head>
    <body>
    <?php include_once("includes/inc-header.php"); ?>
    <div class="container skip-nav">
        <h2>Example: Drag and Drop File Upload</h2>
        <div class="file_upload" style="max-width:80%">
            <form action="file_upload.php" class="dropzone" method="POST">
                <div class="dz-message needsclick">
                    <strong>Drop files here or click to upload.</strong><br />
                    <span class="note needsclick">(This is just a demo. The selected files are <strong>not</strong> actually uploaded.)</span>
                </div>
                <input type="submit" value="Submit images" class="btn-primary"/>
            </form>
        </div>
    </div>
