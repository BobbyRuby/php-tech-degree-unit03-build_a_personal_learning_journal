<?php
include('inc/debug_functions.php');
include('inc/functions.php');
include('inc/Class_SqliteCommunicator.php');
include('inc/Class_JournalCommunicator.php');
// Create connection
$sqlCom = new Class_JournalCommunicator();
$sqlCom->setDsn(__DIR__.'/inc/journal.db');
$sqlCom->setPdoConnection();

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
        <h1>My Journal</h1>
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
                <form action="new.php" method="post" id="form-new">

<?php elseif ( isEditEntry() ):
    if( ! empty($_GET) ) {
    $entryID = filter_input(INPUT_GET, 'entryID', FILTER_VALIDATE_INT);
    ?>
        <section>
        <div class="container">
            <div class="edit-entry">
                <h2>Edit Entry ID# <?php echo $entryID ?></h2>
                <form action="edit.php?entryID=<?php echo $entryID ?>" method="post" id="form-edit">
<?php
    } else {
        // Not from GET, is it from POST?
        if( empty($_POST) ){
            // Where you come from?  Redirect to index.php
            header("Location: ./index.php");
            exit;
        }
    }
 elseif ( isEntryDetail() ):
    // Set what we need for header area
    $no_entries_error = filter_input(INPUT_GET, 'no_entries_error', FILTER_VALIDATE_BOOLEAN);
    $tagID = filter_input(INPUT_GET, 'tagID', FILTER_VALIDATE_INT);
    $tag = $sqlCom->getAssocRowById($tagID, 'tags');

    if( ! empty($_GET) ) {
        if ( $no_entries_error ) {
            if (!$tagID):
                ?>
                <div id="error">
                    <p>No more entries in that direction! <br/>
                        <a href="./index.php">Click here to return to the index!</a>
                    </p>
                </div>
            <?php
            else: ?>
                <div id="error">
                    <p>No more entries in that direction tagged as "<?php echo $tag['name'] ?>"!<br/>
                        <a href="./tagEntries.php?tagID=<?php echo $tagID ?>">Click here to return to the tag "<?php echo $tag['name'] ?>" index!</a>
                    </p>
                </div>
                <?php
            endif;
        }
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
            header("Location: ./index.php");
            exit;
        }
    }
    elseif ( isTagEntries() ) :
    // Get reference to the tag
    $tagID = filter_input(INPUT_GET, 'tagID', FILTER_VALIDATE_INT);
    $tag = $sqlCom->getAssocRowById($tagID, 'tags');
    ?>
    <section>
    <h2>Entries for tag "<?php echo $tag['name'] ?>"</h2>
        <div class="container">
            <div class="entry-list">
<?php
    else: // (index.php) ?>
    <section>
        <div class="container">
            <div class="entry-list">
<?php endif;