<?php

define("TASKS_FILE", "tasks.json");


function loadTasks()
{
    if (!file_exists(TASKS_FILE)) {
        file_put_contents(TASKS_FILE, json_encode([]));
    }

    $tasks = json_decode(file_get_contents(TASKS_FILE), true);
    return is_array ($tasks) ? $tasks : [];
}

function addTask($description)
{
    $tasks = loadTasks();

}


//Parse command Line arguments

if ($argc < 2) {
    echo "Usage: php tasker.php <command> [options]\n";
}

$command = $argv[1];
switch ($command) {
    case 'add':
        if(isset($argv[2])) {
            addTask($argv[2]);
        } else {
            echo "Usage: php tasker.php add <description>\n>";
        }
        break;
}
