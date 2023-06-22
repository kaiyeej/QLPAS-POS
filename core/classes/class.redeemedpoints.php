<?php

class RedeemedPoints extends Connection {
    private $table = 'tbl_redeemed_points';
    public $pk = 'redeem_id';
    public $name = 'reference_number';

    public $module_name = "Redeemed Points";
    public $inputs = [];

    public function add()
    {
        $form = array(
            $this->name     => $this->clean($this->inputs[$this->name]),
            'sales_id'      => $this->inputs['sales_id'],
            'customer_id'  => $this->inputs['customer_id'],
            'redeem_points'  => $this->inputs['redeem_points'],
            'sales_summary_id'  => $this->inputs['sales_summary_id'],
            'encoded_by'  => $this->inputs['encoded_by'],
            'status'        => 'S'
        );
        return $this->insertIfNotExist($this->table, $form, '', 'Y');
    }

    public function finish()
    {
        $sales_id = $this->inputs['sales_id'];
        $form = array(
            'status' => 'F',
        );
        return $this->update($this->table, $form, "sales_id = '$sales_id'");
    }

    public function generate()
    {
        return 'RP-' . date('YmdHis');
    }

    public function show()
    {
        $Customers = new Customers;
        $param = isset($this->inputs['param']) ? $this->inputs['param'] : null;
        $rows = array();
        $result = $this->select($this->table, '*', $param);
        while ($row = $result->fetch_assoc()) {

            $sales_id = $row['sales_id'];
            $fetch_sales = $this->select("tbl_sales", "*", "sales_id = '$sales_id'");
            $sales_row = $fetch_sales->fetch_array();

            $customer_name = $sales_row['customer_id'] > 0 ? $Customers->name($sales_row['customer_id']) : 'Walk-in';
            $row['customer'] = $customer_name;
            $total = $row['total_amount'];
            $row['total'] = number_format($total, 2);
            $row['total_nonformat'] = $total;
            $row['sales_num'] = $sales_row['reference_number'];
            $row['sales_type'] = $sales_row['sales_type'];

            $rows[] = $row;
        }
        return $rows;
    }

    public function get_reward_points($sales_id){
        $Settings = new Settings;
        
        $fetch_total_sales = $this->select("tbl_sales_details", "sum((quantity*price)-discount) as total", "sales_id = '$sales_id'");
        $sales_row = $fetch_total_sales->fetch_assoc();
        $reward_points_factor = $Settings->get_reward_points_factor();

        $reward_points = $reward_points_factor > 0 ? floor($sales_row['total']/$reward_points_factor) : 0;

        return $reward_points;
        
    }
}

/*

CREATE TABLE `tbl_redeemed_points` (
  `redeem_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `reference_number` varchar(30) NOT NULL DEFAULT '',
  `customer_id` int(11) NOT NULL,
  `sales_id` int(11) NOT NULL,
  `redeem_points` decimal(9,2) NOT NULL,
  `encoded_by` int(11) NOT NULL,
  `status` varchar(1) NOT NULL DEFAULT '',
  `sales_summary_id` int(11) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_last_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`redeem_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

*/

?>