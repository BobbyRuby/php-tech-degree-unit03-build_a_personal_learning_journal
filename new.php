<?php
    include('header.php');
if( ! empty($_POST) ){
    // Obtain filtered references to $tags table data and entry table data
    list($tags, $entryTableData) = prepareDataForDbUpdate($_POST);
    // If entry inserted successfully
    if( $insertId = $sqlCom->insertEntry($entryTableData) && ! empty($tags) ) :
        $sqlCom->insertOrUpdateTags( $entryID, $tags );
    endif;
    // Redirect to detail page using newly inserted entry
    header("Location: ./detail.php?entryID=$insertId");
}
?>
                <label for="title"> Title</label>
                <input id="title" type="text" name="title"/><br/>
                <label for="date">Date</label>
                <input id="date" type="date" name="date"/><br/>
                <label for="time-spent"> Time Spent</label>
                <input id="time-spent" type="text" name="timeSpent"/><br/>
                <label for="what-i-learned">What I Learned</label>
                <textarea id="what-i-learned" rows="5" name="whatILearned"></textarea>
                <label for="resources-to-remember">Resources to Remember ( 1 per line )</label>
                <textarea id="resources-to-remember" rows="5" name="resourcesToRemember"></textarea>
                <label for="tags">Tags( 1 per line )</label>
                <textarea id="tags" rows="5" name="tags"></textarea>
                <div class="button-container">
                    <input type="submit" value="Publish Entry" class="button save"/>
                    <br/><br/>
                    <input type="submit" value="Cancel" class="button red cancel"/>
                </div>
            </form>
        </div>
    </div>
</section>
<?php
    include('footer.php');
?>
