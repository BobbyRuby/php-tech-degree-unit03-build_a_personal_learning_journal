<?php
include('header.php');
// Explode the entries index by using the comma's
$entryIDs = explode(',', $tag['entries']);

// Loop through entries and display
foreach ($entryIDs as $entryID ) {
    $row = $sqlCom->getAssocRowById( $entryID, 'entries' );
    ?>
    <article>
        <h2><a href="detail.php?entryID=<?php echo $row['id'] ?>"><?php echo $row['title'] ?></a></h2>
        <time datetime="<?php echo $row['date'] ?>"><?php echo $row['date'] ?></time>
        <ul class="tags">
            <?php
            $tags = $sqlCom->getTagsForEntry($row['id']);
            if (count($tags) >= 1 && $tags[0] != FALSE):
                // Tags exist so loop through and display
                foreach ($tags as $tag) {
                    echo '<li><a class="button" href="./tagEntries.php?tagID=' . $tag['id'] . '">' . $tag['name'] . '</a></li>';
                }
            else:
                // Tags do not exist
                echo '<li>No tags recorded.</li>';
            endif;
            ?>
        </ul>
    </article>
<?php
} ?>
</div>
        </div>
    </section>
<?php
include('footer.php');