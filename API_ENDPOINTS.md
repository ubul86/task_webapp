# API Documentation

## Table of Contents
1. [Base URL](#base-url)
2. [Retrieve All Tasks](#1-retrieve-all-tasks)
3. [Retrieve a Specific Task](#2-retrieve-a-specific-task)
4. [Create a Task](#3-create-a-task)
5. [Update a Task](#4-update-a-task)
6. [Delete a Task](#5-delete-a-task)
7. [Bulk Delete Tasks](#6-bulk-delete-tasks)
8. [Toggle Task Completion](#7-toggle-task-completion)
9. [Bulk Complete Tasks](#8-bulk-complete-tasks)
10. [Add Used Time to a Task](#9-add-used-time-to-a-task)

## 1. Retrieve All Tasks

**Endpoint:**  
`GET /api/tasks`

Retrieves all tasks from the database.

#### Response Example
```json
{
    "success": true,
    "message": "Tasks retrieved successfully",
    "data": [
        {
            "id": 1,
            "description": "Fix bugs",
            "estimated_time": 120,
            "used_time": 60,
            "user_id": 2
        }
    ]
}

```

---

## 2. Retrieve a Specific Task

**Endpoint:**  
`GET /api/tasks/{id}`

Retrieves a specific task by its ID.

### URL Parameters

| **Name** | **Required** | **Type**  | **Description**                      |
|----------|--------------|-----------|--------------------------------------|
| `id`     | Yes          | Integer   | The ID of the task to retrieve.      |


### Response Example

```json
{
    "success": true,
    "message": "Task retrieved successfully",
    "data": {
        "id": 1,
        "description": "Fix bugs",
        "estimated_time": 120,
        "used_time": 60,
        "user_id": 2
    }
}
```

or an error message:

```json
{
    "success": false,
    "message": "Task not found",
    "errors": null
}
```

---

## 3. Create a Task

**Endpoint:**  
`POST /api/tasks`

Creates a new task.

### Request Body

| **Field**          | **Required** | **Type**  | **Description**                          |
|--------------------|--------------|-----------|------------------------------------------|
| `user_id`          | No           | Integer   | The ID of the user assigned to the task. |
| `description`      | No           | String    | The description of the task.             |
| `estimated_time`   | Yes          | Integer   | Estimated time in seconds.               |
| `used_time`        | No           | Integer   | Used time in seconds.                    |


#### Response Example

```json
{
    "success": true,
    "message": "Task created successfully",
    "data": {
        "id": 3,
        "description": "Fix bugs",
        "estimated_time": 120,
        "user_id": 2
    }
}
```

---

## 4. Update a Task

**Endpoint:**  
`PUT /api/tasks/{id}`

Updates an existing task by its ID.

### URL Parameters

| **Name** | **Required** | **Type**  | **Description**                     |
|----------|--------------|-----------|-------------------------------------|
| `id`     | Yes          | Integer   | The ID of the task to update.       |

### Request Body

| **Field**          | **Required** | **Type**  | **Description**              |
|--------------------|--------------|-----------|------------------------------|
| `description`      | No           | String    | The description of the task. |
| `estimated_time`   | Yes          | Integer   | Estimated time in seconds.   |
| `used_time`        | No           | Integer   | Used time in seconds.        |


#### Response Example

```json
{
    "success": true,
    "message": "Task updated successfully",
    "data": {
        "id": 1,
        "description": "Updated task description",
        "estimated_time": 90
    }
}
```

---

## 5. Delete a Task

**Endpoint:**  
`DELETE /api/tasks/{id}`

Deletes an existing task by its ID.

#### Response Example

```json
{
    "success": true,
    "message": "Task deleted successfully",
    "data": null
}
```

---

## 6. Bulk Delete Tasks

**Endpoint:**  
`DELETE /api/tasks/bulk-destroy`

Deletes multiple tasks at once by specifying their IDs.

### Request Body

| **Field** | **Required** | **Type**  | **Description**                                  |
|-----------|--------------|-----------|--------------------------------------------------|
| `ids`     | Yes          | String    | Comma-separated list of task IDs to be deleted.  |


#### Response Example

```json
{
    "success": true,
    "message": "Tasks marked as completed",
    "data": null
}
```

---

## 7. Toggle Task Completion

**Endpoint:**  
`PUT /api/tasks/toggle-completed/{id}`

Toggles the completion status of a task.

#### Response Example

```json
{
    "success": true,
    "message": "Task completion toggled",
    "data": {
        "id": 1,
        "completed_at": "2024-11-29T12:00:00Z"
    }
}
```

---

## 8. Bulk Complete Tasks

**Endpoint:**  
`PUT /api/tasks/set-bulk-completed`

Marks multiple tasks as completed.

### Request Body

| **Field** | **Required** | **Type**  | **Description**                                   |
|-----------|--------------|-----------|---------------------------------------------------|
| `ids`     | Yes          | String    | Comma-separated list of task IDs to be completed. |


#### Response Example

```json
{
    "success": true,
    "message": "Tasks deleted successfully",
    "data": null
}
```

---

## 9. Add Used Time to a Task

**Endpoint:**  
`PUT /api/tasks/add-used-time-to-task/{id}`

Adds used time to a task.

### Request Body

| **Field**      | **Required** | **Type**  | **Description**                  |
|----------------|--------------|-----------|----------------------------------|
| `used_time`    | No           | Integer   | The amount of time used in minutes. |


#### Response Example

```json
{
    "success": true,
    "message": "Used time added to task",
    "data": {
        "id": 1,
        "used_time": 90
    }
}
```
