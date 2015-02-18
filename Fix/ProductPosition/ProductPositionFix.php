<?php
/*************************************************************************************/
/*      This file is part of the Fix package.                                        */
/*                                                                                   */
/*      email : gilles.bourgeat@gmail.com                                            */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace Fix\Fix\ProductPosition;

use Fix\Fix\FixInterface;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Model\CategoryQuery;
use Thelia\Model\Product;
use Thelia\Model\ProductCategory;
use Thelia\Model\ProductCategoryQuery;
use Thelia\Model\ProductQuery;

/**
 * Class ProductPositionFix
 * @package Fix\Fix
 * @author Gilles Bourgeat <gbourgeat@openstudio.fr>
 */
class ProductPositionFix implements FixInterface
{
    /**
     * @return array
     */
    public function checkAction()
    {
        $logs = array();

        $categories = array();

        $products = ProductQuery::create()->find();

        /** @var Product $product */
        foreach ($products as $product) {
            $defaultCategory = $product->getDefaultCategoryId();
            $position = $product->getPosition();

            if ($defaultCategory < 0 || $defaultCategory === null) {
                $logs[] = "Product ".$product->getId()." has not category";
                continue;
            }

            if (!isset($categories[$defaultCategory])) {
                $categories[$defaultCategory] = array();
            }

            if (!isset($categories[$defaultCategory][$position])) {
                $categories[$defaultCategory][$position] = array();
            }

            $categories[$defaultCategory][$position][] = $product->getId();

        }

        foreach ($categories as $category) {
            foreach ($category as $position) {
                if (count($position) > 1) {
                    $logs[] = 'Duplicate position for the product "'.implode(', ', $position).'"';
                }
            }
        }

        if (!count($logs)) {
            $logs[] = 'No conflict detected';
        }

        return $logs;
    }

    /**
     * @return array
     */
    public function applyAction()
    {
        $logs = array();

        $categories = array();

        $products = ProductQuery::create()->find();

        /** @var Product $product */
        foreach ($products as $product) {
            $defaultCategory = $product->getDefaultCategoryId();
            $position = $product->getPosition();

            // test if product has default category
            if ($defaultCategory < 0 || $defaultCategory === null) {
                //looking if the product belongs to a category
                $categoryFind = ProductCategoryQuery::create()->findOneByProductId($product->getId());

                if ($categoryFind !== null) {
                    //attribute to a default category
                    $categoryFind = CategoryQuery::create()->findOne();

                    if ($categoryFind !== null) {
                        $categoryFindId = $categoryFind->getId();
                    } else {
                        continue;
                    }

                } else {
                    $categoryFindId = $categoryFind->getCategoryId();
                }

                $insert = new ProductCategory();
                $insert
                    ->setDefaultCategory(true)
                    ->setProductId($product->getId())
                    ->setCategoryId($categoryFindId)
                    ->save();

                $logs[] = "the product ".$product->getId()." was attributed to the category ".$categoryFindId;

                $defaultCategory = $categoryFindId;
            }

            if (!isset($categories[$defaultCategory])) {
                $categories[$defaultCategory] = array();
            }

            if (!isset($categories[$defaultCategory][$position])) {
                $categories[$defaultCategory][$position] = array();
            }

            $categories[$defaultCategory][$position][] = $product->getId();
        }

        foreach ($categories as $category) {
            foreach ($category as $position) {
                if (count($position) > 1) {
                    foreach ($position as $key => $productId) {
                        //it does not move the first duplicate
                        if ($key > 0) {
                            $product = ProductQuery::create()->findOneById($productId);

                                $productFind = ProductQuery::create()
                                    ->orderByPosition(Criteria::DESC)
                                    ->useProductCategoryQuery(null, Criteria::INNER_JOIN)
                                        ->filterByDefaultCategory(true)
                                        ->filterByCategoryId($product->getDefaultCategoryId())
                                    ->endUse()
                                    ->findOne();

                                if ($productFind === null) {
                                    $newPosition = 1;
                                } else {
                                    $newPosition = $productFind->getPosition() + 1;
                                }

                            $product
                                ->setPosition($newPosition)
                                ->save();

                            $logs[] = "The product ".$product->getId()." has been moved to the position ".$newPosition;
                        }
                    }
                }
            }
        }

        if (!count($logs)) {
            $logs[] = 'No conflict detected';
        }

        return $logs;
    }
}
