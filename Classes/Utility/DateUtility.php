<?php

declare(strict_types=1);

namespace GuteBotschafter\GbEvents\Utility;

use DateInterval;
use DateTime;
use Exception;
use GuteBotschafter\GbEvents\Domain\Model\Event;

class DateUtility
{
    protected array $settings = [];

    protected array $excludedDates = [];

    /**
     * DateUtility constructor.
     *
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        $this->settings = $settings;

        $this->initializeExcludedDates();
    }

    protected function initializeExcludedDates()
    {
        // Global excludes
        if (intval($this->settings['forceExcludeHolidays']) !== 0) {
            if (is_array($this->settings['holidays']) && count($this->settings['holidays']) !== 0) {
                foreach ($this->settings['holidays'] as $holiday) {
                    try {
                        $date = $this->expandExcludeDate($holiday);
                        $this->excludedDates[$date->format('Y')][$date->format('m-d')] = 1;
                    } catch (Exception $e) {
                        continue;
                    }
                }
            }
        }
    }

    /**
     * This returns the initial event dates including
     * all recurring events up to and including the
     * stopDate, taking the defined end of recurrance
     * into account
     *
     * @param Event    $event
     * @param DateTime $startDate
     * @param DateTime $stopDate
     * @param bool     $expandedList
     *
     * @return array $eventDates
     */
    public function getEventDates(Event $event, DateTime $startDate, DateTime $stopDate, bool $expandedList = false): array
    {
        $oneDay = new DateInterval('P1D');
        $oneMonth = new DateInterval('P1M');

        $startMonth = clone($startDate);
        $startMonth->modify('first day of this month');
        $stopMonth = clone($stopDate);
        $stopMonth->modify('last day of this month');
        $recurringMonths = [];

        while ($startMonth <= $stopMonth) {
            $recurringMonths[] = clone($startMonth);
            $startMonth->add($oneMonth);
        }

        $recurringWeeks = self::getRecurringWeeksAsText($event);
        $recurringDays = self::getRecurringDaysAsText($event);
        $eventDates = [];
        foreach ($recurringMonths as $workDate) {
            /** @var DateTime $workDate */
            $workingMonth = $workDate->format('n');

            // Weeks have been selected, check every nth week / day combination
            if (count($recurringWeeks) !== 0) {
                foreach (self::getRecurringWeeksAsText($event) as $week) {
                    foreach (self::getRecurringDaysAsText($event) as $day) {
                        $workDate->modify(sprintf('%s %s of this month', $week, $day));
                        if ($workingMonth === $workDate->format('n')
                            && $workDate >= $event->getEventDate()
                            && (is_null($event->getRecurringStop()) || $workDate <= $event->getRecurringStop())
                            && $workDate >= $startDate
                            && $workDate <= $stopDate
                        ) {
                            if ($this->isExcludedDate($event, $workDate)) {
                                continue;
                            }
                            $eventDates[$workDate->format('Y-m-d')] = clone($workDate);
                            if (!$this->settings['startDateOnly'] || $expandedList) {
                                $re_StartDate = clone($workDate);
                                $difference = $event->getEventDate()->diff($re_StartDate);
                                $re_StopDate = clone($event->getEventStopDate());
                                $re_StopDate->add($difference);
                                while ($re_StartDate <= $re_StopDate) {
                                    $eventDates[$re_StartDate->format('Y-m-d')] = clone($re_StartDate);
                                    $re_StartDate->modify('+1 day');
                                }
                            }
                        }
                    }
                }
            } else {
                // Check the weekdays only, ignoring the weeks of the month
                $stopDay = clone($workDate);
                $stopDay->modify('last day of this month');
                while ($workDate <= $stopDay) {
                    $addCurrentDay = false;
                    switch ($workDate->format('w')) {
                        case 0:
                        case 7:
                            $addCurrentDay = in_array('Sunday', $recurringDays);
                            break;
                        case 1:
                            $addCurrentDay = in_array('Monday', $recurringDays);
                            break;
                        case 2:
                            $addCurrentDay = in_array('Tuesday', $recurringDays);
                            break;
                        case 3:
                            $addCurrentDay = in_array('Wednesday', $recurringDays);
                            break;
                        case 4:
                            $addCurrentDay = in_array('Thursday', $recurringDays);
                            break;
                        case 5:
                            $addCurrentDay = in_array('Friday', $recurringDays);
                            break;
                        case 6:
                            $addCurrentDay = in_array('Saturday', $recurringDays);
                            break;
                    }
                    if ($addCurrentDay && !$this->isExcludedDate($event, $workDate)) {
                        if ($workDate >= $event->getEventDate()
                            && (is_null($event->getRecurringStop()) || $workDate <= $event->getRecurringStop())
                            && $workDate >= $startDate
                            && $workDate <= $stopDate
                        ) {
                            $eventDates[$workDate->format('Y-m-d')] = clone($workDate);
                            if (!$this->settings['startDateOnly'] || $expandedList) {
                                $re_StartDate = clone($workDate);
                                $difference = $event->getEventDate()->diff($re_StartDate);
                                $re_StopDate = clone($event->getEventStopDate());
                                $re_StopDate->add($difference);
                                while ($re_StartDate <= $re_StopDate) {
                                    $eventDates[$re_StartDate->format('Y-m-d')] = clone($re_StartDate);
                                    $re_StartDate->modify('+1 day');
                                }
                            }
                        }
                    }
                    $workDate->add($oneDay);
                }
            }
        }
        $myStartDate = clone($event->getEventDate());
        $myStopDate = clone($event->getEventStopDate());
        if (!$this->settings['startDateOnly'] || $expandedList) {
            while ($myStartDate <= $myStopDate) {
                if (!$this->isExcludedDate($event, $myStartDate)) {
                    $eventDates[$myStartDate->format('Y-m-d')] = clone($myStartDate);
                }
                $myStartDate->modify('+1 day');
            }
        } else {
            $eventDates[$myStartDate->format('Y-m-d')] = clone($myStartDate);
        }

        $eventDates[$event->getEventDate()->format('Y-m-d')] = $event->getEventDate();
        ksort($eventDates);

        return $eventDates;
    }

    /**
     * @param Event $event
     *
     * @return array
     */
    protected static function getRecurringWeeksAsText(Event $event): array
    {
        $weeks = [];
        if ($event->getRecurringWeeks() & 1) {
            $weeks[] = 'first';
        }
        if ($event->getRecurringWeeks() & 2) {
            $weeks[] = 'second';
        }
        if ($event->getRecurringWeeks() & 4) {
            $weeks[] = 'third';
        }
        if ($event->getRecurringWeeks() & 8) {
            $weeks[] = 'fourth';
        }
        if ($event->getRecurringWeeks() & 16) {
            $weeks[] = 'fifth';
        }
        if ($event->getRecurringWeeks() & 32) {
            $weeks[] = 'last';
        }

        return $weeks;
    }

    /**
     * @param Event $event
     *
     * @return array
     */
    public static function getRecurringDaysAsText(Event $event): array
    {
        $days = [];
        if ($event->getRecurringDays() === 0 && $event->getRecurringWeeks() !== 0) {
            switch ($event->getEventDate()->format('w')) {
                case 0:
                case 7:
                    $days[] = 'Sunday';
                    break;
                case 1:
                    $days[] = 'Monday';
                    break;
                case 2:
                    $days[] = 'Tuesday';
                    break;
                case 3:
                    $days[] = 'Wednesday';
                    break;
                case 4:
                    $days[] = 'Thursday';
                    break;
                case 5:
                    $days[] = 'Friday';
                    break;
                case 6:
                    $days[] = 'Saturday';
                    break;
            }
        } else {
            if ($event->getRecurringDays() & 1) {
                $days[] = 'Monday';
            }
            if ($event->getRecurringDays() & 2) {
                $days[] = 'Tuesday';
            }
            if ($event->getRecurringDays() & 4) {
                $days[] = 'Wednesday';
            }
            if ($event->getRecurringDays() & 8) {
                $days[] = 'Thursday';
            }
            if ($event->getRecurringDays() & 16) {
                $days[] = 'Friday';
            }
            if ($event->getRecurringDays() & 32) {
                $days[] = 'Saturday';
            }
            if ($event->getRecurringDays() & 64) {
                $days[] = 'Sunday';
            }
        }

        return $days;
    }

    /**
     * Check if the given date is to be excluded from the list of recurring events
     *
     * @param Event    $event
     * @param DateTime $date
     *
     * @return boolean
     */
    protected function isExcludedDate(Event $event, DateTime $date): bool
    {
        if (array_key_exists($date->format('Y'), $this->excludedDates) && array_key_exists($date->format('m-d'), $this->excludedDates[$date->format('Y')])
        ) {
            return true;
        }
        if (array_key_exists('0000', $this->excludedDates)
            && array_key_exists($date->format('m-d'), $this->excludedDates['0000'])
        ) {
            return true;
        }

        // Per event excludes
        foreach ($this->getRecurringExcludeDatesArray($event) as $excludedDate) {
            if (trim($excludedDate) === '') {
                continue;
            }
            try {
                $expandedExcludeDate = $this->expandExcludeDate($excludedDate);

                if (($date->format('Y') === $expandedExcludeDate->format('Y') || $expandedExcludeDate->format('Y') === '000') && $date->format('m-d') === $expandedExcludeDate->format('m-d')) {
                    return true;
                }
            } catch (Exception $e) {
                continue;
            }
        }

        return false;
    }

    /**
     * Expand the given date to include a year (if missing) and convert to a
     * DateTime object
     *
     * @param string $excludeDate
     *
     * @throws Exception
     *
     * @return DateTime
     */
    protected function expandExcludeDate(string $excludeDate): DateTime
    {
        if (preg_match('#^\d{1,2}\.\d{1,2}\.?$#', $excludeDate)) {
            $excludeDate = str_replace('..', '.', sprintf('%s.%s', $excludeDate, '0000'));
        } else {
            if (preg_match('#^\d{1,2}-\d{1,2}$#', $excludeDate)) {
                $excludeDate = sprintf('%s-%s', '0000', $excludeDate);
            }
        }

        return new DateTime($excludeDate);
    }

    /**
     * Gets the Dates on which recurring events do not occur.
     *
     * @param Event $event
     *
     * @return array
     */
    protected function getRecurringExcludeDatesArray(Event $event): array
    {
        return preg_split("#[\r\n]+|$#", $event->getRecurringExcludeDates() ?? '');
    }
}
