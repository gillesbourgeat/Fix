<?php
/*************************************************************************************/
/*      This file is part of the Fix package.                                        */
/*                                                                                   */
/*      email : gilles.bourgeat@gmail.com                                            */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace Fix\Fix\CategoryPosition;

use Fix\Fix\FixInterface;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Model\Category;
use Thelia\Model\CategoryQuery;

/**
 * Class CategoryPositionFix
 * @package Fix\Fix
 * @author Gilles Bourgeat <gbourgeat@openstudio.fr>
 */
class CategoryPositionFix implements FixInterface
{
    /**
     * @return array
     */
    public function checkAction()
    {
        $logs = self::testPosition(0);

        if (!count($logs)) {
            $logs[] = "No conflict detected";
        }

        return $logs;
    }

    /**
     * @return array
     */
    public function applyAction()
    {
        $logs = self::fixPosition(0);

        if (!count($logs)) {
            $logs[] = "No conflict detected";
        }

        return $logs;
    }

    /**
     * @param int $parentId
     * @return array
     */
    private function testPosition($parentId = 0)
    {
        $logs = array();

        $categoreis = CategoryQuery::create()
            ->findByParent($parentId);

        $positions = array();

        /** @var Category $category */
        foreach ($categoreis as $category) {
            if (!isset($positions[$category->getPosition()])) {
                $positions[$category->getPosition()] = array();
            }

            $positions[$category->getPosition()][] = $category->getId();

        }

        foreach ($positions as $position) {
            if (count($position) > 1) {
                $logs[] = 'Duplicate position for the category "'.implode(', ', $position).'"';
            }
        }

        //test children cat
        /** @var Category $category */
        foreach ($categoreis as $category) {
            $subLog = self::testPosition($category->getId());

            if (count($subLog)) {
                $logs = array_merge($logs, $subLog);
            }

        }

        return $logs;
    }

    /**
     * @param int $parentId
     * @return array
     */
    private function fixPosition($parentId = 0)
    {
        $logs = array();

        $categoreis = CategoryQuery::create()
            ->findByParent($parentId);

        $positions = array();

        /** @var Category $category */
        foreach ($categoreis as $category) {
            if (!isset($positions[$category->getPosition()])) {
                $positions[$category->getPosition()] = array();
            }

            $positions[$category->getPosition()][] = $category->getId();

        }

        foreach ($positions as $position) {
            if (count($position) > 1) {
                foreach ($position as $key => $categoryId) {
                    //it does not move the first duplicate
                    if ($key > 0) {
                        $categoryFind = CategoryQuery::create()
                            ->orderByPosition(Criteria::DESC)
                            ->findOneByParent($parentId);

                        if ($categoryFind === null) {
                            $newPosition = 1;
                        } else {
                            $newPosition = $categoryFind->getPosition() + 1;
                        }

                        CategoryQuery::create()
                            ->findOneById($categoryId)
                            ->setPosition($newPosition)
                            ->save();

                        $logs[] = "The category ".$categoryId." has been moved to the position ".$newPosition;

                    }
                }
            }
        }

        //test children cat
        /** @var Category $category */
        foreach ($categoreis as $category) {
            $subLog = self::fixPosition($category->getId());

            if (count($subLog)) {
                $logs = array_merge($logs, $subLog);
            }

        }

        return $logs;
    }
}
