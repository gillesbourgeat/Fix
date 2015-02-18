<?php
/*************************************************************************************/
/*      This file is part of the Fix package.                                        */
/*                                                                                   */
/*      email : gilles.bourgeat@gmail.com                                            */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace Fix\EventListener;

use Fix\Event\FixEvent;
use Fix\Event\FixEvents;
use Fix\Fix\FixInterface;
use Fix\Helper\ActionHelper;
use Fix\Model\FixLog;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Fix\Fix;

/**
 * Class FixCheckEventListener
 * @package Fix\EventListener
 * @author Gilles Bourgeat <gbourgeat@openstudio.fr>
 */
class FixCheckEventListener implements EventSubscriberInterface
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     * @return array The event names to listen to
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            FixEvents::CHECK => array(
                'check', 128
            )
        );
    }

    /**
     * @param FixEvent $event
     */
    public function check(FixEvent $event)
    {
        $insert = new FixLog();

        $code = $event->getCode();

        $insert
            ->setAction(ActionHelper::CHECK)
            ->setCode($code)
            ->setCommand($event->getCommand())
            ->setAdminId($event->getAdminId())
            ->save();

        try {
            Fix::exists($code);

            $class = '\Fix\Fix\\'.$code.'\\'.$code.'Fix';

            $fix = new $class();

            /** @var FixInterface $fix */
            $logs = $fix->checkAction();

            $event->setLogs($logs);

            $logs = implode("\r\n", $logs);

        } catch (\Exception $e) {
            $logs = $e->getMessage();

            $event->setLogs(array($logs));
        }

        $insert
            ->setLog($logs)
            ->save();
    }
}
