<?php

/**
 * Клас ScheDU представляє бібліотеку для роботи з розкладом Сумського державного університету.
 *
 * Ця бібліотека дозволяє отримувати інформацію про групи, викладачів, аудиторії та розклад з серверу СумДУ.
 * Вона надає можливість отримувати інформацію про розклад для груп, викладачів та аудиторій за певний період часу.
 * Також бібліотека дозволяє перетворювати отримані дані у зручний формат.
 * 
 * Copyright © 2023 Обливанцов Єгор
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
        // Отримуємо JSON-дані з серверу для груп
        $jsonData = @file_get_contents($this->urlGroups);

        if ($jsonData === false) {
            throw new Exception('Не вдалося отримати перелік груп');
        }

        // Декодуємо JSON-дані у масив для груп
        $this->groups = json_decode($jsonData, true);

        if ($this->groups === null) {
            throw new Exception('Помилка при декодуванні JSON для груп');
        }

        // Видаляємо пусті елементи з масиву для груп
        $this->groups = array_filter($this->groups);
    }

    private function fetchTeachers()
    {
        // Отримуємо JSON-дані з серверу для викладачів
        $jsonData = @file_get_contents($this->urlTeachers);

        if ($jsonData === false) {
            throw new Exception('Не вдалося отримати перелік викладачів');
        }

        // Декодуємо JSON-дані у масив для викладачів
        $this->teachers = json_decode($jsonData, true);

        if ($this->teachers === null) {
            throw new Exception('Помилка при декодуванні JSON для викладачів');
        }

        // Видаляємо пусті елементи з масиву для викладачів
        $this->teachers = array_filter($this->teachers);
    }

    private function fetchAuditoriums()
    {
        // Отримуємо JSON-дані з серверу для аудиторій
        $jsonData = @file_get_contents($this->urlAuditoriums);

        if ($jsonData === false) {
            throw new Exception('Не вдалося отримати перелік аудиторій');
        }

        // Декодуємо JSON-дані у масив для аудиторій
        $this->auditoriums = json_decode($jsonData, true);

        if ($this->auditoriums === null) {
            throw new Exception('Помилка при декодуванні JSON для аудиторій');
        }

        // Видаляємо пусті елементи з масиву для аудиторій
        $this->auditoriums = array_filter($this->auditoriums);
    }

    public function getAllGroups()
    {
        // Перевіряємо, чи дані про групи уже завантажені. Якщо ні, то завантажуємо їх.
        if ($this->groups === null) {
            $this->fetchGroups();
        }

        // Повертаємо усі групи у вигляді масиву
        return $this->groups;
    }

    public function getAllTeachers()
    {
        // Перевіряємо, чи дані про викладачів уже завантажені. Якщо ні, то завантажуємо їх.
        if ($this->teachers === null) {
            $this->fetchTeachers();
        }

        // Повертаємо усіх викладачів у вигляді масиву
        return $this->teachers;
    }

    public function getAllAuditoriums()
    {
        // Перевіряємо, чи дані про аудиторії уже завантажені. Якщо ні, то завантажуємо їх.
        if ($this->auditoriums === null) {
            $this->fetchAuditoriums();
        }

        // Повертаємо усі аудиторії у вигляді масиву
        return $this->auditoriums;
    }

    public function getGroupIdByName($groupName)
    {
        foreach ($this->groups as $groupId => $group) {
            if ($group === $groupName) {
                return $groupId;
            }
        }
        return null; // Якщо групу не знайдено
    }

    public function getGroupNameById($groupId)
    {
        if (isset($this->groups[$groupId])) {
            return $this->groups[$groupId];
        }
        return null; // Якщо групу не знайдено
    }

    public function getTeacherIdByName($teacherName)
    {
        foreach ($this->teachers as $teacherId => $teacher) {
            if ($teacher === $teacherName) {
                return $teacherId;
            }
        }
        return null; // Якщо викладача не знайдено
    }

    public function getTeacherNameById($teacherId)
    {
        if (isset($this->teachers[$teacherId])) {
            return $this->teachers[$teacherId];
        }
        return null; // Якщо викладача не знайдено
    }

    public function getAuditoriumIdByName($auditoriumName)
    {
        foreach ($this->auditoriums as $auditoriumId => $auditorium) {
            if ($auditorium === $auditoriumName) {
                return $auditoriumId;
            }
        }
        return null; // Якщо аудиторію не знайдено
    }

    public function getAuditoriumNameById($auditoriumId)
    {
        if (isset($this->auditoriums[$auditoriumId])) {
            return $this->auditoriums[$auditoriumId];
        }
        return null; // Якщо аудиторію не знайдено
    }

    public function getSchedule($type, $name, $dateFrom = null, $dateTo = null)
    {

        // Вказуємо дати, якщо аргументи не були передані
        if ($dateFrom === null) {
            $dateFrom = date('d.m.Y');
        }
        if ($dateTo === null) {
            $dateTo = date('d.m.Y');
        }

        // Валідація наданого типу
        $type = strtoupper($type);
        $validTypes = ['GROUP', 'TEACHER', 'AUDITORIUM'];

        if (!in_array($type, $validTypes)) {
            throw new InvalidArgumentException('Невірний тип. Використовуйте GROUP, TEACHER або AUDITORIUM.');
        }

        // Побудова URL для запиту та отримання ідентифікаторів
        if ($type === 'GROUP') {
            $groupId = $this->getGroupIdByName($name);
            if ($groupId === null) {
                throw new InvalidArgumentException('Групу не знайдено.');
            }
            $url = "https://schedule.sumdu.edu.ua/index/json?method=getSchedules&id_grp={$groupId}&date_beg={$dateFrom}&date_end={$dateTo}";
        } elseif ($type === 'TEACHER') {
            $teacherId = $this->getTeacherIdByName($name);
            if ($teacherId === null) {
                throw new InvalidArgumentException('Викладача не знайдено.');
            }
            $url = "https://schedule.sumdu.edu.ua/index/json?method=getSchedules&id_fio={$teacherId}&date_beg={$dateFrom}&date_end={$dateTo}";
        } elseif ($type === 'AUDITORIUM') {
            $auditoriumId = $this->getAuditoriumIdByName($name);
            if ($auditoriumId === null) {
                throw new InvalidArgumentException('Аудиторію не знайдено.');
            }
            $url = "https://schedule.sumdu.edu.ua/index/json?method=getSchedules&id_aud={$auditoriumId}&date_beg={$dateFrom}&date_end={$dateTo}";
        }

        // Виконуємо HTTP-запит і отримуємо JSON-відповідь
        $response = file_get_contents($url);

        // Перетворюємо JSON-відповідь в масив
        $data = json_decode($response, true);

        // Тепер перетворюємо отриманий масив в бажаний формат
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

        // Виводимо масив через var_dump
        return $formattedData;
    }
}
