<?php
    include('header.php');
$pdoSObj = $sqlCom->prepareAndExecuteStatement('SELECT * FROM entries ORDER BY date DESC', []);
$rows = $pdoSObj[1]->fetchAll(PDO::FETCH_ASSOC);
if( is_array($rows) && count($rows) ):
    foreach( $rows as $row ){ ?>
        <article>
            <h2><a href="detail.php?entryID=<?php echo $row['id'] ?>"><?php echo $row['title'] ?></a></h2>
            <time datetime="<?php echo $row['date'] ?>"><?php if ( ! empty ($row['date']) ) : echo convertDateTime($row['date']); else: echo 'No date recored'; endif; ?></time>
            <ul class="tags">
            <?php
            $tags = $sqlCom->getTagsForEntry($row['id']);
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