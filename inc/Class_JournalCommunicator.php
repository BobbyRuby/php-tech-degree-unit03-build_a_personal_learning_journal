<?php
/**
 * Class Class_JournalCommunicator
 * Handles interactions between entries table and tags tables for PHP techdegree unit 03 build a personal learning journal
 */
class Class_JournalCommunicator extends Class_SqliteCommunicator
{
    /**
     * Make update Statement and redirect
     * @param $entryID
     * @param $updateData
     * @return boolean
     */
    public function updateEntry($entryID, $updateData){
        // Push entryID onto updateData array for WHERE ?
        $updateData [] = $entryID;
        $pdoSObj = $this->prepareAndExecuteStatement(
            'UPDATE entries SET title = ?, date = ?, time_spent = ?, learned = ?, resources = ?
            WHERE id = ?',
            $updateData
        );
        return $pdoSObj[0];
    }

    /**
     * Make update Statement and redirect
     * @param $tagID
     * @param array $updateData - index 0 containing a set of comma separated entryIDs as a string to add to the tags table or a single id
     */
    public function updateTag( $tagID, $updateData ){
        // Push tagID onto updateData array for WHERE ?
        $updateData [] = $tagID;
        $this->prepareAndExecuteStatement(
            'UPDATE tags SET entries = ?
            WHERE id = ?',
            $updateData
        );
    }

    /**
     * @param $insertData
     * @return string
     */
    public function insertEntry($insertData)
    {
        $this->prepareAndExecuteStatement(
            'INSERT INTO entries 
    (title, date, time_spent, learned, resources) 
    VALUES (?,?,?,?,?)',
            $insertData
        );
        $insertId = $this->getPdo()->lastInsertId();
        return $insertId;
    }

    /**
     * @param $tagNames
     * @return array
     */
    protected function getTagsLikeNames( $tagNames )
    {
        $dbTags = [];
        if ( ! empty($tagNames) ) :
            // Begin to build sql
            $sql = 'SELECT * 
            FROM tags 
            WHERE';
            // Get a count of the tagNames to identify last iteration
            $count = count($tagNames);
            // Is there more than only 1?
            if ( $count !== 1 ):
                // Append col and value we want to search for
                for ($i = 0; $i < $count; $i++){
                    // Ensure no specials characters or whitespace on the sides of the tag
                    $tagNames[$i] = strip_tags(trim(strtolower($tagNames[$i])));
                    // Adjust count for index
                    if ( $i !== $count - 1 ):
                        $sql .= " name = ? OR";
                    else:
                        $sql .= " name = ?";
                    endif;
                }
            else:
                // Only 1
                $sql .= " name = ?";
            endif;
            $pdoSObj = $this->prepareAndExecuteStatement($sql, array_values($tagNames));
            $dbTags = $pdoSObj[1]->fetchAll(PDO::FETCH_ASSOC);
        endif;
        return $dbTags;
    }

    /**
     * @param array $dbTagsLikeNames
     * @return mixed
     */
    public function getTagsNOTLikeIDs(array $dbTagsLikeNames)
    {
        $dbTagsNOTLikeNames = [];
        // Get all tags not equal to above
        $nextSql = 'SELECT * 
        FROM tags 
        WHERE';
        // Get a count of the tagNames to identify last iteration
        $nextCount = count($dbTagsLikeNames);
        // Append col and value we want to search for
        foreach ($dbTagsLikeNames as $index => $dbTag) {
            if ($index !== $nextCount - 1):
                $nextSql .= " id != ? AND";
                $tagIDs[] = $dbTag['id'];
            else:
                $nextSql .= " id != ?";
                $tagIDs[] = $dbTag['id'];
            endif;
        }
        if ( ! empty($tagIDs) ):
            $pdoSObj = $this->prepareAndExecuteStatement($nextSql, array_values($tagIDs));
            $dbTagsNOTLikeNames = $pdoSObj[1]->fetchAll(PDO::FETCH_ASSOC);
        else:
            $pdoSObj = $this->prepareAndExecuteStatement("SELECT * FROM tags", []);
            $dbTagsNOTLikeNames = $pdoSObj[1]->fetchAll(PDO::FETCH_ASSOC);
        endif;
        return $dbTagsNOTLikeNames;
    }

    /**
     * @param $insertData
     * @return string
     */
    private function insertTag( $insertData )
    {
        $this->prepareAndExecuteStatement(
            'INSERT INTO tags (name, entries) VALUES (?, ?)',
            $insertData
        );
        $insertId = $this->getPdo()->lastInsertId();
        return $insertId;
    }

    /**
     * @param string $tagName - Name of the tag
     * @return int - $tagID - if exists or 0 if does not
     */
    private function tagNameExists( $tagName )
    {
        $pdoSObj = $this->prepareAndExecuteStatement("SELECT id FROM tags WHERE name = ?", [$tagName]);
        $data = $pdoSObj[1]->fetch(PDO::FETCH_ASSOC);
        if ( ! empty($data['id']) ):
            return $data['id'];
        else:
            return 0;
        endif;
    }

    /**
     * Query the db and return all tags for the entryID passed
     * @param $entryID
     * @return mixed
     */
    public function getTagsForEntry( $entryID )
    {
        $pdoSObj = $this->prepareAndExecuteStatement("SELECT * FROM tags WHERE entries LIKE ?", ['%'.$entryID.'%']);
        return $pdoSObj[1]->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Decides if tag should be inserted or updated and then calls function accordingly
     * @param $entryID
     * @param $tagNames - The names of the tags for this entry on edit ( Any names in the database before the update that are not present here are removed from the entry )
     */
    public function insertOrUpdateTag($entryID, $tagNames)
    {
        // Filter out blanks
        $tagNames = array_values(array_filter($tagNames));
        // Get rows for tags submitted from the db
        $dbTagsLikeNames = $this->getTagsLikeNames($tagNames);
        // Get rows for tags not submitted from the db
        $dbTagsNOTLikeNames = $this->getTagsNOTLikeIDs($dbTagsLikeNames);

        // loop through and remove the entryID from all of the returned tags as they have been removed from the entry
        foreach ($dbTagsNOTLikeNames as $i => $dbTagsNOTLikeName) {
            $entryIDsNOTLikeName = explode(",", $dbTagsNOTLikeName['entries']);
            foreach ($entryIDsNOTLikeName as $entryIDNOTLikeName) {
                if ( $entryID == $entryIDNOTLikeName ):
                    // Remove this entryID from the list of entryIDs
                    // If found with comma
                    if ( strpos($dbTagsNOTLikeNames[$i]['entries'], $entryID.',') !== FALSE ) :
                        $dbTagsNOTLikeNames[$i]['entries'] = str_replace($entryID.',', '', $dbTagsNOTLikeNames[$i]['entries']);
                    else:
                        $dbTagsNOTLikeNames[$i]['entries'] = str_replace($entryID, '', $dbTagsNOTLikeNames[$i]['entries']);
                    endif;
                    // Remove comma from end if it exists
                    $dbTagsNOTLikeNames[$i]['entries'] = preg_replace('/[,]$/', '', $dbTagsNOTLikeNames[$i]['entries']);
                    // Update to remove the old entries from this tag
                    $this->updateTag($dbTagsNOTLikeNames[$i]['id'], [$dbTagsNOTLikeNames[$i]['entries']]);
                endif;
            }
        }

        // Loop through $tagNames to insert new ones and update existing ones
        foreach ($tagNames as $index => $tagName) {
            // Ensure no specials characters or whitespace on the sides of the tag
            $tagName = strip_tags( trim( strtolower( $tagName)));
            // If tag exists, update tag with new entryID
            if( ! $tagID = $this->tagNameExists($tagName) ) :
                if ( ! empty( $tagName ) ) :
                    // Insert a new tag into the tags table
                    $this->insertTag([$tagName, $entryID]);
                endif;
            else:
                // Else Update Tag
                $tagData = $this->getAssocRowById($tagID, 'tags');
                $entryIDs = array_filter(explode(",", $tagData['entries']));
                $entryIDs[] = $entryID;
                if ( count($entryIDs) <= 1):
                    $this->updateTag($tagID, [$entryID]);
                else:
                    $this->updateTag($tagID, [implode(",", array_unique($entryIDs) )]);
                endif;
            endif;
        }
    }

    public function removeEntryFromAllTags( $entryID )
    {
        // Get all tags
        $tags = $this->getTagsForEntry( $entryID );
        foreach ($tags as $tag) {
            $tagEntryIDs = $tag['entries'];
            // If found with comma
            if ( strpos($tagEntryIDs, $entryID.',') !== FALSE ) :
                $tagEntryIDs = str_replace($entryID.',', '', $tagEntryIDs);
            else:
                $tagEntryIDs = str_replace($entryID, '', $tagEntryIDs);
            endif;
            // Remove comma from end if it exists
            $tagEntryIDs = preg_replace('/[,]$/', '', $tagEntryIDs);
            // Update to remove the old entries from this tag
            $this->updateTag($tag['id'], [$tagEntryIDs]);
        }
    }
}