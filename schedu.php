<?php

/**
 * The ScheDU class represents a library for working with the schedule of Sumy State University.
 *
 * This library allows retrieving information about groups, teachers, auditoriums, and the schedule from the SumDU server.
 * It provides the ability to obtain information about the schedule for groups, teachers, and auditoriums for a specific period.
 * Additionally, the library allows transforming the retrieved data into a convenient format.
 *
 * Copyright © 2023 Yegor Oblyviancov
 */

class ScheDU
{
    private $urlGroups = 'https://schedule.sumdu.edu.ua/index/json?method=getGroups';
    private $urlTeachers = 'https://schedule.sumdu.edu.ua/index/json?method=getTeachers';
    private $urlAuditoriums = 'https://schedule.sumdu.edu.ua/index/json?method=getAuditoriums';
    private $groups = null;
    private $teachers = null;
    private $auditoriums = null;

    public function __construct()
    {
        $this->fetchGroups();
        $this->fetchTeachers();
        $this->fetchAuditoriums();
    }

    private function fetchGroups()
    {
        // Get JSON data from the server for groups
        $jsonData = @file_get_contents($this->urlGroups);

        if ($jsonData === false) {
            throw new Exception('Failed to retrieve the list of groups');
        }

        // Decode JSON data into an array for groups
        $this->groups = json_decode($jsonData, true);

        if ($this->groups === null) {
            throw new Exception('Error decoding JSON for groups');
        }

        // Remove empty elements from the groups array
        $this->groups = array_filter($this->groups);
    }

    private function fetchTeachers()
    {
        // Get JSON data from the server for teachers
        $jsonData = @file_get_contents($this->urlTeachers);

        if ($jsonData === false) {
            throw new Exception('Failed to retrieve the list of teachers');
        }

        // Decode JSON data into an array for teachers
        $this->teachers = json_decode($jsonData, true);

        if ($this->teachers === null) {
            throw new Exception('Error decoding JSON for teachers');
        }

        // Remove empty elements from the teachers array
        $this->teachers = array_filter($this->teachers);
    }

    private function fetchAuditoriums()
    {
        // Get JSON data from the server for auditoriums
        $jsonData = @file_get_contents($this->urlAuditoriums);

        if ($jsonData === false) {
            throw new Exception('Failed to retrieve the list of auditoriums');
        }

        // Decode JSON data into an array for auditoriums
        $this->auditoriums = json_decode($jsonData, true);

        if ($this->auditoriums === null) {
            throw new Exception('Error decoding JSON for auditoriums');
        }

        // Remove empty elements from the auditoriums array
        $this->auditoriums = array_filter($this->auditoriums);
    }

    public function getAllGroups()
    {
        // Check if data for groups is already loaded. If not, load it.
        if ($this->groups === null) {
            $this->fetchGroups();
        }

        // Return all groups as an array
        return $this->groups;
    }

    public function getAllTeachers()
    {
        // Check if data for teachers is already loaded. If not, load it.
        if ($this->teachers === null) {
            $this->fetchTeachers();
        }

        // Return all teachers as an array
        return $this->teachers;
    }

    public function getAllAuditoriums()
    {
        // Check if data for auditoriums is already loaded. If not, load it.
        if ($this->auditoriums === null) {
            $this->fetchAuditoriums();
        }

        // Return all auditoriums as an array
        return $this->auditoriums;
    }

    public function getGroupIdByName($groupName)
    {
        foreach ($this->groups as $groupId => $group) {
            if ($group === $groupName) {
                return $groupId;
            }
        }
        return null; // If the group is not found
    }

    public function getGroupNameById($groupId)
    {
        if (isset($this->groups[$groupId])) {
            return $this->groups[$groupId];
        }
        return null; // If the group is not found
    }

    public function getTeacherIdByName($teacherName)
    {
        foreach ($this->teachers as $teacherId => $teacher) {
            if ($teacher === $teacherName) {
                return $teacherId;
            }
        }
        return null; // If the teacher is not found
    }

    public function getTeacherNameById($teacherId)
    {
        if (isset($this->teachers[$teacherId])) {
            return $this->teachers[$teacherId];
        }
        return null; // If the teacher is not found
    }

    public function getAuditoriumIdByName($auditoriumName)
    {
        foreach ($this->auditoriums as $auditoriumId => $auditorium) {
            if ($auditorium === $auditoriumName) {
                return $auditoriumId;
            }
        }
        return null; // If the auditorium is not found
    }

    public function getAuditoriumNameById($auditoriumId)
    {
        if (isset($this->auditoriums[$auditoriumId])) {
            return $this->auditoriums[$auditoriumId];
        }
        return null; // If the auditorium is not found
    }

    public function getSchedule($type, $name, $dateFrom = null, $dateTo = null)
    {

        // Specify dates if arguments were not provided
        if ($dateFrom === null) {
            $dateFrom = date('d.m.Y');
        }
        if ($dateTo === null) {
            $dateTo = date('d.m.Y');
        }

        // Validation of the provided type
        $type = strtoupper($type);
        $validTypes = ['GROUP', 'TEACHER', 'AUDITORIUM'];

        if (!in_array($type, $validTypes)) {
            throw new InvalidArgumentException('Invalid type. Use GROUP, TEACHER, or AUDITORIUM.');
        }

        // Build the URL for the request and obtain identifiers
        if ($type === 'GROUP') {
            $groupId = $this->getGroupIdByName($name);
            if ($groupId === null) {
                throw new InvalidArgumentException('Group not found.');
            }
            $url = "https://schedule.sumdu.edu.ua/index/json?method=getSchedules&id_grp={$groupId}&date_beg={$dateFrom}&date_end={$dateTo}";
        } elseif ($type === 'TEACHER') {
            $teacherId = $this->getTeacherIdByName($name);
            if ($teacherId === null) {
                throw new InvalidArgumentException('Teacher not found.');
            }
            $url = "https://schedule.sumdu.edu.ua/index/json?method=getSchedules&id_fio={$teacherId}&date_beg={$dateFrom}&date_end={$dateTo}";
        } elseif ($type === 'AUDITORIUM') {
            $auditoriumId = $this->getAuditoriumIdByName($name);
            if ($auditoriumId === null) {
                throw new InvalidArgumentException('Auditorium not found.');
            }
            $url = "https://schedule.sumdu.edu.ua/index/json?method=getSchedules&id_aud={$auditoriumId}&date_beg={$dateFrom}&date_end={$dateTo}";
        }

        // Execute an HTTP request and get the JSON response
        $response = file_get_contents($url);

        // Decode the JSON response into an array
        $data = json_decode($response, true);

        // Now transform the obtained array into the desired format
        $formattedData = [];
        foreach ($data as $item) {
            $formattedItem = [
                'date' => $item['DATE_REG'],
                'weekday' => $item['NAME_WDAY'],
                'number' => intval($item['NAME_PAIR']),
                'time' => [
                    'start' => substr($item['TIME_PAIR'], 0, 5),
                    'end' => substr($item['TIME_PAIR'], 6)
                ],
                'teacher' => $item['NAME_FIO'],
                'auditorium' => $item['NAME_AUD'],
                'groups' => explode(', ', $item['NAME_GROUP']),
                'shortname' => $item['ABBR_DISC'],
                'discipline' => $item['NAME_DISC'],
                'type' => $item['NAME_STUD'],
                'reason' => $item['REASON'],
                'info' => $item['INFO'],
                'comment' => $item['COMMENT'],
                'approved' => date('d.m.Y', $item['PUB_DATE']),
                'token' => $item['ID_TOKEN']
            ];

            $formattedData[] = $formattedItem;
        }

        // Output the array using var_dump
        return $formattedData;
    }
}
