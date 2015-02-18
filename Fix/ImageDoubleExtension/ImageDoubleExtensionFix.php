<?php
/*************************************************************************************/
/*      This file is part of the Fix package.                                        */
/*                                                                                   */
/*      email : gilles.bourgeat@gmail.com                                            */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace Fix\Fix\ImageDoubleExtension;

use Fix\Exception\FixException;
use Fix\Fix\FixInterface;
use Thelia\Model\BrandImageQuery;
use Thelia\Model\CategoryImageQuery;
use Thelia\Model\ContentImageQuery;
use Thelia\Model\FolderImageQuery;
use Thelia\Model\Map\BrandImageTableMap;
use Thelia\Model\Map\CategoryImageTableMap;
use Thelia\Model\Map\ContentImageTableMap;
use Thelia\Model\Map\FolderImageTableMap;
use Thelia\Model\Map\ProductImageTableMap;
use Thelia\Model\ProductImage;
use Thelia\Model\ProductImageQuery;

/**
 * Class ImageDoubleExtensionFix
 * @package Fix\Fix
 * @author Gilles Bourgeat <gbourgeat@openstudio.fr>
 */
class ImageDoubleExtensionFix implements FixInterface
{
    /** @var string */
    const TYPE_PRODUCT = 'product';

    /** @var string */
    const TYPE_CATEGORY = 'category';

    /** @var string */
    const TYPE_BRAND = 'brand';

    /** @var string */
    const TYPE_CONTENT = 'content';

    /** @var string */
    const TYPE_FOLDER = 'folder';

    /** @var string */
    const FOLDER_IMAGE_PATH = 'local/media/images/';

    /**
     * @return array
     */
    public function checkAction()
    {
        $nbProductImage = self::getQuery(self::TYPE_PRODUCT)->find()->count();

        $nbCategoryImage = self::getQuery(self::TYPE_CATEGORY)->find()->count();

        $nbContentImage = self::getQuery(self::TYPE_CONTENT)->find()->count();

        $nbBrandImage = self::getQuery(self::TYPE_BRAND)->find()->count();

        $nbFolderImage = self::getQuery(self::TYPE_FOLDER)->find()->count();

        return array(
            $nbProductImage.' image(s) fount for products',
            $nbCategoryImage.' image(s) fount for categories',
            $nbContentImage.' image(s) fount for contents',
            $nbBrandImage.' image(s) fount for brands',
            $nbFolderImage.' image(s) fount for folders',
        );
    }

    /**
     * @return array
     */
    public function applyAction()
    {
        $finalLog = array();

        //product
        $productImage = self::getQuery(self::TYPE_PRODUCT)->find();

        $log = self::apply(self::TYPE_PRODUCT, $productImage);

        $finalLog = array_merge($finalLog, $log);

        //category
        $categoryImage = self::getQuery(self::TYPE_CATEGORY)->find();

        $log = self::apply(self::TYPE_CATEGORY, $categoryImage);

        $finalLog = array_merge($finalLog, $log);

        //content
        $contentImage = self::getQuery(self::TYPE_CONTENT)->find();

        $log = self::apply(self::TYPE_CONTENT, $contentImage);

        $finalLog = array_merge($finalLog, $log);

        //brand
        $brandImage = self::getQuery(self::TYPE_BRAND)->find();

        $log = self::apply(self::TYPE_BRAND, $brandImage);

        $finalLog = array_merge($finalLog, $log);

        //folder
        $folderImage = self::getQuery(self::TYPE_FOLDER)->find();

        $log = self::apply(self::TYPE_FOLDER, $folderImage);

        $finalLog = array_merge($finalLog, $log);

        return $finalLog;
    }

    /**
     * @param string $type
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria
     * @throws \Exception
     */
    private function getQuery($type)
    {
        if ($type === self::TYPE_PRODUCT) {
            return ProductImageQuery::create()->where(ProductImageTableMap::FILE." REGEXP '\.jpg\..+jpg'");
        } else if ($type === self::TYPE_CATEGORY) {
            return CategoryImageQuery::create()->where(CategoryImageTableMap::FILE." REGEXP '\.jpg\..+jpg'");
        } else if ($type === self::TYPE_BRAND) {
            return BrandImageQuery::create()->where(BrandImageTableMap::FILE." REGEXP '\.jpg\..+jpg'");
        } else if ($type === self::TYPE_CONTENT) {
            return ContentImageQuery::create()->where(ContentImageTableMap::FILE." REGEXP '\.jpg\..+jpg'");
        } else if ($type === self::TYPE_FOLDER) {
            return FolderImageQuery::create()->where(FolderImageTableMap::FILE." REGEXP '\.jpg\..+jpg'");
        } else {
            throw new FixException('Invalid type');
        }
    }

    /**
     * @param string $type
     * @param $images
     * @return array
     */
    private function apply($type, $images)
    {
        $folderPatch = realpath(__DIR__.DS.'..'.DS.'..'.DS.'..'.DS.'..'.DS.'..'.DS.self::FOLDER_IMAGE_PATH.$type.DS);

        /** @var int $nbRename */
        $nbRename = 0;

        /** @var int $nbError */
        $nbError = 0;

        /** @var ProductImage $image */
        foreach ($images as $image) {
            $oldName = $image->getFile();

            //create new name, replace first .jpg by ''
            $newName = preg_replace('/\.jpg/', '', $oldName, 1);

            //set new name to folder
            $rename = @rename($folderPatch.DS.$oldName, $folderPatch.DS.$newName);

            if ($rename === true) {
                //set new name to db
                $image->setFile($newName)->save();

                $nbRename++;
            } else {
                $nbError++;
            }

        }

        return array(
            $nbRename.' '.$type.' images renamed',
            $nbError.' '.$type.' images not renamed (error, file not found in file system)'
        );
    }
}
