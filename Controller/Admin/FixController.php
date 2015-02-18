<?php
/*************************************************************************************/
/*      This file is part of the Fix package.                                        */
/*                                                                                   */
/*      email : gilles.bourgeat@gmail.com                                            */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace Fix\Controller\Admin;

use Fix\Event\FixApplyEvent;
use Fix\Event\FixCheckEvent;
use Fix\Event\FixEvents;
use Fix\Fix;
use Fix\Form\Admin\FixApplyForm;
use Fix\Form\Admin\FixCheckForm;
use Fix\Helper\ActionHelper;
use Fix\Model\FixLogQuery;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;

/**
 * Class FixController
 * @package Fix\Controller\Admin
 * @author Gilles Bourgeat <gbourgeat@openstudio.fr>
 */
class FixController extends BaseAdminController
{
    /**
     * @param string $code
     * @return mixed
     */
    public function checkAction($code)
    {
        if (null !== $response = $this->checkAuth(AdminResources::MODULE, 'Fix', AccessManager::UPDATE)) {
            return $response;
        }

        /** @var string $errorMessage */
        $errorMessage = '';

        /** @var bool $error */
        $error = false;

        $formActive = new FixCheckForm($this->getRequest());

        try {
            Fix::checkCode($code);

            $this->validateForm($formActive, "post");

            $event = new FixCheckEvent($code);

            $event->setAdminId($this->getSecurityContext()->getAdminUser()->getId());

            $this->dispatch(FixEvents::CHECK, $event);

        } catch (\Exception $e) {
            $error = true;
            $errorMessage = $e->getMessage();
        }

        $response = $this->render(
            'module-configure',
            [
                'module_code' => 'Fix',
                'logs' => (isset($event)) ? $event->getLogs() : array(),
                'fixCode' => $code,
                'error' => $error,
                'error_message' => $errorMessage,
                'action' => ActionHelper::CHECK
            ]
        );

        return $response;
    }

    /**
     * @param string $code
     * @return mixed
     */
    public function applyAction($code)
    {
        if (null !== $response = $this->checkAuth(AdminResources::MODULE, 'Fix', AccessManager::UPDATE)) {
            return $response;
        }

        /** @var string $errorMessage */
        $errorMessage = '';

        /** @var bool $error */
        $error = false;

        $formActive = new FixApplyForm($this->getRequest());

        try {
            Fix::checkCode($code);

             $this->validateForm($formActive, "post");

            $event = new FixApplyEvent($code);

            $event->setAdminId($this->getSecurityContext()->getAdminUser()->getId());

            $this->dispatch(FixEvents::APPLY, $event);

        } catch (\Exception $e) {
            $error = true;
            $errorMessage = $e->getMessage();
        }

        $response = $this->render(
            'module-configure',
            [
                'module_code' => 'Fix',
                'logs' => (isset($event)) ? $event->getLogs() : array(),
                'fixCode' => $code,
                'error' => $error,
                'error_message' => $errorMessage,
                'action' => ActionHelper::APPLY
            ]
        );

        return $response;
    }

    public function logsAction($code)
    {
        if (null !== $response = $this->checkAuth(AdminResources::MODULE, 'Fix', AccessManager::VIEW)) {
            return $response;
        }

        Fix::checkCode($code);

        return $this->render("Fix/logs", array(
            'code' => $code
        ));
    }
}
