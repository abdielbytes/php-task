<?php
// File path for tasks storage
define("TASKS_FILE", "tasks.json");

// Load tasks from the JSON file
function loadTasks() {
    if (!file_exists(TASKS_FILE)) {
        file_put_contents(TASKS_FILE, json_encode([]));
    }
    $tasks = json_decode(file_get_contents(TASKS_FILE), true);
    return is_array($tasks) ? $tasks : [];
}

// Save tasks to the JSON file
function saveTasks($tasks) {
    file_put_contents(TASKS_FILE, json_encode($tasks, JSON_PRETTY_PRINT));
}

// Add a new task
function addTask($description) {
    $tasks = loadTasks();
    $taskId = count($tasks) + 1;
    $task = [
        "id" => $taskId,
        "description" => $description,
        "status" => "todo",
        "createdAt" => date("Y-m-d H:i:s"),
        "updatedAt" => date("Y-m-d H:i:s")
    ];
    $tasks[] = $task;
    saveTasks($tasks);
    echo "Task added successfully (ID: $taskId)\n";
}

// Update an existing task
function updateTask($taskId, $newDescription) {
    $tasks = loadTasks();
    foreach ($tasks as &$task) {
        if ($task["id"] == $taskId) {
            $task["description"] = $newDescription;
            $task["updatedAt"] = date("Y-m-d H:i:s");
            saveTasks($tasks);
            echo "Task $taskId updated successfully\n";
            return;
        }
    }
    echo "Task with ID $taskId not found\n";
}

// Delete a task
function deleteTask($taskId) {
    $tasks = loadTasks();
    $tasks = array_filter($tasks, fn($task) => $task["id"] != $taskId);
    saveTasks(array_values($tasks));
    echo "Task $taskId deleted successfully\n";
}

// Mark a task as in-progress or done
function markTask($taskId, $status) {
    $tasks = loadTasks();
    foreach ($tasks as &$task) {
        if ($task["id"] == $taskId) {
            $task["status"] = $status;
            $task["updatedAt"] = date("Y-m-d H:i:s");
            saveTasks($tasks);
            echo "Task $taskId marked as $status\n";
            return;
        }
    }
    echo "Task with ID $taskId not found\n";
}

// List tasks based on their status
function listTasks($status = null) {
    $tasks = loadTasks();
    foreach ($tasks as $task) {
        if (is_null($status) || $task["status"] === $status) {
            echo "ID: {$task['id']}, Description: {$task['description']}, Status: {$task['status']}, Created At: {$task['createdAt']}\n";
        }
    }
}

// Parse command-line arguments
if ($argc < 2) {
    echo "Usage: php task_tracker.php <command> [options]\n";
    exit(1);
}

$command = $argv[1];
switch ($command) {
    case "add":
        if (isset($argv[2])) {
            addTask($argv[2]);
        } else {
            echo "Usage: php task_tracker.php add <description>\n";
        }
        break;

    case "update":
        if (isset($argv[2]) && isset($argv[3])) {
            updateTask($argv[2], $argv[3]);
        } else {
            echo "Usage: php task_tracker.php update <id> <new_description>\n";
        }
        break;

    case "delete":
        if (isset($argv[2])) {
            deleteTask($argv[2]);
        } else {
            echo "Usage: php task_tracker.php delete <id>\n";
        }
        break;

    case "mark":
        if (isset($argv[2]) && isset($argv[3]) && in_array($argv[3], ["in-progress", "done"])) {
            markTask($argv[2], $argv[3]);
        } else {
            echo "Usage: php task_tracker.php mark <id> <status> (status: in-progress, done)\n";
        }
        break;

    case "list":
        $status = $argv[2] ?? null;
        if ($status && !in_array($status, ["todo", "in-progress", "done"])) {
            echo "Usage: php task_tracker.php list [status] (status: todo, in-progress, done)\n";
        } else {
            listTasks($status);
        }
        break;

    default:
        echo "Unknown command: $command\n";
        echo "Available commands: add, update, delete, mark, list\n";
        break;
}
