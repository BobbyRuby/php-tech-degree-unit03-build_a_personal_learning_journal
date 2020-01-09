<?php
    include('header.php');
$pdoSObj = $sqlCom->prepareAndExecuteStatement('SELECT * FROM entries ORDER BY date DESC', []);
$rows = $pdoSObj[1]->fetchAll(PDO::FETCH_ASSOC);
if( is_array($rows) && count($rows) ):
    foreach( $rows as $row ){ ?>
        <article>
            <h2><a href="detail.php?entryID=<?php echo $row['id'] ?>"><?php echo $row['title'] ?></a></h2>
            <time datetime="<?php echo $row['date'] ?>"><?php echo $row['date'] ?></time>
        </article>
<?php
    }
else: ?>
    <article>
        <h2>No Journal Entries Yet!</h2>
    </article>
<?php endif; ?>
            </div>
        </div>
    </section>
<?php
    include('footer.php');
?>