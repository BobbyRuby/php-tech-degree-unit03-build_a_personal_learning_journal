<?php
    include('header.php');
$pdoSObj = $sqlCom->prepareAndExecuteStatement('SELECT * FROM entries', []);
foreach( $pdoSObj[1]->fetchAll(PDO::FETCH_ASSOC) as $row ){ ?>
    <article>
        <h2><a href="detail.php?entryID=<?php echo $row['id'] ?>"><?php echo $row['title'] ?></h2>
        <time datetime="<?php echo $row['date'] ?>"><?php echo $row['date'] ?></time>
    </article>
<?php  }
// loop through entries and display
?>
            </div>
        </div>
    </section>
<?php
    include('footer.php');
?>