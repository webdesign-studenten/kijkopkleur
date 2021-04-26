<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_PDFCustom
 */


namespace Amasty\PDFCustom\Ui;

use Amasty\PDFCustom\Model\ConfigProvider;

class MassAction extends \Magento\Ui\Component\MassAction
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        ConfigProvider $configProvider,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        array $components = [],
        array $data = []
    ) {
        $this->configProvider = $configProvider;
        parent::__construct($context, $components, $data);
    }

    /**
     * Prepare component configuration
     *
     * @return void
     */
    public function prepare()
    {
        if (!$this->configProvider->isEnabled()) {
            unset($this->components['pdforders_order']);
        }
        parent::prepare();
    }
}
