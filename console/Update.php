<?php

namespace Zaxbux\IubendaPolicy\Console;

use Zaxbux\IubendaPolicy\Classes\PolicyCache;
use Illuminate\Console\Command;

class Update extends Command {
	protected $name = 'iubenda:update';
	protected $description = 'Download fresh versions of the policies.';

	public function handle() {
		$cache = new PolicyCache();

		$this->writeln('Updating policies... ');
		$cache->update();
		$this->writeln('Done!');
	}
}