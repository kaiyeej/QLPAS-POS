<?php
class SalesReturn extends Connection
{
    private $table = 'tbl_sales_return';
    public $pk = 'sales_return_id';
    public $name = 'reference_number';

    private $table_detail = 'tbl_sales_return_details';
    public $pk2 = 'return_detail_id';
    public $fk_det = 'product_id';

    public $module = 'SLS-RET-';

    public function add()
    {
        $Sales = new Sales;
        $sales_id = $Sales->pk_by_name($this->inputs['sales_reference_number']);
        $form = array(
            $this->name     => $this->clean($this->inputs[$this->name]),
            'sales_id'      => $sales_id,
            'remarks'       => $this->inputs['remarks'],
            'return_date'   => $this->inputs['return_date'],
            'encoded_by' => $_SESSION['user']['id']
        );
        $sales_return_id = $this->insert($this->table, $form, 'Y');

        $form_detail = array(
            'sales_return_id'   => "$sales_return_id AS sales_return_id",
            'sales_detail_id'   => 'sales_detail_id',
            'product_id'        => 'product_id',
            'quantity'          => 'quantity',
            'price'             => 'price',
            'discount'          => 'discount',
            'cost'              => 'cost'
        );

        $this->insert_select($this->table_detail, 'tbl_sales_details', $form_detail, "sales_id = '$sales_id'");
        return $sales_return_id;
    }

    public function edit()
    {
        $form = array(
            'remarks'       => $this->inputs['remarks'],
            'return_date'   => $this->inputs['return_date'],
            'encoded_by' => $_SESSION['user']['id']
        );
        return $this->updateIfNotExist($this->table, $form);
    }

    public function show()
    {
        $Sales = new Sales;
        $Users = new Users;
        $param = isset($this->inputs['param']) ? $this->inputs['param'] : null;
        $rows = array();
        $result = $this->select($this->table, '*', $param);
        while ($row = $result->fetch_assoc()) {
            $row['sales_reference_number'] = $Sales->name($row['sales_id']);
            $row['encoded_name'] = $Users->getUser($row['encoded_by']);
            $rows[] = $row;
        }
        return $rows;
    }

    public function view()
    {
        $Sales = new Sales;
        $primary_id = $this->inputs['id'];
        $result = $this->select($this->table, "*", "$this->pk = '$primary_id'");
        $row = $result->fetch_assoc();
        $row['sales_reference_number'] = $Sales->name($row['sales_id']);
        return $row;
    }

    public function getStatus($primary_id)
    {
        $result = $this->select($this->table, "status", "$this->pk = '$primary_id'");
        $row = $result->fetch_assoc();
        return $row['status'];
    }

    public function remove()
    {
        $ids = implode(",", $this->inputs['ids']);
        return $this->delete($this->table, "$this->pk IN($ids)");
    }

    public function generate()
    {
        return $this->module . date('YmdHis');
    }

    public function finish()
    {
        $primary_id = $this->inputs['id'];
        $Products = new Products();
        $result = $this->select($this->table_detail, " * ", "$this->pk = '$primary_id'");
        while ($row = $result->fetch_array()) {
            $Products->prodAVG($row['product_id'], $row['quantity_return'], $row['cost']);
        }

        $form = array(
            'status' => 'F',
        );
        return $this->update($this->table, $form, "$this->pk = '$primary_id'");
    }

    public function add_detail()
    {
        $primary_id_2       = $this->inputs[$this->pk2];
        $quantity_return    = $this->inputs['quantity_return'];

        $result = $this->select($this->table_detail, 'quantity,quantity_return,sales_detail_id', "$this->pk2 = '$primary_id_2'");
        $row = $result->fetch_assoc();

        $new_quantity_return = $row['quantity_return'] + $quantity_return;
        if (($new_quantity_return+$this->total_return($row['sales_detail_id'])) > $row['quantity']) {
            return 2;
        } else {
            $form = array(
                'quantity_return' => $new_quantity_return,
            );
            return $this->update($this->table_detail, $form, "$this->pk2 = '$primary_id_2'");
        }
    }

    public function total_return($primary_id){
        $result = $this->select("tbl_sales_return_details as d, tbl_sales_return as p" , 'sum(d.quantity_return)', "p.sales_return_id=d.sales_return_id AND p.status='F' AND d.sales_detail_id='$primary_id'");
        $row = $result->fetch_array();

        return $row[0];
    }

    public function show_detail()
    {
        $Products = new Products();
        $param = isset($this->inputs['param']) ? $this->inputs['param'] : null;
        $rows = array();
        $result = $this->select($this->table_detail, '*', $param);
        while ($row = $result->fetch_assoc()) {
            if($this->get_status($row['sales_return_id']) == "F"){
                $total = $row['quantity_return'];
            }else{
                $total = $row['quantity_return'] + $this->total_return($row['sales_detail_id']);
            }
            $row['product'] = Products::name($row['product_id']);
            $row['amount'] = $row['quantity'] * $row['price'];
            $row['quantity_return'] = $total;
            $rows[] = $row;
        }
        return $rows;
    }

    public function get_status($primary_id){
        $result = $this->select($this->table, 'status', "$this->pk  = '$primary_id'");
        $row = $result->fetch_assoc();
        return $row['status'];
    }

    public function remove_detail()
    {
        $form = array(
            'quantity_return' => 0,
        );
        $ids = implode(",", $this->inputs['ids']);
        return $this->update($this->table_detail, $form, "$this->pk2 IN($ids)");
    }

    private function delete_sales_details()
    {
        $query = "CREATE TRIGGER `delete_sales_details` AFTER DELETE ON `tbl_sales` FOR EACH ROW DELETE FROM tbl_sales_details WHERE sales_id = OLD.sales_id";
    }

    private function finish_transaction()
    {
        $query = "CREATE TRIGGER `finish_transaction` AFTER UPDATE ON `tbl_sales` FOR EACH ROW UPDATE tbl_product_transactions SET status = IF (NEW.status = 'F', 1, 0) WHERE header_id = NEW.sales_id AND module = 'SLS'";
    }

    private function add_transaction_in()
    {
        $query = "CREATE TRIGGER `add_transaction_in` AFTER INSERT ON `tbl_sales_details` FOR EACH ROW INSERT INTO tbl_product_transactions (product_id,quantity,cost,price,header_id,detail_id,module,type) VALUES (NEW.product_id,NEW.quantity,NEW.cost,NEW.price,NEW.sales_id,NEW.sales_detail_id,'SLS','OUT')";
    }
}
