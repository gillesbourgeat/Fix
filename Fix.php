<?php
/*************************************************************************************/
/*      This file is part of the Fix package.                                        */
/*                                                                                   */
/*      email : gilles.bourgeat@gmail.com                                            */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace Fix;

use Fix\Exception\FixException;
use Thelia\Module\BaseModule;
use Propel\Runtime\Connection\ConnectionInterface;
use Thelia\Install\Database;

/**
 * Class Fix
 * @package Fix
 * @author Gilles Bourgeat <gbourgeat@openstudio.fr>
 */
class Fix extends BaseModule
{
    /**
     * @param ConnectionInterface $con
     */
    public function postActivation(ConnectionInterface $con = null)
    {
        $database = new Database($con->getWrappedConnection());
        $database->insertSql(null, array(__DIR__ . '/Config/thelia.sql'));
    }

    /**
     * @param string $code
     * @return string
     */
    public static function getFixPath($code = '')
    {
        if (!empty($code)) {
            return THELIA_MODULE_DIR . DIRECTORY_SEPARATOR . "Fix". DIRECTORY_SEPARATOR . 'Fix' . DIRECTORY_SEPARATOR . $code;
        }

        return THELIA_MODULE_DIR . DIRECTORY_SEPARATOR . "Fix" . DIRECTORY_SEPARATOR . 'Fix';
    }

    /**
     * @param $code
     * @return bool
     * @throws FixException
     */
    public static function exists($code)
    {
        self::checkCode($code);

        if (!file_exists(self::getFixPath($code).DS.$code.'Fix.php')) {
            throw new FixException("Fix not found");
        }

        return true;
    }

    /**
     * @param string $code
     * @return bool
     * @throws \Exception
     */
    public static function checkCode($code)
    {
        if (empty($code) || !preg_match("/^[a-z0-9]+$/i", $code)) {
            throw new FixException("Invalid code fix name");
        }

        return true;
    }
}
