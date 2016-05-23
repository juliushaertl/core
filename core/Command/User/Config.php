<?php
/**
 * @author Julius Härtl <jus@bitgrid.net>
 *
 * @copyright Copyright (c) 2016, ownCloud, Inc.
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OC\Core\Command\User;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

use OCP\IUserManager;
use OCP\IConfig;

class Config extends Command {
	/** @var IUserManager */
	protected $userManager;
	/** @var IConfig */
	protected $config;

	/**
	 * @param IConfig $config
	 */
	public function __construct(IUserManager $userManager, IConfig $config) {
		$this->config = $config;
		$this->userManager = $userManager;
		parent::__construct();
	}

	protected function configure() {
		$this
			->setName('user:config')
			->setDescription('get all user defined values')
			->addArgument(
				'uid',
				InputArgument::REQUIRED,
				'the username'
			)
			->addArgument(
				'appname',
				InputArgument::OPTIONAL,
				'the app name',
				false
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$uid = $input->getArgument('uid');
		$user = $this->userManager->get($uid);
		if(is_null($user)) {
			$output->writeln('<error>User does not exist</error>');
			return;
		}
		$apps = $input->getArgument('appname');
		if($apps) {
			$apps = [$apps];
		} else {
			$apps = ['core','apporder'];
		}
		foreach($apps as $appname) {
			$output->writeln("[" . $appname . "]");
			$keys = $this->config->getUserKeys($uid, $appname);
			foreach($keys as $key) {
				$value = $this->config->getUserValue($uid, $appname, $key);
				$output->writeln($key . ' = ' . $value);
			}
		}
	}
}
