<?php
include('header.php');
$entryData = $sqlCom->getAssocRowById($entryID, 'entries');
if( ! empty($_POST) ):
    // Obtain filtered references to tags table data and entry table data
    list($tags, $entryTableData) = prepareDataForDbUpdate($_POST);
    // Update successful
    if ( $sqlCom->updateEntry($entryData['id'], $entryTableData ) ) :
        // There are tags
        if ( ! empty($tags) ):
          $sqlCom->insertOrUpdateTags( $entryID, $tags );
        endif;
    endif;
    // Redirect to detail page using newly inserted id
    header("Location: ./detail.php?entryID=$entryID");
endif;

?>
                    <label for="title">Title</label>
                    <input id="title" type="text" name="title" value="<?php echo $entryData['title'] ?>"/><br/>
                    <label for="date">Date</label>
                    <input id="date" type="date" name="date" value="<?php echo $entryData['date'] ?>"/><br/>
                    <label for="time-spent">Time Spent</label>
                    <input id="time-spent" type="text" name="timeSpent" value="<?php echo $entryData['time_spent'] ?>"/><br/>
                    <label for="what-i-learned">What I Learned</label>
                    <textarea id="what-i-learned" rows="5" name="whatILearned"><?php echo $entryData['learned'] ?></textarea>
                    <label for="resources-to-remember">Resources to Remember ( 1 per line )</label>
                    <textarea id="resources-to-remember" rows="5" name="resourcesToRemember"><?php echo $entryData['resources'] ?></textarea>
                    <label for="tags">Tags( 1 per line )</label>
                    <textarea id="tags" rows="5" name="tags"><?php
                        $tags = $sqlCom->getTagsForEntry($entryID);
                        if ( count($tags) >= 1 && $tags[0] != FALSE):
                            foreach ( $tags as $tag) {
                                echo "\r\n".$tag['name'];
                            }
                        endif;
                    ?></textarea>
                    <div class="button-container">
                        <input type="submit" value="Process Edits for Entry" class="button save"/>
                        <br/>
                        <br/>
                        <input type="submit" value="Cancel" class="button red cancel"/>
                    </div>
                </form>
            </div>
        </div>
    </section>
<?php
include('footer.php');
?>