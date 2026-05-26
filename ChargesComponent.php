<?php

namespace Apps\Tms\Components\Tools\Charges;

use Apps\Tms\Packages\Adminltetags\Traits\DynamicTable;
use Apps\Tms\Packages\Tools\Charges\ToolsCharges;
use System\Base\BaseComponent;

class ChargesComponent extends BaseComponent
{
    use DynamicTable;

    protected $chargesPackage;

    public function initialize()
    {
        $this->chargesPackage = $this->usePackage(ToolsCharges::class);
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            $this->view->chargeTypes = $this->chargesPackage->getChargeTypes();

            if ($this->getData()['id'] != 0) {
                $charge = $this->chargesPackage->getById((int) $this->getData()['id']);

                if (!$charge) {
                    return $this->throwIdNotFound();
                }

                $this->view->charge = $charge;
            }

            $this->view->pick('charges/view');

            return;
        }

        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'edit'      => 'tools/charges'
                ]
            ];

        $replaceColumns =
            function ($dataArr) {
                if ($dataArr && is_array($dataArr) && count($dataArr) > 0) {
                    foreach ($dataArr as &$data) {
                        if ($data['type'] == '0') {
                            $data['type'] = 'Product (' . $data['type'] . ')';
                        } else if ($data['type'] == '1') {
                            $data['type'] = 'Charges (' . $data['type'] . ')';
                        }
                    }
                }

                return $dataArr;
            };

        $this->generateDTContent(
            $this->chargesPackage,
            'tools/charges/view',
            null,
            ['name', 'type'],
            true,
            ['name', 'type'],
            $controlActions,
            ['type' => 'type (id)'],
            $replaceColumns,
            'name'
        );

        $this->view->pick('charges/list');
    }

    /**
     * @acl(name=add)
     */
    public function addAction()
    {
        $this->requestIsPost();

        $this->chargesPackage->addCharge($this->postData());

        $this->addResponse(
            $this->chargesPackage->packagesData->responseMessage,
            $this->chargesPackage->packagesData->responseCode
        );
    }

    /**
     * @acl(name=update)
     */
    public function updateAction()
    {
        $this->requestIsPost();

        $this->chargesPackage->updateCharge($this->postData());

        $this->addResponse(
            $this->chargesPackage->packagesData->responseMessage,
            $this->chargesPackage->packagesData->responseCode
        );
    }

    /**
     * @acl(name=remove)
     */
    public function removeAction()
    {
        $this->requestIsPost();

        $this->chargesPackage->removeCharge($this->postData());

        $this->addResponse(
            $this->chargesPackage->packagesData->responseMessage,
            $this->chargesPackage->packagesData->responseCode
        );
    }
}