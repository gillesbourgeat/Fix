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

use Fix\Event\FixEvents;
use Fix\Event\FixGenerateEvent;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Thelia\Command\ContainerAwareCommand;

/**
 * Class FixGenerateCommand
 * @package Fix\Command
 * @author Gilles Bourgeat <gbourgeat@openstudio.fr>
 */
class FixGenerateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setname('fix:generate')
            ->addArgument(
                'code',
                InputArgument::REQUIRED,
                'Specify the fix code'
            )
            ->setDescription('Generate fix');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $code = $input->getArgument('code');

        $event = new FixGenerateEvent($code);

        $event->setCommand(true);

        /** @var \Symfony\Component\EventDispatcher\EventDispatcher $dispatcher */
        $dispatcher = $this->getContainer()->get('event_dispatcher');

        $dispatcher->dispatch(FixEvents::GENERATE, $event);

        $logs = $event->getLogs();

        foreach ($logs as $log) {
            $output->writeln($log);
        }
    }
}
