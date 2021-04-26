<?php
namespace Magenuts\Productattach\Model\Import\CustomImport;

interface RowValidatorInterface extends \Magento\Framework\Validator\ValidatorInterface
{
       const ERROR_INVALID_TITLE= 'InvalidValueTITLE';
       const ERROR_MESSAGE_IS_EMPTY = 'EmptyMessage';
       public function init($context);
}