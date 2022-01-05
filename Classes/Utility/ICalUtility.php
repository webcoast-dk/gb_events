<?php

declare(strict_types=1);

namespace GuteBotschafter\GbEvents\Utility;

use DateInterval;
use Exception;
use GuteBotschafter\GbEvents\Domain\Model\Event;

class ICalUtility
{
    /**
     * Return a suggested filename for sending the iCalendar file to the client
     *
     * @return string $filename;
     */
    public static function iCalendarFilename(Event $event): string
    {
        return sprintf('%s - %s.ics', $event->getTitle(), $event->getEventDate()->format('Y-m-d'));
    }

    /**
     * Return an iCalendar file as string representation suitable for sending to the client
     *
     * @throws Exception
     *
     * @return string $iCalendarData
     */
    public static function iCalendarData(Event $event): string
    {
        $now = new \DateTime('now', new \DateTimeZone('UTC'));
        $startDate = clone($event->getEventDate());
        $startDate->add(static::getEventTimeAsDateInterval($event));
        $startDate->setTimezone(new \DateTimeZone('UTC'));
        $stopDate = clone($event->getEventStopDate());
        $stopDate->add(static::getEventTimeAsDateInterval($event))->add(new DateInterval('PT1H'));
        $stopDate->setTimezone(new \DateTimeZone('UTC'));

        $iCalData = [];

        $iCalData[] = 'BEGIN:VEVENT';
        $iCalData[] = 'UID:' . $event->getUniqueIdentifier();
        $iCalData[] = 'LOCATION:' . self::escapeTextForIcal($event->getLocation());
        $iCalData[] = 'SUMMARY:' . self::escapeTextForIcal($event->getTitle());
        $iCalData[] = 'DESCRIPTION:' . self::escapeTextForIcal($event->getDescription());
        $iCalData[] = 'CLASS:PUBLIC';

        if ($event->getIsOneDayEvent()) {
            $iCalData[] = 'DTSTART;VALUE=DATE:' . $startDate->format('Ymd');
            $iCalData[] = 'DTEND;VALUE=DATE:' . $stopDate->format('Ymd');
        } else {
            $iCalData[] = 'DTSTART:' . $startDate->format('Ymd\THis\Z');
            $iCalData[] = 'DTEND:' . $stopDate->format('Ymd\THis\Z');
        }
        $iCalData[] = 'DTSTAMP:' . $now->format('Ymd\THis\Z');
        if ($event->isRecurringEvent()) {
            $iCalData[] = 'RRULE:' . static::buildRecurrenceRule($event);
        }
        $iCalData[] = 'END:VEVENT';

        return implode("\r\n", $iCalData);
    }

    /**
     * Tries an intelligent guess as to the start time of an event
     *
     * @throws Exception
     *
     * @return DateInterval
     */
    protected static function getEventTimeAsDateInterval(Event $event)
    {
        $hours = $minutes = 0;
        $matches = [];
        if (preg_match('#(\d{1,2}):?(\d{2})#', $event->getEventTime(), $matches)) {
            $hours = $matches[1];
            $minutes = $matches[2];
        }

        return new DateInterval(sprintf('PT%dH%dM0S', $hours, $minutes));
    }

    /**
     * Escapes given text for usage in ical format.
     *
     * @param $textInput
     * @return mixed|string
     *
     * @see http://www.ietf.org/rfc/rfc2445.txt
     */
    protected static function escapeTextForIcal($textInput)
    {
        $text = html_entity_decode(strip_tags($textInput), ENT_COMPAT | ENT_HTML401, 'UTF-8');

        return str_replace(
            ["\"", "\\", ",", ":", ";", "\n"],
            ["DQUOTE", "\\\\", "\,", "\":\"", "\;", "\\n"],
            $text
        );
    }


    /**
     * Builds iCalendar recurrence rule
     *
     * @return string $rRule
     */
    protected static function buildRecurrenceRule(Event $event): string
    {
        $shortDays = [
            'Monday' => 'MO',
            'Tuesday' => 'TU',
            'Wednesday' => 'WE',
            'Thursday' => 'TH',
            'Friday' => 'FR',
            'Saturday' => 'SA',
            'Sunday' => 'SU',
        ];

        $weeks = [];
        if ($event->getRecurringWeeks() & 1) {
            $weeks[] = '1';
        }
        if ($event->getRecurringWeeks() & 2) {
            $weeks[] = '2';
        }
        if ($event->getRecurringWeeks() & 4) {
            $weeks[] = '3';
        }
        if ($event->getRecurringWeeks() & 8) {
            $weeks[] = '4';
        }
        if ($event->getRecurringWeeks() & 16) {
            $weeks[] = '5';
        }
        if ($event->getRecurringWeeks() & 32) {
            $weeks[] = '-1';
        }

        $days = DateUtility::getRecurringDaysAsText($event);
        foreach ($days as $index => $value) {
            $days[$index] = $shortDays[$value];
        }

        if (count($weeks) !== 0) {
            $rRule = 'FREQ=MONTHLY;BYDAY=';
            $byDays = [];
            foreach ($weeks as $week) {
                foreach ($days as $day) {
                    $byDays[] = sprintf('%s%s', $week, $day);
                }
            }
            $rRule .= join(",", $byDays);
        } else {
            $rRule = 'FREQ=WEEKLY;BYDAY=';
            $byDays = [];
            foreach ($days as $day) {
                $byDays[] = $day;
            }
            $rRule .= join(',', $byDays);
        }

        return $rRule;
    }
}
