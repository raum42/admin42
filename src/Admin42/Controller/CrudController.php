<?php
/**
 * admin42 (www.raum42.at)
 *
 * @link      http://www.raum42.at
 * @copyright Copyright (c) 2010-2014 raum42 OG (http://www.raum42.at)
 *
 */

namespace Admin42\Controller;

use Admin42\Crud\AbstractOptions;
use Admin42\Mvc\Controller\AbstractAdminController;
use Core42\View\Model\JsonModel;
use Zend\Http\Response;
use Zend\View\Model\ViewModel;

class CrudController extends AbstractAdminController
{
    /**
     * @return string
     */
    protected function getCurrentRoute()
    {
        return $this
            ->getServiceLocator()
            ->get('Application')
            ->getMvcEvent()
            ->getRouteMatch()
            ->getMatchedRouteName();
    }

    /**
     * @return AbstractOptions
     */
    protected function getCrudOptions()
    {
        return $this
            ->getServiceLocator()
            ->get('Admin42\CrudOptionsPluginManager')
            ->get($this->params("crud"));
    }

    public function indexAction()
    {
        $selector = $this->getSelector($this->getCrudOptions()->getSelectorName());
        if ($this->getRequest()->isXmlHttpRequest()) {
            return $selector->getResult();
        }

        $viewModel = new ViewModel([
            'icon' => $this->getCrudOptions()->getIcon(),
            'name' => $this->getCrudOptions()->getName(),
            'editRoute' => $this->getCurrentRoute() . '/edit',
            'addRoute' => $this->getCurrentRoute() . '/add',
            'deleteRoute' => $this->getCurrentRoute() . '/delete',
        ]);

        $viewModel->setTemplate($this->getCrudOptions()->getIndexViewTemplate());

        return $viewModel;
    }

    public function detailAction()
    {
        $isEditMode = $this->params()->fromRoute("isEditMode");

        $prg = $this->prg();
        if ($prg instanceof Response) {
            return $prg;
        }

        $currentRoute = $this->getCurrentRoute();
        $currentRoute = str_replace('/add', "", $currentRoute);
        $currentRoute = str_replace('/edit', "", $currentRoute);

        $createEditForm = $this->getForm($this->getCrudOptions()->getFormName());
        if ($prg !== false) {
            if ($isEditMode === true) {
                $cmdName = $this->getCrudOptions()->getEditCommandName();
                $cmd = $this->getCommand($cmdName);
                $cmd->setId($this->params()->fromRoute("id"));

                if (method_exists($cmd, "setTableGatewayName")) {
                    $cmd->setTableGatewayName($this->getCrudOptions()->getTableGatewayName());
                }
            } else {
                $cmdName = $this->getCrudOptions()->getCreateCommandName();
                $cmd = $this->getCommand($cmdName);
                if (method_exists($cmd, "setTableGatewayName")) {
                    $cmd->setTableGatewayName($this->getCrudOptions()->getTableGatewayName());
                }
            }

            $formCommand = $this->getFormCommand();
            $model = $formCommand->setForm($createEditForm)
                ->setCommand($cmd)
                ->setData($prg)
                ->run();
            if (!$formCommand->hasErrors()) {
                $this->flashMessenger()->addSuccessMessage([
                    'title' => 'toaster.item.edit.title.success',
                    'message' => 'toaster.item.edit.content.success',
                ]);

                return $this->redirect()->toRoute($currentRoute . '/edit', array('id' => $model->getId()));
            } else {
                $this->flashMessenger()->addErrorMessage([
                    'title' => 'toaster.item.edit.title.failed',
                    'message' => 'toaster.item.edit.content.failed',
                ]);
            }
        } else {
            if ($isEditMode === true) {
                $model = $this
                    ->getTableGateway($this->getCrudOptions()->getTableGatewayName())
                    ->selectByPrimary((int) $this->params()->fromRoute("id"));

                $createEditForm->setData($model->toArray());
            }
        }

        $viewModel = new ViewModel([
            'createEditForm' => $createEditForm,
            'editRoute' => $currentRoute . '/edit',
            'addRoute' => $currentRoute . '/add',
            'deleteRoute' => $currentRoute . '/delete',
        ]);

        return $viewModel;
    }

    public function deleteAction()
    {
        $cmdName = $this->getCrudOptions()->getDeleteCommandName();

        $redirectRoute = $this->getCurrentRoute();
        $redirectRoute = str_replace("/delete", "", $redirectRoute);

        if ($this->getRequest()->isDelete()) {
            $deleteCmd = $this->getCommand($cmdName);

            $deleteParams = array();
            parse_str($this->getRequest()->getContent(), $deleteParams);

            $deleteCmd->setId((int) $deleteParams['id']);

            if (method_exists($deleteCmd, "setTableGatewayName")) {
                $deleteCmd->setTableGatewayName($this->getCrudOptions()->getTableGatewayName());
            }

            $deleteCmd->run();

            return new JsonModel(array(
                'success' => true,
            ));
        } elseif ($this->getRequest()->isPost()) {
            $deleteCmd = $this->getCommand($cmdName);

            $deleteCmd->setId((int) $this->params()->fromPost('id'));

            if (method_exists($deleteCmd, "setTableGatewayName")) {
                $deleteCmd->setTableGatewayName($this->getCrudOptions()->getTableGatewayName());
            }

            $deleteCmd->run();

            $this->flashMessenger()->addSuccessMessage([
                'title' => 'toaster.item.delete.title.success',
                'message' => 'toaster.item.delete.message.success',
            ]);

            return new JsonModel([
                'redirect' => $this->url()->fromRoute($redirectRoute)
            ]);
        }

        return new JsonModel([
            'redirect' => $this->url()->fromRoute($redirectRoute)
        ]);
    }
}