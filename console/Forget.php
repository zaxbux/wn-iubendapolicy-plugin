<?php

namespace Zaxbux\IubendaPolicy\Console;

use Zaxbux\IubendaPolicy\Classes\PolicyCache;
use Illuminate\Console\Command;

class Forget extends Command {
	protected $name = 'iubenda:forget';
	protected $description = 'Remove the policies from the cache.';

	public function handle() {
		$cache = new PolicyCache();

		$this->writeln('Forgetting policies... ');
		$cache->forget();
		$this->writeln('Done!');
	}
}