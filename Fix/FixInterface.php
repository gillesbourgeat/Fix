<?php
/*************************************************************************************/
/*      This file is part of the Fix package.                                        */
/*                                                                                   */
/*      email : gilles.bourgeat@gmail.com                                            */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace Fix\Fix;

/**
 * Interface Fix
 * @package FixBug\Fix
 * @author Gilles Bourgeat <gbourgeat@openstudio.fr>
 */
interface FixInterface
{
    public function checkAction();

    public function applyAction();
}
