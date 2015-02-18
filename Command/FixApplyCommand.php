<?php
/*************************************************************************************/
/*      This file is part of the Fix package.                                        */
/*                                                                                   */
/*      email : gilles.bourgeat@gmail.com                                            */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace Fix\Command;

use Fix\Event\FixCheckEvent;
use Fix\Event\FixEvents;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Thelia\Command\ContainerAwareCommand;

/**
 * Class FixApplyCommand
 * @package Fix\Command
 * @author Gilles Bourgeat <gbourgeat@openstudio.fr>
 */
class FixApplyCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setname('fix:apply')
            ->addArgument(
                'code',
                InputArgument::REQUIRED,
                'Specify the fix'
            )
            ->setDescription('Apply the fix');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $code = $input->getArgument('code');

        $event = new FixCheckEvent($code);

        $event->setCommand(true);

        /** @var \Symfony\Component\EventDispatcher\EventDispatcher $dispatcher */
        $dispatcher = $this->getContainer()->get('event_dispatcher');

        $dispatcher->dispatch(FixEvents::APPLY, $event);

        $logs = $event->getLogs();

        foreach ($logs as $log) {
            $output->writeln($log);
        }
    }
}
