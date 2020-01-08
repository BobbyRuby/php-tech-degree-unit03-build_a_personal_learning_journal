<?php

/**
 * Function for debugging
 * @param $debugItem
 * @param int $die
 */
function rfd_debugger($debugItem, $die = 0)
{
    echo '<pre>';
    print_r($debugItem);
    echo '</pre>';
    if ($die == 1) {
        die();
    }
}
