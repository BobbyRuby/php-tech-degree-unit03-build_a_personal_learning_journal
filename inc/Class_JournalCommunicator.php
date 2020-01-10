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
    public function updateTag($tagID, $updateData){

        // Get existing entries for the tagID passed
        $row = $this->getAssocRowById($tagID, 'tags');
        $entries = $row['entries'];
        // If entries exist
        if ( strpos($entries, ',') !== FALSE )
            // Append $entries to the new entryID which is = to index 0 of updateData
            $updateData[0] .= ", $entries";

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
        $pdoSObj = $this->prepareAndExecuteStatement("SELECT id, name FROM tags WHERE entries LIKE ?", [$entryID]);
        return $pdoSObj[1]->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Decides if tag should be inserted or updated and then calls function accordingly
     * @param $entryID
     * @param $tags
     */
    public function insertOrUpdateTags( $entryID, $tags ){
        // Loop through $tags
        foreach ($tags as $index => $tagName) {
            // Ensure no specials characters or whitespace on the sides of the tag
            $tagName = strip_tags( trim( strtolower( $tagName)));
            // If tag exists, update tag with new entryID
            if( $tagID = $this->tagNameExists($tagName) ):
                $this->updateTag($tagID, [$entryID]);
            else:
                // Else insert a new tag into the tags table
                $this->insertTag( [$tagName, $entryID] );
            endif;
        }
    }
}