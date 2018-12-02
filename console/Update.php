<?php

namespace Zaxbux\Console;

use Zaxbux\IubendaPolicy\Classes\PolicyCache;
use Illuminate\Console\Command;

class Update extends Command {
	protected $name = 'iubenda:update';
	protected $description = 'Download fresh versions of the policies.';

	public function handle() {
		$cache = new PolicyCache();

		$this->write('Updating policies... ');
		$cache->update();
		$this->writeln('Done!');
	}
}