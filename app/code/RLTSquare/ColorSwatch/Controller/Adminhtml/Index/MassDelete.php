<?php
/**
 *
 * @category : RLTSquare
 * @Package  : RLTSquare_ColorSwatch
 * @Author   : RLTSquare <support@rltsquare.com>
 * @copyright Copyright 2021 © rltsquare.com All right reserved
 * @license https://rltsquare.com/
 */
namespace RLTSquare\ColorSwatch\Controller\Adminhtml\Index;

use RLTSquare\ColorSwatch\Controller\Adminhtml\AbstractMassDelete;
/**
 * Class MassDelete
 */
class MassDelete extends AbstractMassDelete
{
    /**
     * Field id
     */
    const ID_FIELD = 'colorswatch_id';
    /**
     * ResourceModel collection
     *
     * @var string
     */
    protected $collection = 'RLTSquare\ColorSwatch\Model\ResourceModel\ColorSwatch\Collection';
    /**
     * Page model
     *
     * @var string
     */
    protected $model = 'RLTSquare\ColorSwatch\Model\ColorSwatch';
}
