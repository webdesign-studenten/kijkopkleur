<?php
namespace WebdesignStudenten\MassAction\Ui;

class MassAction extends \Magento\Ui\Component\MassAction
{
    public function prepare()
    {
        parent::prepare();

            $config = $this->getConfiguration();
            $notAllowedActions = ['pdfdocs_order','print_shipping_label','pdfshipments_order'];
            $allowedActions = [];
            foreach ($config['actions'] as $action) {
                if (!in_array($action['type'], $notAllowedActions)) {
                    $allowedActions[] = $action;
                }
            }
            $config['actions'] = $allowedActions;
            $this->setData('config', (array)$config);
       
    }
}