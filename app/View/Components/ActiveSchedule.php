<?php

namespace App\View\Components;

use App\Helpers;
use App\Conflict;
use Illuminate\View\Component;

class ActiveSchedule extends Component
{
    private $id;
    private $title;
    private $className;
    private $isClose;
    private $timeClassName = '';
    private $timeTitle;
    private $timeText;
    private $quotaClassName;
    private $quotaText;

    public function __construct($schedule)
    {
        $this->id = $schedule['id'];

        $timeFormat = 'dddd, DD MMMM, YYYY';

        if ($schedule['activeDate']->isFuture()) {
            $this->title = 'Jadwal ini akan berlaku mulai ' . $schedule['activeDate']->isoFormat($timeFormat);
            $this->className = 'fst-italic grey';
        }
        elseif ($schedule['deletedAt']) {
            $this->title = 'Jadwal ini akan terhapus pada ' . $schedule['deletedAt']->isoFormat($timeFormat);
            $this->className = 'text-decoration-line-through';
        }

        $this->isClose = $schedule['quota'] === 0;

        if (!$schedule['deletedAt']) {
            $this->timeClassName = 'editable';
            if ($schedule['replacedWith']) {
                $this->timeClassName .= ' text-warning';

                $this->timeTitle = "Sudah ada pasien yang mendaftar pada jadwal ini\n";
                $this->timeTitle .= 'Jadwal asli: ' . $schedule['time'];
            }
        }

        if (Conflict::contain($schedule['id'])) {
            $this->timeClassName .= ' text-warning';
        }

        $this->timeText = (is_null($schedule['replacedWith']) ? $schedule : $schedule['replacedWith'])['time'];

        if (!$schedule['deletedAt']) {
            $this->quotaClassName = 'editable';
        }

        $this->quotaText = Helpers::formatSlotTime($schedule['quota'], $schedule['time']);
    }

    public function render()
    {
        return view('components.active-schedule', get_object_vars($this));
    }
}
