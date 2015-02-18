<?php
/*************************************************************************************/
/*      This file is part of the Fix package.                                        */
/*                                                                                   */
/*      email : gilles.bourgeat@gmail.com                                            */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace Fix\Event;

use Fix\Exception\FixException;
use Fix\Helper\ActionHelper;
use Thelia\Core\Event\ActionEvent;

/**
 * Class FixEvent
 * @package Fix\Event
 * @author Gilles Bourgeat <gbourgeat@openstudio.fr>
 */
class FixEvent extends ActionEvent
{
    /** @var string */
    protected $code;

    /** @var string */
    protected $action;

    /** @var array  */
    protected $logs = array();

    /** @var bool */
    protected $command = false;

    /** @var int */
    protected $adminId = 0;

    /**
     * @param string $code
     * @param string $action
     */
    public function __construct($code, $action = ActionHelper::CHECK)
    {
        $this->setAction($action);

        $this->setCode($code);
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        if (empty($code)) {
            throw new FixException("Invalid fix code");
        }

        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     * @throws \Exception
     */
    public function setAction($action)
    {
        if ($action !== ActionHelper::APPLY
            && $action !== ActionHelper::CHECK
        ) {
            throw new FixException("Invalid fix action");
        }

        $this->action = $action;
    }

    /**
     * @return array
     */
    public function getLogs()
    {
        return $this->logs;
    }

    /**
     * @param string $message
     */
    public function setLog($message)
    {
        $this->logs = array_merge($this->logs, array($message));
    }

    /**
     * @param array $logs
     */
    public function setLogs(array $logs)
    {
        $this->logs = array_merge($this->logs, $logs);
    }

    /**
     * @return bool
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @param $command
     */
    public function setCommand($command)
    {
        $this->command = $command;
    }

    /**
     * @return int
     */
    public function getAdminId()
    {
        return $this->adminId;
    }

    /**
     * @param int $adminId
     */
    public function setAdminId($adminId)
    {
        $this->adminId = $adminId;
    }
}
