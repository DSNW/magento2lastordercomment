<?php
namespace DSNW\LastOrderComment\Model\Plugin\Sales\Order;

class Grid {
    public static $table = 'sales_order_grid';
    public static $tableHistory = 'sales_order_status_history';

    public function afterSearch($intercepter, $collection) {
        if ($collection->getMainTable() === $collection->getConnection()->getTableName(self::$table)) {
            $history = $collection->getConnection()->getTableName(self::$tableHistory);

            $collection->getSelect()->joinLeft(
                ['t' => new \Zend_Db_Expr('
                        (SELECT t01.comment, t01.parent_id
                         FROM ' . $history . ' AS t01
                         INNER JOIN (
                            SELECT parent_id, MAX(entity_id) AS entity_id
                            FROM ' . $history . ' AS t0
                            WHERE entity_name = \'order\'
                            GROUP BY parent_id
                         ) AS t02
                         ON t01.parent_id = t02.parent_id AND t01.entity_id = t02.entity_id)
                ')],
                't.parent_id = main_table.entity_id',
                ['comment']
            );
        }
        return $collection;
    }
}