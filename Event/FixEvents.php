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

/**
 * Class FixEvents
 * @package Fix\Event
 * @author Gilles Bourgeat <gbourgeat@openstudio.fr>
 */
class FixEvents
{
    /** @var string */
    const APPLY = 'fix.apply';

    /** @var string */
    const CHECK = 'fix.check';

    /** @var string */
    const GENERATE = 'fix.generate';
}
