<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-feed
 * @version   1.0.91
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Feed\Service;

use Mirasvit\Core\Service\YamlService;
use Mirasvit\Feed\Api\Service\ExportServiceInterface;

class ExportService implements ExportServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function export($entityModel, $path)
    {
        $yaml = YamlService::dump(
            $entityModel->toArray($entityModel->getRowsToExport()),
            10
        );

        file_put_contents($path, $yaml);
    }
}