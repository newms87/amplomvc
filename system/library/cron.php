<?php

class Cron extends Library
{
	private $settings;

	public function __construct()
	{
		parent::__construct();
		$this->settings = $this->config->loadGroup('cron');
	}

	public function check()
	{
		//TODO: Re-enable Cron when convenient
		return;

		if (empty($this->settings['cron_last_run'])) {
			$this->run();
		} else {
			$diff = $this->date->diff($this->settings['cron_last_run']);

			//Run Cron every minute (will check task times before executing them)
			if (($diff->days + $diff->h + $diff->i) > 0) {
				$this->run();
			}
		}
	}

	public function run()
	{
		//Maximum execution time is 5 minutes
		set_time_limit(defined("AMPLO_CRON_TIMEOUT") ? AMPLO_CRON_TIMEOUT : 300);

		//System User enables full permissions
		$this->user->loginSystemUser();

		$msg = _l("------ Cron START %s ------\r\n\r\n", $this->date->now());

		if (!empty($this->settings['cron_tasks'])) {
			foreach ($this->settings['cron_tasks']['tasks'] as &$task) {
				if (empty($task['status'])) {
					continue;
				}

				$msg .= "Task $task[name]: ";

				if (empty($task['last_run'])) {
					$task['last_run'] = DATETIME_ZERO;
				}
				$last_scheduled = $this->getPrevRun($task['time']);

				$msg .= _l("Last Scheduled (%s), ", $this->date->format($last_scheduled, 'full'));
				$msg .= _l("Last Run (%s)\r\n", $this->date->format($task['last_run'], 'full'));

				$diff = $this->date->diff($last_scheduled, $task['last_run']);

				if ($diff->days + $diff->h + $diff->i <= 0) {
					$msg .= _l("Already ran.");
				} elseif (!$task['last_run'] || $this->date->isAfter($last_scheduled, $task['last_run'])) {
					$task['last_run'] = $this->date->now();

					$msg .= _l("Executing %s\r\n", $task['name']);

					//Verify class exists
					if (!is_file(DIR_CRON . $task['file'] . '.php')) {
						continue;
					}

					$classname = "System_Cron_" . _2camel($task['file']);
					$method    = $task['method'];

					if (method_exists($classname, $method)) {
						$this->$classname->$method();
					} else {
						$error = _l("Cron::run(): Failed to run %s. Class Method, %s() was not found.", $task['name'], $classname . '::' . $method);
						$msg .= $error;
						write_log('error', $error);
					}
				} else {
					$msg .= _l("Skipping...");
				}

				$msg .= "\r\n\r\n";
			}
			unset($task);
		} else {
			$msg .= _l("There are no tasks to run.\r\n");
		}

		$this->settings['cron_last_run'] = $this->date->now();

		$this->config->saveGroup('cron', $this->settings, false);

		$msg .= _l("Cron ran successfully!");

		write_log('default', $msg);

		//Always logout system user when done!
		$this->user->logoutSystemUser();

		return $msg;
	}

	public function getNextRun($tab)
	{
		$times = $this->parse($tab);

		$date = $this->date->datetime();

		while (true) {
			$current = $this->date->getCronUnits($date);

			if (!in_array($current['m'], $times['m'])) {
				$_m  = $this->getNext($current['m'], $times['m']);
				$d_m = $this->diff($current['m'], $_m, 12);
				$date->setDate($current['y'], $current['m'] + $d_m, 1);
				$date->setTime(0, 0, 0);
				continue;
			}

			if (!in_array($current['w'], $times['w'])) {
				$_w  = $this->getNext($current['w'], $times['w']);
				$d_w = $this->diff($current['w'], $_w, 7);
				$date->setDate($current['y'], $current['m'], $current['d'] + $d_w);
				$date->setTime(0, 0, 0);
				continue;
			}

			if (!in_array($current['d'], $times['d'])) {
				$_d = $this->getNext($current['d'], $times['d']);
				if ($_d < $current['d']) {
					$date->setDate($current['y'], $current['m'] + 1, $_d);
				} else {
					$date->setDate($current['y'], $current['m'], $_d);
				}
				$date->setTime(0, 0, 0);
				continue;
			}

			if (!in_array($current['h'], $times['h'])) {
				$_h  = $this->getNext($current['h'], $times['h']);
				$d_h = $this->diff($current['h'], $_h, 24);
				$date->setTime($current['h'] + $d_h, 0, 0);
				continue;
			}

			if (!in_array($current['i'], $times['i'])) {
				$_i  = $this->getNext($current['i'], $times['i']);
				$d_i = $this->diff($current['i'], $_i, 60);
				$date->setTime($current['h'], $current['i'] + $d_i, 0);
				continue;
			}

			break;
		}

		return $date;
	}

	public function getPrevRun($tab)
	{
		$times = $this->parse($tab);

		$date = $this->date->datetime();

		while (true) {
			$current = $this->date->getCronUnits($date);

			if (!in_array($current['m'], $times['m'])) {
				$_m  = $this->getPrev($current['m'], $times['m']);
				$d_m = $this->diff($_m, $current['m'], 12);
				$date->setDate($current['y'], $current['m'] - $d_m, 1);
				$temp = $this->date->getCronUnits($date);
				$date->setDate($temp['y'], $temp['m'], $temp['t']);
				$date->setTime(23, 59, 0);
				continue;
			}

			if (!in_array($current['w'], $times['w'])) {
				$_w  = $this->getPrev($current['w'], $times['w']);
				$d_w = $this->diff($_w, $current['w'], 7);
				$date->setDate($current['y'], $current['m'], $current['d'] - $d_w);
				$date->setTime(23, 59, 0);
				continue;
			}

			if (!in_array($current['d'], $times['d'])) {
				$_d = $this->getPrev($current['d'], $times['d']);
				if ($_d > $current['d']) {
					$date->setDate($current['y'], $current['m'] - 1, $_d);
				} else {
					$date->setDate($current['y'], $current['m'], $_d);
				}
				$date->setTime(23, 59, 0);
				continue;
			}

			if (!in_array($current['h'], $times['h'])) {
				$_h  = $this->getPrev($current['h'], $times['h']);
				$d_h = $this->diff($_h, $current['h'], 24);
				$date->setTime($current['h'] - $d_h, 59, 0);
				continue;
			}

			if (!in_array($current['i'], $times['i'])) {
				$_i  = $this->getPrev($current['i'], $times['i']);
				$d_i = $this->diff($_i, $current['i'], 60);
				$date->setTime($current['h'], $current['i'] - $d_i, 0);
				continue;
			}

			break;
		}

		return $date;
	}

	public function parse($tab)
	{
		return array(
			'i' => $this->getMinutes($tab['i']),
			'h' => $this->getHours($tab['h']),
			'd' => $this->getDaysOfMonth($tab['d']),
			'm' => $this->getMonths($tab['m']),
			'w' => $this->getDaysOfWeek($tab['w']),
		);
	}

	private function getNext($n, $times)
	{
		foreach ($times as $t) {
			if ($n <= $t) {
				return $t;
			}
		}

		reset($times);
		return current($times);
	}

	private function getPrev($n, $times)
	{
		foreach (array_reverse($times) as $t) {
			if ($n >= $t) {
				return $t;
			}
		}

		reset($times);
		return current($times);
	}

	private function diff($a, $b, $range)
	{
		return ($a < $b) ? $b - $a : ($b + $range) - $a;
	}

	private function getMinutes($minute)
	{
		$minutes = $this->getTimes(str_replace("*", "0-59", $minute));

		foreach ($minutes as $i) {
			if ($i < 0 || $i > 59) {
				unset($minutes[$i]);
			}
		}

		asort($minutes);

		return $minutes;
	}

	private function getHours($hour)
	{
		$hours = $this->getTimes(str_replace("*", "0-23", $hour));

		foreach ($hours as $h) {
			if ($h < 0 || $h > 23) {
				unset($hours[$h]);
			}
		}

		asort($hours);

		return $hours;
	}

	private function getDaysOfMonth($day_of_month)
	{
		$days_of_month = $this->getTimes(str_replace("*", "1-31", $day_of_month));

		foreach ($days_of_month as $d) {
			if ($d < 1 || $d > 31) {
				unset($days_of_month[$d]);
			}
		}

		asort($days_of_month);

		return $days_of_month;
	}

	private function getMonths($month)
	{
		$months = $this->getTimes(str_replace("*", "1-12", $month));

		foreach ($months as $m) {
			if ($m < 1 || $m > 12) {
				unset($months[$m]);
			}
		}

		asort($months);

		return $months;
	}

	private function getDaysOfWeek($day_of_week)
	{
		$days_of_week = $this->getTimes(str_replace("*", "0-6", $day_of_week));

		foreach ($days_of_week as $w) {
			if ($w < 0 || $w > 6) {
				unset($days_of_week[$w]);
			}
		}

		asort($days_of_week);

		return $days_of_week;
	}

	private function getTimes($tab)
	{
		$times = array();

		$list = explode(',', $tab);

		foreach ($list as $l) {
			$nums = $this->getDivisor($l);

			$times += $nums ? $nums : $this->getRange($l);
		}

		return $times;
	}

	private function getDivisor($divisor)
	{
		$nums = array();

		if (strpos($divisor, '/') !== false) {
			list($range, $div) = explode('/', $divisor);
			$range = $this->getRange($range);
			$mod   = current($range) % (int)$div;

			foreach ($range as $r) {
				if ($r % (int)$div === $mod) {
					$nums[$r] = $r;
				}
			}
		}

		return $nums;
	}

	private function getRange($range)
	{
		if (strpos($range, '-') !== false) {
			list($from, $to) = explode("-", $range);
			$range_list = range((int)$from, (int)$to);
			$nums       = array();
			foreach ($range_list as $r) {
				$nums[$r] = $r;
			}

			return $nums;
		} else {
			return array((int)$range => (int)$range);
		}
	}
}
