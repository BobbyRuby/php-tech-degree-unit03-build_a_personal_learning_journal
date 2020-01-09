<?php
    include('header.php');

    if( ! empty($_POST) ){
        // loop through post data and set up sql to insert the new entry
        foreach ( $_POST as $key => $item ){
            $item = filter_input(INPUT_POST, $key, FILTER_SANITIZE_STRING);
            $insertData[] = $item;
        }
        $pdoSObj =
            $sqlCom->prepareAndExecuteStatement(
                'INSERT INTO entries 
        (title, date, time_spent, learned, resources) 
        VALUES (?,?,?,?,?)',
                $insertData
        );
        $insertId = $sqlCom->getPdo()->lastInsertId();
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
                <label for="resources-to-remember">Resources to Remember</label>
                <textarea id="resources-to-remember" rows="5" name="ResourcesToRemember"></textarea>
                <input type="submit" value="Publish Entry" class="button"/>
                <a href="./index.php" class="button button-secondary">Cancel</a>
            </form>
        </div>
    </div>
</section>
<?php
    include('footer.php');
?>
