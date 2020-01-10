<?php
include('header.php');
// Get entryID from GET
$entryID = filter_input(INPUT_GET, 'entryID', FILTER_VALIDATE_INT);

if( ! empty($_GET['delete']) && $_GET['delete'] === 'yes' ):
    $sqlCom->deleteRow($entryID, 'entries');
elseif ( ! empty($_GET['nextEntry']) &&  $_GET['nextEntry'] === 'true' ) :
    // Check to see if the next entry exists
    if ( $row = $sqlCom->getAssocRowById($entryID+1, 'entries') ){
        $entryID++;
        // If it does then display reload page with proper entryID
        header("Location: ./detail.php?entryID=$entryID");
    }
    else {
        // There is not an immediate entry 1 id from here, find the next ROW

        // Return a data set of all entries in DESC order
        $pdoSObj = $sqlCom->prepareAndExecuteStatement('
            SELECT * 
            FROM ( SELECT *
                    FROM entries 
                    WHERE id <= ? 
                    ORDER BY id DESC 
                    LIMIT 1
                )
            UNION
            SELECT * 
            FROM ( SELECT *
                    FROM entries 
                    WHERE id >= ? 
                    ORDER BY id ASC 
                    LIMIT 1
                )',
            [$entryID, $entryID]);
        $rows = $pdoSObj[1]->fetchAll(PDO::FETCH_ASSOC);


    }
elseif ( ! empty($_GET['prevEntry']) &&  $_GET['prevEntry'] === 'true'  ) :
    // Check to see if the next entry exists
    if ( $row = $sqlCom->getAssocRowById($entryID-1, 'entries') ){
        $entryID--;
        // If it does then display reload page with proper entryID
        header("Location: ./detail.php?entryID=$entryID");
    }
    else {

    }

endif;
$entryData = $sqlCom->getAssocRowById($entryID, 'entries');
?>

                    <h1><?php echo $entryData['title'] ?></h1>
                    <time datetime="<?php echo $entryData['date'] ?>"><?php echo convertDateTime($entryData['date']) ?></time>
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
            <p><a class="button" href="./detail.php?entryID=<?php echo $entryID ?>&nextEntry=true">Next Entry</a></p>
            <p><a class="button" href="./detail.php?entryID=<?php echo $entryID ?>&prevEntry=true">Previous Entry</a></p>
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