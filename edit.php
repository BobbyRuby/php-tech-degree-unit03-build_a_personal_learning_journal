<?php
include('header.php');
if( ! empty($_POST) ):
    // loop through post data and set up sql to insert the new entry
    foreach ( $_POST as $key => $item ){
        $item = filter_input(INPUT_POST, $key, FILTER_SANITIZE_STRING);
        $updateData[] = $item;
    }
    $sqlCom->updateEntry($entryData['id'], $updateData);
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
                    <label for="resources-to-remember">Resources to Remember</label>
                    <textarea id="resources-to-remember" rows="5" name="ResourcesToRemember"><?php echo $entryData['resources'] ?></textarea>
                    <input type="submit" value="Process Edits for Entry" class="button"/><br/><br/>
                    <a href="./detail.php?entryID=<?php echo $entryID ?>" class="button button-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </section>
<?php
include('footer.php');
?>