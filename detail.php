<?php
include('header.php');


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
                            $resources = preg_split('/\r\n|[\r\n]/', $entryData['resources']); // Thanks Long Ears - https://stackoverflow.com/questions/7058168/explode-textarea-php-at-new-lines
                            if ( count($resources) >= 1 && $resources[0] != FALSE):
                                // Resources exist so loop through and display
                                foreach ( $resources as $resource) {
                                    // No http
                                    if( strpos($resource, 'http') === FALSE ) :
                                        // Has valid web extension
                                        if( preg_match('/^(?!\-)(?:(?:[a-zA-Z\d][a-zA-Z\d\-]{0,61})?[a-zA-Z\d]\.){1,126}(?!\d+)[a-zA-Z\d]{1,63}$/',$resource, $matches ) ) { // Thanks for Regex - Onur Yıldırım - https://stackoverflow.com/questions/3026957/how-to-validate-a-domain-name-using-regex-php
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
                </article>
            </div>
        </div>
        <div class="edit">
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