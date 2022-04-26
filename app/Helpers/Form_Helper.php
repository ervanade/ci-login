<?php
function display_error($validation, $fields)
{
    if ($validation->hasError($fields)) {
        return $validation->getError($fields);
    } else {
        return false;
    }
}
