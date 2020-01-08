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