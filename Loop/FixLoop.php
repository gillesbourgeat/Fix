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
use Symfony\Component\Filesystem\Filesystem;
use Thelia\Core\Template\Element\ArraySearchLoopInterface;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;

/**
 * Class FixLoop
 * @package Fix\Loop
 * @author Gilles Bourgeat <gbourgeat@openstudio.fr>
 */
class FixLoop extends BaseLoop implements ArraySearchLoopInterface
{
    /**
     * @return ArgumentCollection
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection();
    }

    /**
     * @return array
     */
    public function buildArray()
    {
        $locale = $this->request->getSession()->getLang()->getLocale();

        $fs = new Filesystem();

        $fixPath = Fix::getFixPath();

        $fix = [];

        $pointer = opendir($fixPath);

        while ($file = readdir($pointer)) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($fixPath.DS.$file)) {
                    $title = "";
                    $description = "";
                    $theliaVersionMin = "";
                    $theliaVersionMax = "";

                    $pathTemplate = THELIA_MODULE_DIR . DIRECTORY_SEPARATOR . "Fix" . DIRECTORY_SEPARATOR . "Fix" .DIRECTORY_SEPARATOR. $file . DIRECTORY_SEPARATOR . $file . "Fix.html";
                    $pathJson = THELIA_MODULE_DIR . DIRECTORY_SEPARATOR . "Fix" . DIRECTORY_SEPARATOR . "Fix" .DIRECTORY_SEPARATOR. $file . DIRECTORY_SEPARATOR . $file . "Fix.json";

                    if ($fs->exists($pathJson)) {
                        $json = json_decode(file_get_contents($pathJson), true);

                        if ($json) {
                            //title and description
                            if (isset($json['i18n'])) {
                                if (isset($json['i18n'][$locale])) {
                                    $title = (isset($json['i18n'][$locale]['title'])) ? $json['i18n'][$locale]['title'] : "";
                                    $description = (isset($json['i18n'][$locale]['description'])) ? $json['i18n'][$locale]['description'] : "";
                                } else if (isset($json['i18n']['en_US'])) {
                                    {
                                        $title = (isset($json['i18n']['en_US']['title'])) ? $json['i18n']['en_US']['title'] : "";
                                        $description = (isset($json['i18n']['en_US']['description'])) ? $json['i18n']['en_US']['description'] : "";
                                    }
                                }

                                if (isset($json['theliaVersion'])) {
                                    if (isset($json['theliaVersion'][0])) {
                                        $theliaVersionMin = $json['theliaVersion'][0];
                                    }
                                    if (isset($json['theliaVersion'][1])) {
                                        $theliaVersionMax = $json['theliaVersion'][1];
                                    }
                                }
                            }
                        }
                    }

                    $fix[] = array(
                        'Code' => $file,
                        'Path' => $fixPath.DS.$file,
                        "Title" => $title,
                        "Description" => $description,
                        "TheliaVersionMin" => $theliaVersionMin,
                        "TheliaVersionMax" => $theliaVersionMax
                    );
                }
            }
        }

        return $fix;
    }

    /**
     * @param LoopResult $loopResult
     * @return LoopResult
     */
    public function parseResults(LoopResult $loopResult)
    {
        foreach ($loopResult->getResultDataCollection() as $key => $fix) {
            $loopResultRow = new LoopResultRow();

            $loopResultRow
                ->set('CODE', $fix['Code'])
                ->set('PATH', $fix['Path'])
                ->set("HTML", "")
                ->set("TITLE", $fix['Title'])
                ->set("DESCRIPTION", $fix['Description'])
                ->set("THELIA_VERSION_MIN", $fix['TheliaVersionMin'])
                ->set("THELIA_VERSION_MAX", $fix['TheliaVersionMax'])
            ;

            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }
}
