<?php
/*************************************************************************************/
/*      This file is part of the Fix package.                                        */
/*                                                                                   */
/*      email : gilles.bourgeat@gmail.com                                            */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace Fix\Form\Admin;

use Thelia\Form\BaseForm;

/**
 * Class FixApplyForm
 * @package Fix\Form\Admin
 * @author Gilles Bourgeat <gbourgeat@openstudio.fr>
 */
class FixApplyForm extends BaseForm
{

    protected function buildForm()
    {

    }

    /**
     * @return string the name of you form. This name must be unique
     */
    public function getName()
    {
        return "fix-apply-form";
    }
}
