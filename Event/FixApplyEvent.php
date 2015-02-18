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

use Fix\Helper\ActionHelper;

/**
 * Class FixApplyEvent
 * @package Fix\Event
 * @author Gilles Bourgeat <gbourgeat@openstudio.fr>
 */
class FixApplyEvent extends FixEvent
{
    /**
     * @param string $code
     */
    public function __construct($code)
    {
        $this->setCode($code);

        $this->setAction(ActionHelper::APPLY);
    }
}
