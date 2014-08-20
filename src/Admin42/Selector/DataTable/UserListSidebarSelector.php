<?php
/**
 * admin42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2014 raum42 OG (http://www.raum42.at)
 *
 */

namespace Admin42\Selector\DataTable;

use Admin42\DataTable\AbstractDataTableSelector;
use Admin42\DataTable\AbstractTableGatewaySelector;
use Admin42\DataTable\DataTable;
use Admin42\DataTable\Decorator\DateTimeDecorator;
use Admin42\DataTable\Decorator\RoleDecorator;
use Admin42\DataTable\Mutator\DateTimeMutator;
use Admin42\Model\User;
use Zend\Db\Sql\Predicate\In;
use Zend\Db\Sql\Sql;

class UserListSidebarSelector extends AbstractTableGatewaySelector
{
    /**
     * @var string
     */
    protected $tableGateway = 'Admin42\User';

    /**
     * @param DataTable $dataTable
     * @return mixed
     */
    protected function configure(DataTable $dataTable)
    {
        $dataTable->setAjax('admin/user/index-sidebar');

        $dataTable->addAttribute("dom", 't<"text-center"p>');

        $dataTable->addColumn(array(
            'label' => 'Email',
            'match_name' => 'email',
            'sortable' => true,
            'searchable' => true,
        ));

        $dataTable->addEditButton('admin/user/edit', array('id' => 'id'));
    }

    /**
     * @return \Zend\Db\Sql\Select
     */
    public function getSelect()
    {
        $select = parent::getSelect();
        $select->where(new In('status', array(User::STATUS_ACTIVE)));

        return $select;
    }
}
