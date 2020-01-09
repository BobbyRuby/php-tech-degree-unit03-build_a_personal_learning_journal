<?php
include('inc/debug_functions.php');
include('inc/functions.php');
include('model/Class_SqliteCommunicator.php');
// Create connection
$sqlCom = new Class_SqliteCommunicator();
$sqlCom->setDsn(__DIR__.'/inc/journal.db');
$sqlCom->setPdo();

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>MyJournal</title>
    <link href="https://fonts.googleapis.com/css?family=Cousine:400" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Work+Sans:600" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/site.css">
</head>
<body>
<header>
    <div class="container">
        <div class="site-header">
            <a class="logo" href="index.php"><i class="material-icons">library_books</i></a>
            <a class="button icon-right" href="new.php"><span>New Entry</span> <i class="material-icons">add</i></a>
        </div>
    </div>
</header>
<?php

// Decide heading to output
if( isNewEntry() ):
?>
    <section>
        <div class="container">
            <div class="new-entry">
                <h2>New Entry</h2>
                <form action="new.php" method="post">

<?php elseif ( isEditEntry() ):
    if( ! empty($_GET) ) {
        // Get entryID from GET
        $entryID = filter_input(INPUT_GET, 'entryID', FILTER_VALIDATE_INT);
        $entryData = $sqlCom->getSingleRow($entryID);
    ?>
        <section>
        <div class="container">
            <div class="edit-entry">
                <h2>Edit Entry ID# <?php echo $entryID ?></h2>
                <form action="edit.php?entryID=<?php echo $entryID ?>" method="post">
<?php
    } else {
        // Not from GET, is it from POST?
        if( empty($_POST) ){
            // Where you come from?  Redirect to index.php
            // Redirect to detail page using newly inserted id
            header("Location: ./index.php");
            exit;
        }
    }
?>


<?php elseif ( isEntryDetail() ):
    if( ! empty($_GET) ) {
        // Get entryID from GET
        $entryID = filter_input(INPUT_GET, 'entryID', FILTER_VALIDATE_INT);

        if( ! empty($_GET['delete']) && $_GET['delete'] === 'yes' ){
            $sqlCom->deleteEntry($entryID);
        }

        $entryData = $sqlCom->getSingleRow($entryID);
        ?>
    <section>
        <div class="container">
            <div class="entry-list single">
                <article>
    <?php
    } else {
        // Not from GET, is it from POST?
        if( empty($_POST) ){
            // Where you come from?  Redirect to index.php
            // Redirect to detail page using newly inserted id
            header("Location: ./index.php");
            exit;
        }
    }
    ?>

<?php else: // (index.php) ?>
    <section>
        <div class="container">
            <div class="entry-list">
<?php endif;