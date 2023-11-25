# ScheDU PHP Library Documentation
## Introduction
The ScheDU class is a PHP library designed to interact with the schedule data of Sumy State University 🏫 (Сумський державний університет). This library allows you to retrieve information about groups, teachers, auditoriums, and schedules from the Sumy State University server. It also provides functionality to fetch and format this data for your convenience.

## Author
Copyright © 2023 Yehor Oblyvantsov 📅


## Installation 
To use the ScheDU library, you need to include it in your PHP project. You can do this by downloading the ScheDU.php file and requiring it in your PHP script:

```php
require_once 'schedu.php';
```
## Initialization
Before you can use the library, you need to create an instance of the ScheDU class:

```php
$scheDU = new ScheDU();
```
This will automatically fetch data about groups, teachers, and auditoriums from the Sumy State University server upon initialization.

## Methods
The ScheDU library provides various methods to interact with the schedule data. These methods are divided into three categories:

### Fetching Data
These methods are responsible for fetching data from the Sumy State University server:
- `getAllGroups()`: Retrieves a array of all groups.
- `getAllTeachers()`: Retrieves a array of all teachers.
- `getAllAuditoriums()`: Retrieves a array of all auditoriums.

### Getting Data
These methods allow you to retrieve data by name or ID:

- `getGroupIdByName($groupName)`: Gets the ID of a group by its name.
- `getGroupNameById($groupId)`: Gets the name of a group by its ID.
- `getTeacherIdByName($teacherName)`: Gets the ID of a teacher by their name.
- `getTeacherNameById($teacherId)`: Gets the name of a teacher by their ID.
- `getAuditoriumIdByName($auditoriumName)`: Gets the ID of an auditorium by its name.
- `getAuditoriumNameById($auditoriumId)`: Gets the name of an auditorium by its ID.

### Retrieving Schedules
These methods allow you to retrieve schedules based on various parameters:

- `getSchedule($type, $name, $dateFrom = null, $dateTo = null)`: Retrieves an array of schedule for a group, teacher, or auditorium. You can specify the `$type` (GROUP, TEACHER, or AUDITORIUM), `$name` (the name of the group, teacher, or auditorium), and optional `$dateFrom` and `$dateTo` parameters to specify the date range.
## Examples
Here are some example usages of the ScheDU library:

```php
// Create an instance of ScheDU
$scheDU = new ScheDU();

// Fetch all groups
$allGroups = $scheDU->getAllGroups();

// Get the ID of a group by its name
$groupId = $scheDU->getGroupIdByName('Group Name');

// Retrieve a schedule for a group for a specific date range
$schedule = $scheDU->getSchedule('GROUP', 'ТК-21', '01.09.2023', '30.09.2023');

// Encode retrieved array into JSON
$json_data = json_encode($schedule);

// Displaying JSON data as a response to a request
echo $json_data;
```
## Tips and Best Practices
- ### Make sure to handle exceptions: 
The library throws exceptions when data retrieval fails. Be sure to catch and handle these exceptions appropriately in your code.
- ### Use appropriate date formats: 
When specifying date ranges, use the format 'DD.MM.YYYY' for consistency.
- ### Check for null values: 
When using methods that return data by name or ID, check for null values to handle cases where the data is not found.
- ### Keep data up to date: 
If the format of the schedule server data changes, it may be necessary to update the library accordingly to obtain accurate results.

Feel free to explore the ScheDU library further and adapt it to your specific needs when working with Sumy State University's schedule data. 📚
