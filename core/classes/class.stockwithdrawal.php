<?php
class StockWithdrawal extends Connection
{
    private $table = 'tbl_stock_withdrawal';
    public $pk = 'withdrawal_id';
    public $name = 'reference_number';

    private $table_detail = 'tbl_stock_withdrawal_details';
    public $pk2 = 'sw_detail_id';
    public $fk_det = 'sales_id';

    public $module_name = "Stock Withdrawal";
    public $inputs = [];
    public $searchable = ['reference_number', 'remarks'];
    public $uri = "stock-withdrawal";
    public function add()
    {
        $sales_id = $this->inputs['sales_id'];
        $ref_number = $this->clean($this->inputs[$this->name]);
        $form = array(
            $this->name => $ref_number,
            'sales_id' => $sales_id,
            'remarks' => $this->inputs['remarks'],
            'withdrawal_date' => $this->inputs['withdrawal_date'],
            'encoded_by' => isset($this->inputs['encoded_by']) ? $this->inputs['encoded_by'] :  $_SESSION['user']['id']
        );
        $withdrawal_id = $this->insertIfNotExist($this->table, $form, "reference_number = '$ref_number'", 'Y');

        $form_detail = array(
            'withdrawal_id' => "$withdrawal_id AS withdrawal_id",
            'sales_detail_id' => 'sales_detail_id',
            'product_id' => 'product_id',
            'qty' => 0,
        );

        $this->insert_select($this->table_detail, 'tbl_sales_details', $form_detail, "sales_id = '$sales_id'");
        return $withdrawal_id;
    }

    public function save_detail()
    {
        $r_qty = $this->inputs['r_qty'];
        $r_id = $this->inputs['r_id'];

        array_map(function ($id, $qty) {
            $form = array(
                'qty' => $qty,
                'status' => 'S'
            );
            return $this->update($this->table_detail, $form, "$this->pk2 = '$id'");
        }, $r_id, $r_qty);
    }

    public function edit()
    {
        $form = array(
            'sales_id' => $this->inputs['sales_id'],
            'remarks' => $this->inputs['remarks'],
            'withdrawal_date' => $this->inputs['withdrawal_date'],
            'encoded_by' => $_SESSION['user']['id']
        );
        return $this->updateIfNotExist($this->table, $form);
    }

    public function view()
    {
        $Customers = new Customers();
        $Sales = new Sales();
        $primary_id = $this->inputs['id'];
        $result = $this->select($this->table, "*", "$this->pk = '$primary_id'");
        $row = $result->fetch_assoc();
        $row['customer'] = $Customers->name($Sales->dataRow($row['sales_id'], "customer_id"));
        $row['sales_reference_number'] = $Sales->dataRow($row['sales_id'], "reference_number");
        return $row;
    }

    public function show_detail()
    {
        $Products = new Products();
        $Sales = new Sales();
        $param = isset($this->inputs['param']) ? $this->inputs['param'] : null;
        $rows = array();
        $result = $this->select($this->table_detail, '*', $param);
        while ($row = $result->fetch_assoc()) {
            $row['product'] = Products::name($row['product_id']);
            $row['sales_qty'] = $Sales->detailsRow($row['sales_detail_id'], "quantity");
            $row['remaining_qty'] = $this->remaining_qty($row['sales_detail_id']);
            $rows[] = $row;
        }
        return $rows;
    }

    public function remaining_qty($primary_id)
    {
        $sum_sales = $this->select('tbl_sales_details', "sum(quantity)", "sales_detail_id='$primary_id'");
        $total_sales = $sum_sales->fetch_array();

        $sum_sales_return = $this->select('tbl_sales_return_details as d, tbl_sales_return as h', "sum(d.quantity_return)", "d.sales_detail_id='$primary_id' AND h.status='F' AND h.sales_return_id=d.sales_return_id");
        $total_sales_return = $sum_sales_return->fetch_array();

        $sum_released = $this->select('tbl_stock_withdrawal_details', "sum(qty)", "status='F' AND sales_detail_id='$primary_id'");
        $total_released = $sum_released->fetch_array();

        $total = ($total_sales[0] - $total_sales_return[0]) - $total_released[0];

        return $total;
    }

    public function show()
    {
        $Customers = new Customers();
        $Sales = new Sales();
        $Users = new Users();
        $param = isset($this->inputs['param']) ? $this->inputs['param'] : null;
        $rows = array();
        $result = $this->select($this->table, '*', $param);
        while ($row = $result->fetch_assoc()) {
            $row['sales_ref'] = $Sales->dataRow($row['sales_id'], "reference_number");
            $row['customer'] = $Customers->name($Sales->dataRow($row['sales_id'], "customer_id"));
            $row['encoded_name'] = $Users->getUser($row['encoded_by']);
            $rows[] = $row;
        }
        return $rows;
    }

    public function finish($id = null)
    {
        if ($id != null) {
            $primary_id = $id;
        } else {
            $primary_id = $this->inputs['id'];
        }

        $result = $this->select($this->table_detail, '*', "withdrawal_id='$primary_id'");
        $total = 0;
        $counter = 0;
        while ($row = $result->fetch_assoc()) {
            if ($id != null) {
                $sub_total = $this->remaining_qty($row['sales_detail_id']);
            } else {
                $sub_total = $this->remaining_qty($row['sales_detail_id']) - $row['qty'];
            }
            $total += $sub_total;

            $counter += $row['qty'];
        }

        $sales_id = $this->sales_id($primary_id);

        if ($counter <= 0) {
            return -1;
        } else {

            $ClaimSlip = new ClaimSlip;
            $ClaimSlip->inputs['sales_id'] = $sales_id;
            $ClaimSlip->finish();

            if ($total <= 0) {
                $form_ = array(
                    'withdrawal_status' => 0,
                );
                $this->update("tbl_sales", $form_, "sales_id = '$sales_id'");
            } else {
                $ClaimSlip2 = new ClaimSlip;
                $Sales = new Sales;
                $ClaimSlip2->inputs['reference_number'] = $ClaimSlip2->generate();
                $ClaimSlip2->inputs['sales_id'] = $sales_id;
                $ClaimSlip2->inputs['total_amount'] = $Sales->total($sales_id);
                $ClaimSlip2->add();
            }

            $form = array(
                'status' => 'F',
            );
            $this->update($this->table_detail, $form, "$this->pk = '$primary_id'");
            return $this->update($this->table, $form, "$this->pk = '$primary_id'");
        }
    }

    public function generate()
    {
        // return 'SW-' . date('ymdHis');
        $fetch = $this->select($this->table, "max(withdrawal_id) + 1 as max_id", $this->pk > 0);
        $row = $fetch->fetch_assoc();
        return sprintf("%'.06d", $row['max_id']);
    }

    public function remove()
    {
        $ids = implode(",", $this->inputs['ids']);
        return $this->delete($this->table, "$this->pk IN($ids)");
    }


    public function remove_detail()
    {
        $ids = implode(",", $this->inputs['ids']);
        return $this->delete($this->table_detail, "$this->pk2 IN($ids)");
    }

    public function po_id($primary_id)
    {

        $result = $this->select($this->table, $this->pk, "$this->name = '$primary_id'");
        $row = $result->fetch_assoc();
        return $row[$this->name];
    }

    public function pk_by_name($name = null)
    {
        $name = $name == null ? $this->inputs[$this->name] : $name;
        $result = $this->select($this->table, $this->pk, "$this->name = '$name'");
        $row = $result->fetch_assoc();
        return $row[$this->pk] * 1;
    }

    public function get_row($primary_id, $field)
    {
        $result = $this->select($this->table, $field, "$this->pk = '$primary_id'");
        $row = $result->fetch_assoc();
        return $row[$field];
    }

    public function name($primary_id)
    {
        $result = $this->select($this->table, $this->name, "$this->pk = '$primary_id'");
        $row = $result->fetch_assoc();
        return $row[$this->name];
    }

    public function total($primary_id)
    {
        $result = $this->select($this->table_detail, 'sum(qty*supplier_price)', "$this->pk = '$primary_id'");
        $row = $result->fetch_array();
        return $row[0];
    }

    public function po_balance($primary_id)
    {
        $po_total = $this->total($primary_id);

        $fetch_sp = $this->select('tbl_supplier_payment_details as d, tbl_supplier_payment as h', "sum(amount) as total", "d.po_id = $primary_id AND h.sp_id=d.sp_id AND h.status='F'");
        $paid_total = 0;
        while ($row = $fetch_sp->fetch_assoc()) {
            $paid_total += $row['total'];
        }

        return $po_total - $paid_total;
    }

    public function totalPurchaseDays($days)
    {
        $fetchData = $this->select('tbl_purchase_order_details as d, tbl_purchase_order as h', "sum(supplier_price*qty) as total", "h.po_id = d.po_id AND h.po_date BETWEEN NOW() - INTERVAL $days DAY AND NOW() AND h.status='F'");
        $row = $fetchData->fetch_assoc();

        return $row['total'] == 0 ? 0 : $row['total'];
    }

    public function addStockWithdrawalPOS()
    {
        $Sales = new Sales();
        $ClaimSlip = new ClaimSlip;
        $reference_number = $this->inputs['reference_number'];
        $param = "reference_number='$reference_number'";
        $Sales->inputs['sales_id'] = $Sales->getID($param);
        
        $Sales->inputs['r_qty'] = $this->inputs['r_qty'];
        $Sales->inputs['r_id'] = $this->inputs['r_id'];
        $Sales->inputs['p_id'] = $this->inputs['p_id'];
        $withdrawal_id = $Sales->released();

        $ClaimSlip->inputs['withdrawal_id'] = $withdrawal_id;
        $ClaimSlip->inputs['claim_slip_id'] = $this->inputs['claim_slip_id'];
        return $ClaimSlip->update_claim_slip();
    }

    public function getID($param)
    {
        $fetch = $this->select($this->table, $this->pk, $param);

        if ($fetch->num_rows > 0) {
            $row = $fetch->fetch_array();
            return $row[0];
        } else {
            return null;
        }
    }

    public function sales_id($primary_id)
    {

        $result = $this->select($this->table, "sales_id", "$this->pk = '$primary_id'");
        $row = $result->fetch_assoc();
        return $row['sales_id'];
    }

    private function delete_sales_details()
    {
        $query = "CREATE TRIGGER `delete_po_details` AFTER DELETE ON `tbl_purchase_order` FOR EACH ROW DELETE FROM tbl_purchase_order_details WHERE po_id = OLD.po_id";
    }

    private function finish_transaction()
    {
        $query = "CREATE TRIGGER `finish_transaction` AFTER UPDATE ON `tbl_sales` FOR EACH ROW UPDATE tbl_product_transactions SET status = IF (NEW.sales_status = 'F', 1, 0) WHERE header_id = NEW.sales_id AND module = 'SLS'";
    }

    private function add_transaction_in()
    {
        $query = "CREATE TRIGGER `add_transaction_in` AFTER INSERT ON `tbl_sales_details` FOR EACH ROW INSERT INTO tbl_product_transactions (product_id,quantity,cost,price,header_id,detail_id,module,type) VALUES (NEW.product_id,NEW.quantity,NEW.cost,NEW.price,NEW.sales_id,NEW.sales_detail_id,'SLS','OUT')";
    }


    public function getHeader()
    {
        $Customers = new Customers;
        $id = $_POST['id'];
        $result = $this->select($this->table, "*", "$this->pk='$id'");
        $row = $result->fetch_assoc();
        $row['withdrawal_date'] = date("F j, Y", strtotime($row['withdrawal_date']));
        $rows[] = $row;
        return $rows;
    }
    public function getPrintDetails()
    {
        $id = $_POST['id'];
        $Products = new Products;
        $rows = array();
        $result = $this->select($this->table_detail, "*", "$this->pk='$id' AND qty > 0");
        while ($row = $result->fetch_assoc()) {
            $row['product_name'] = $Products->name($row['product_id']);
            $rows[] = $row;
        }
        return $rows;
    }

    public static function search($words, &$rows)
    {
        $self = new self;
        if (count($self->searchable) > 0) {
            $where = implode(" LIKE '%$words%' OR ", $self->searchable) . " LIKE '%$words%'";
            $result = $self->select($self->table, '*', $where);
            while ($row = $result->fetch_assoc()) {
                $names = [];
                foreach ($self->searchable as $f) {
                    $names[] = $row[$f];
                }
                $rows[] = array(
                    'name' => implode(" ", $names),
                    'module' => $self->module_name,
                    'slug' => $self->uri . "?id=" . $row[$self->pk]
                );
            }
        }
    }
}
