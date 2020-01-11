<?php
/**
 * New Entry
 * @return bool
 */
function isNewEntry()
{
    // are we in admin and viewing an new post?
    if (stripos($_SERVER['SCRIPT_NAME'], 'new.php')) {
        return TRUE;
    }
    return FALSE;
}

/**
 * Editing Entry
 * @return bool
 */
function isEditEntry()
{
    // are we in admin and viewing an new post?
    if (stripos($_SERVER['SCRIPT_NAME'], 'edit.php')) {
        return TRUE;
    }
    return FALSE;
}

/**
 * Detail Entry
 * @return bool
 */
function isEntryDetail()
{
    // are we in admin and viewing an new post?
    if (stripos($_SERVER['SCRIPT_NAME'], 'detail.php')) {
        return TRUE;
    }
    return FALSE;
}

/**
 * Tag entries page
 * @return bool
 */
function isTagEntries()
{
    // are we in admin and viewing an new post?
    if (stripos($_SERVER['SCRIPT_NAME'], 'tagEntries.php')) {
        return TRUE;
    }
    return FALSE;
}

/**
 * Convert to readable form per requirement of this project
 * @param $dateTime
 */
function convertDateTime($dateTime){
    return date_format(date_create($dateTime), 'F d Y');
}

/**
 * Function to filter $_POST array for entry insertion or update
 * @param array $_post - the $_POST ( doesn't need to be passed, but I think it is more clear )
 * @return array
 */
function prepareDataForDbUpdate(array $_post)
{
    // loop through post data and set up sql to insert the new entry
    foreach ($_post as $key => $item) {
        $item = filter_input(INPUT_POST, $key, FILTER_SANITIZE_STRING);
        // Tags textarea?
        if ($key === 'tags'):
            // Get reference to tagNames
            $tagNames = preg_split('/\r\n|[\r\n]/', $item);
        else:
            // No - Get entry data item
            $entryTableData[] = $item;
        endif;
    }
    return array($tagNames, $entryTableData);
}