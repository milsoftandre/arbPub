<?php

namespace App\Events;

class ActiveEvent
{
    protected static $events = [];

    public static function addEvent($event)
    {
        self::$events[] = $event;
    }

    public static function getEvents()
    {
        return self::$events;
    }

    public static function clearEvents()
    {
        self::$events = [];
    }
}