<?php
/*************************************************************************************/
/*      This file is part of the Fix package.                                        */
/*                                                                                   */
/*      email : gilles.bourgeat@gmail.com                                            */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace Fix\Loop;

use Fix\Fix;
use Fix\Model\FixLog;
use Fix\Model\FixLogQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;

/**
 * Class FixLogLoop
 * @package Fix\Loop
 * @author Gilles Bourgeat <gbourgeat@openstudio.fr>
 */
class FixLogLoop extends BaseLoop implements PropelSearchLoopInterface
{
    /**
     * @return ArgumentCollection
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createAnyTypeArgument('code', null, true, false)
        );
    }

    /**
     * @return FixLogQuery|\Propel\Runtime\ActiveQuery\ModelCriteria
     */
    public function buildModelCriteria()
    {
        $code = $this->getCode();

        $logsQuery = FixLogQuery::create()->filterByCode($code);

        $logsQuery->orderById(Criteria::DESC);

        $logsQuery->limit(100);

        return $logsQuery;
    }

    /**
     * @param LoopResult $loopResult
     * @return LoopResult
     */
    public function parseResults(LoopResult $loopResult)
    {
        /** @var FixLog $log */
        foreach ($loopResult->getResultDataCollection() as $key => $log) {
            $loopResultRow = new LoopResultRow();

            $loopResultRow
                ->set('ID', $log->getId())
                ->set('CODE', $log->getCode())
                ->set('LOG', $log->getLog())
                ->set('ACTION', $log->getAction())
                ->set('COMMAND', $log->getCommand())
                ->set('ADMIN_ID', $log->getAdminId())
                ->set("CREATE_DATE", $log->getCreatedAt()->format('Y-m-d H:i:s'));

            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }
}
