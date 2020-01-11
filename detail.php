<?php
include('header.php');
// Set the rest of our variables
$entryID = filter_input(INPUT_GET, 'entryID', FILTER_VALIDATE_INT);
$delete = filter_input(INPUT_GET, 'delete', FILTER_VALIDATE_BOOLEAN);
$nextEntry = filter_input(INPUT_GET, 'nextEntry', FILTER_VALIDATE_BOOLEAN);
$prevEntry = filter_input(INPUT_GET, 'prevEntry', FILTER_VALIDATE_BOOLEAN);

if( $delete ):
    $sqlCom->deleteRow($entryID, 'entries');
    // Remove entryID from all tags
    $sqlCom->removeEntryFromAllTags( $entryID );
elseif ( $nextEntry ) :
    // No tagID
    if ( ! $tagID ):
        // Check to see if the next entry exists
        if ( $row = $sqlCom->getAssocRowById($entryID+1, 'entries') ){
            $entryID++;
            // If it does then display reload page with proper entryID
            header("Location: ./detail.php?entryID=$entryID");
        }
        else {
            // There is not an immediate entry 1 id from here, find the next ROW
            // Return first entry greater than current one
            $pdoSObj = $sqlCom->prepareAndExecuteStatement(' 
                        SELECT *
                        FROM entries 
                        WHERE id > ? 
                        ORDER BY id ASC 
                        LIMIT 1
                    ',
                [$entryID]);
            $rows = $pdoSObj[1]->fetchAll(PDO::FETCH_ASSOC);
            if ( ! empty($rows) ):
                $entryID = $rows[0]['id'];
                // If it does then display reload page with proper entryID
                header("Location: ./detail.php?entryID=$entryID");
            else:
                // If it does then display reload page with proper entryID
                header("Location: ./detail.php?entryID=$entryID&no_entries_error=true");
            endif;
        }
    else:
        // There is a tagID set - we need to get a comma seperated list of entries for this tagID
        $pdoSObj = $sqlCom->prepareAndExecuteStatement('SELECT entries FROM tags WHERE id = ?', [$tagID]);
        $tagEntryIDs = $pdoSObj[1]->fetchAll(PDO::FETCH_ASSOC);

        $tagEntryIDs = explode("," ,$tagEntryIDs[0]['entries']);
        // Perform multi select
        $sql =
            'SELECT *
            FROM ( 
                SELECT * 
                FROM entries
                WHERE';
        $count = count($tagEntryIDs);
        // Loop through this list and build a = sql for second select
        foreach ( $tagEntryIDs as $index => $tagEntryID ){
            // If not last iteration
            if ($index !== $count - 1):
                $sql .= " id = ? OR";
            else:
                $sql .= " id = ?";
            endif;
        }
        $sql .= ') AS tbl WHERE id > ? ORDER BY date LIMIT 1';
        // Push this entryID on for final WHERE
        $tagEntryIDs [] = $entryID;
        // Return first entry greater than current one
        $pdoSObj = $sqlCom->prepareAndExecuteStatement($sql, $tagEntryIDs);

        $rows = $pdoSObj[1]->fetchAll(PDO::FETCH_ASSOC);

        if ( ! empty($rows) ):
            $entryID = $rows[0]['id'];
            // If it does then display reload page with proper entryID
            header("Location: ./detail.php?entryID=$entryID&tagID=$tagID");
        else:
            // If it does then display reload page with proper entryID
            header("Location: ./detail.php?entryID=$entryID&tagID=$tagID&no_entries_error=true");
        endif;
    endif;
elseif ( $prevEntry ) :
    if ( ! $tagID ):
        // Check to see if the next entry exists
        if ( $row = $sqlCom->getAssocRowById($entryID-1, 'entries') ){
            $entryID--;
            // If it does then display reload page with proper entryID
            header("Location: ./detail.php?entryID=$entryID");
        }
        else {
            // There is not an immediate entry 1 id from here, find the next ROW
            // Return first entry less than current one
            $pdoSObj = $sqlCom->prepareAndExecuteStatement(' 
                        SELECT *
                        FROM entries 
                        WHERE id < ? 
                        ORDER BY id DESC 
                        LIMIT 1
                    ',
                [$entryID]);
            $rows = $pdoSObj[1]->fetchAll(PDO::FETCH_ASSOC);
            if ( ! empty($rows) ):
                $entryID = $rows[0]['id'];
                // If it does then display reload page with proper entryID
                header("Location: ./detail.php?entryID=$entryID");
            else:
                // If it does then display reload page with proper entryID
                header("Location: ./detail.php?entryID=$entryID&no_entries_error=true");
            endif;
        }
    else:
        // There is a tagID set - we need to get a comma seperated list of entries for this tagID
        $pdoSObj = $sqlCom->prepareAndExecuteStatement('SELECT entries FROM tags WHERE id = ?', [$tagID]);
        $tagEntryIDs = $pdoSObj[1]->fetchAll(PDO::FETCH_ASSOC);

        $tagEntryIDs = explode("," ,$tagEntryIDs[0]['entries']);
        // Perform multi select
        $sql =
            'SELECT *
            FROM ( 
                SELECT * 
                FROM entries
                WHERE';
        $count = count($tagEntryIDs);
        // Loop through this list and build a = sql for second select
        foreach ( $tagEntryIDs as $index => $tagEntryID ){
            // If not last iteration
            if ($index !== $count - 1):
                $sql .= " id = ? OR";
            else:
                $sql .= " id = ?";
            endif;
        }
        $sql .= ') AS tbl WHERE id < ? ORDER BY date LIMIT 1';
        // Push this entryID on for final WHERE
        $tagEntryIDs [] = $entryID;
        // Return first entry greater than current one
        $pdoSObj = $sqlCom->prepareAndExecuteStatement($sql, $tagEntryIDs);

        $rows = $pdoSObj[1]->fetchAll(PDO::FETCH_ASSOC);

        if ( ! empty($rows) ):
            $entryID = $rows[0]['id'];
            // If it does then display reload page with proper entryID
            header("Location: ./detail.php?entryID=$entryID&tagID=$tagID");
        else:
            // If it does then display reload page with proper entryID
            header("Location: ./detail.php?entryID=$entryID&tagID=$tagID&no_entries_error=true");
        endif;
    endif;
endif;
$entryData = $sqlCom->getAssocRowById($entryID, 'entries');

?>

                    <h2><?php echo $entryData['title'] ?></h2>
                    <time datetime="<?php echo $entryData['date'] ?>"><?php if ( ! empty ($row['date']) ) : echo convertDateTime($row['date']); else: echo 'No date recored'; endif; ?></time>
                    <div class="entry">
                        <h3>Time Spent: </h3>
                        <p><?php echo $entryData['time_spent'] ?></p>
                    </div>
                    <div class="entry">
                        <h3>What I Learned:</h3>
                        <p><?php echo $entryData['learned'] ?></p>
                    </div>
                    <div class="entry">
                        <h3>Resources to Remember:</h3>
                        <ul>
                            <?php
                            $resources = preg_split('/\r\n|[\r\n]/', $entryData['resources']); // Thanks for Regex Long Ears - https://stackoverflow.com/questions/7058168/explode-textarea-php-at-new-lines
                            if ( count($resources) >= 1 && $resources[0] != FALSE):
                                // Resources exist so loop through and display
                                foreach ( $resources as $resource) {
                                    // No http
                                    if( strpos($resource, 'http') === FALSE ) :
                                        // Has valid web extension
                                        if( preg_match('/^(?!\-)(?:(?:[a-zA-Z\d][a-zA-Z\d\-]{0,61})?[a-zA-Z\d]\.){1,126}(?!\d+)[a-zA-Z\d]{1,63}$/', $resource, $matches ) ) { // Thanks for Regex - Onur Yıldırım - https://stackoverflow.com/questions/3026957/how-to-validate-a-domain-name-using-regex-php
                                            $resource = 'http://' . $resource;
                                            echo '<li><a target="_blank" href="'. $resource . '">' . $resource . '</a></li>';
                                        } else {
                                            // No link needed - Not a website
                                            echo '<li>' . $resource . '</li>';
                                        }
                                    endif;

                               }
                            else:
                                // Resources do not exist
                                echo '<li>No resources recorded.</li>';
                            endif;
                            ?>
                        </ul>
                    </div>
                    <div class="entry">
                        <h3>Tags:</h3>
                        <ul class="tags">
                            <?php
                            $tags = $sqlCom->getTagsForEntry($entryID);
                            if ( count($tags) >= 1 && $tags[0] != FALSE):
                                // Tags exist so loop through and display
                                foreach ( $tags as $tag) {
                                    echo '<li><a class="button" href="./tagEntries.php?tagID='. $tag['id'] . '">' . $tag['name'] . '</a></li>';
                                }
                            else:
                                // Tags do not exist
                                echo '<li>No tags recorded.</li>';
                            endif;
                            ?>
                        </ul>
                    </div>
                </article>
            </div>
        </div>
        <div class="edit">
            <p><a class="button" href="./detail.php?entryID=<?php
                echo $entryID;
                if ( $tagID ):
                    echo "&tagID=$tagID";
                endif;
                ?>&nextEntry=true">Next Entry</a></p>
            <p><a class="button" href="./detail.php?entryID=<?php
                echo $entryID;
                if ( $tagID ):
                    echo "&tagID=$tagID";
                endif;
                ?>&prevEntry=true">Previous Entry</a></p>
            <p><a class="button" href="./edit.php?entryID=<?php echo $entryID ?>">Edit Entry</a></p>
            <p><a class="button red" href="./detail.php?delete=yes&entryID=<?php echo $entryID ?>"
                  onclick="return confirm('Are you sure you want to delete this entry?')">
                Delete Entry
                </a></p>
        </div>
    </section>
<?php
include('footer.php');
?>