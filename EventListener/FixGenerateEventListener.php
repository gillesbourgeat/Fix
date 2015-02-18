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
use Fix\Fix;
use Fix\Helper\ActionHelper;
use Fix\Model\FixLog;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class FixGenerateEventListener
 * @package Fix\EventListener
 * @author Gilles Bourgeat <gbourgeat@openstudio.fr>
 */
class FixGenerateEventListener implements EventSubscriberInterface
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     * @return array The event names to listen to
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            FixEvents::GENERATE => array(
                'generate', 128
            )
        );
    }

    /**
     * @param FixEvent $event
     */
    public function generate(FixEvent $event)
    {
        $fs = new Filesystem();

        $insert = new FixLog();

        $code = $event->getCode();

        $insert
            ->setAction(ActionHelper::GENERATE)
            ->setCode($code)
            ->setCommand($event->getCommand())
            ->setAdminId($event->getAdminId())
            ->save();

        try {
            Fix::checkCode($code);

            $skeletonPath = THELIA_MODULE_DIR . DIRECTORY_SEPARATOR . "Fix" . DIRECTORY_SEPARATOR . "Skeleton";
            $fixPath = THELIA_MODULE_DIR . "Fix" . DIRECTORY_SEPARATOR . "Fix" . DIRECTORY_SEPARATOR . $code;

            // directory
            if (!$fs->exists($fixPath)) {
                $fs->mkdir($fixPath);
            }

            // json
            if (!$fs->exists($fixPath . DIRECTORY_SEPARATOR . $code . "Fix.json")) {
                $jsonContent = file_get_contents($skeletonPath . DIRECTORY_SEPARATOR . "fix.json");

                file_put_contents($fixPath . DIRECTORY_SEPARATOR . $code . "Fix.json", $jsonContent);
            }

            // class php
            if (!$fs->exists($fixPath . DIRECTORY_SEPARATOR . $code . "Fix.php")) {
                $phpContent = file_get_contents($skeletonPath . DIRECTORY_SEPARATOR . "fix.php.template");

                $phpContent = str_replace("%%FIXCODE%%", $code, $phpContent);
                $phpContent = str_replace("%%CLASSNAME%%", $code . "Fix", $phpContent);

                file_put_contents($fixPath . DIRECTORY_SEPARATOR . $code . "Fix.php", $phpContent);
            }

            $logs = "";

        } catch (\Exception $e) {
            $logs = $e->getMessage();

            $event->setLogs(array($logs));
        }

        $insert
            ->setLog($logs)
            ->save();
    }
}
