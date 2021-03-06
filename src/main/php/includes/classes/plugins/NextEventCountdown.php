<?php

require_once 'includes/classes/Events.php';

use \libAllure\Form;
use \libAllure\Inflector;
use \libAllure\ElementTextbox;

class NextEventCountdown implements Plugin {
	public function getSettingsForm() {
		return new FormCountdownSettings();
	}

	private function shouldNotDisplay() {
		if (atLan()) return true;

		$excludesPages = explode("\n", getSiteSetting('plugin.countdown.ignorePages'));

		return in_array(basename($_SERVER['PHP_SELF']), $excludesPages); 
	}

	public function renderSidebar() {
		if ($this->shouldNotDisplay()) {
			return;
		}
		
		startbox();

		$event = Events::nextEvent();

		if ($event['seatingPlan']) {
			echo '<p>The seating plan is now live! Pay, then choose your seat!</p><div style = "text-align: center">';
			echo '<a href = "seatingplan.php?event=' . $event['id'] . '"><img src = "resources/images/seatingPlan.png" alt = "seating plan" /></a></div>';
		}

		if (empty($event)) {
			echo '<p>No events planned!</p>';
		} else {
			$diff = (strtotime($event['dateIso']) - time());

			$days = $diff /= 86400;
			$days = floor($days);

			echo '<p><a href = "viewEvent.php?id=' . $event['id'] . '">' . $event['name'] . '</a> starts in <strong>' . $days . '</strong> ' . Inflector::quantify('day', $days). '.  </p>';
		}

		stopbox('Countdown!');
	}
}

class FormCountdownSettings extends Form {
	public function __construct() {
		parent::__construct('countdownSettings', 'Countdown settings');

		$this->addElement(new ElementTextbox('excludePages', 'Exclude pages', getSiteSetting('plugin.countdown.ignorePages')));
		$this->addDefaultButtons();
	}

	public function process() {
		setSiteSetting('plugin.countdown.ignorePages', $this->getElementValue('excludePages'));
	}
}



?>
