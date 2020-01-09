<?php
include('header.php');

// Get entryID from GET

$entryID = filter_input(INPUT_GET, 'entryID', FILTER_VALIDATE_INT);

$pdoSObj = $sqlCom->prepareAndExecuteStatement('SELECT * FROM entries WHERE id = ?', [$entryID]);
$entryData = $pdoSObj[1]->fetch(PDO::FETCH_ASSOC); ?>

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
                            $resources = preg_split('/\r\n|[\r\n]/', $entryData['resources']); // Thanks Long Ears - https://stackoverflow.com/questions/7058168/explode-textarea-php-at-new-lines
                            if ( count($resources) >= 1 && $resources[0] != FALSE):
                                // Resources exist so loop through and display
                                foreach ( $resources as $resource) {
                                    echo '<li><a target="_blank" href="'. $resource . '">' . $resource . '</a></li>';
                               }
                            else:
                                // Resources do not exist
                                echo '<li>No resources recorded.</li>';
                            endif;
                            ?>
                        </ul>
                    </div>
                </article>
            </div>
        </div>
        <div class="edit">
            <p><a href="./edit.php?entryID=1">Edit Entry</a></p>
        </div>
    </section>
<?php
include('footer.php');
?>