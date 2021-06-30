<?php
/**
 *
 * @category : RLTSquare
 * @Package  : RLTSquare_ColorSwatch
 * @Author   : RLTSquare <support@rltsquare.com>
 * @copyright Copyright 2021 © rltsquare.com All right reserved
 * @license https://rltsquare.com/
 */
namespace RLTSquare\ColorSwatch\Block\Adminhtml\ColorSwatch;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class GenericButton
 */
class GenericButton
{
    /**
     * @var Context
     */
    protected $context;
    /**
     * @var BlockRepositoryInterface
     */
    protected $blockRepository;
    /**
     * @param Context $context
     * @param BlockRepositoryInterface $blockRepository
     */
    public function __construct(
        Context $context
    ) {
        $this->context = $context;
    }
    /**
     * Return CMS block ID
     *
     * @return int|null
     */
    public function getBlockId()
    {
        return null;
    }
    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
