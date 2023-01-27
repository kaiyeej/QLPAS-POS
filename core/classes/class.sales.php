<?php
class Sales extends Connection
{
    private $table = 'tbl_sales';
    public $pk = 'sales_id';
    public $name = 'reference_number';

    private $table_detail = 'tbl_sales_details';
    public $pk2 = 'sales_detail_id';
    public $fk_det = 'product_id';

    public $module = 'SLS';

    public function add()
    {
        $for_pick_up = isset($this->inputs['for_pick_up']) ? $this->inputs['for_pick_up'] : 0;
        $form = array(
            $this->name     => $this->clean($this->inputs[$this->name]),
            'customer_id'   => $this->inputs['customer_id'],
            'sales_type'    => $this->inputs['sales_type'],
            'discount_id'   => $this->inputs['discount_id'],
            'remarks'       => $this->inputs['remarks'],
            'for_pick_up'   => $for_pick_up,
            'withdrawal_status'   => $for_pick_up,
            'paid_status'   => ($this->inputs['sales_type'] == "C" ? 1 : 0),
            'sales_date'    => $this->inputs['sales_date'],
            'encoded_by' => isset($this->inputs['encoded_by']) ? $this->inputs['encoded_by'] :  $_SESSION['user']['id']
        );
        return $this->insertIfNotExist($this->table, $form, '', 'Y');
    }

    public function edit()
    {
        $for_pick_up = isset($this->inputs['for_pick_up']) ? 1 : 0;
        $form = array(
            'customer_id'   => $this->inputs['customer_id'],
            'sales_type'    => $this->inputs['sales_type'],
            'remarks'       => $this->inputs['remarks'],
            'for_pick_up'   => $for_pick_up,
            'withdrawal_status'   => $for_pick_up,
            'paid_status'   => ($this->inputs['sales_type'] == "C" ? 1 : 0),
            'sales_date'    => $this->inputs['sales_date'],
            'encoded_by' => $_SESSION['user']['id']
        );
        return $this->updateIfNotExist($this->table, $form);
    }

    public function show()
    {
        $Customers = new Customers;
        $Users = new Users;
        $param = isset($this->inputs['param']) ? $this->inputs['param'] : null;
        $rows = array();
        $result = $this->select($this->table, '*', $param);
        while ($row = $result->fetch_assoc()) {
            $customer_name = $row['customer_id'] > 0 ? $Customers->name($row['customer_id']) : 'Walk-in';
            $row['customer'] = $customer_name;
            $row['customer_id'] = $row['customer_id'];
            $total = $this->total($row['sales_id']);
            $row['total'] = number_format($total, 2);
            $row['total_nonformat'] = $total;
            $row['inv_ref'] = $row['reference_number'] . " (₱" . number_format($this->dr_balance($row['sales_id']), 2) . ")";
            $row['withdrawal_ref'] = $row['reference_number'] . " (Customer: " . $customer_name . ")";
            $row['encoded_name'] = $Users->getUser($row['encoded_by']);
            $rows[] = $row;
        }
        return $rows;
    }

    public function view()
    {
        $primary_id = $this->inputs['id'];
        $result = $this->select($this->table, "*", "$this->pk = '$primary_id'");
        if ($result->num_rows > 0) {
            $Customers = new Customers;
            $row = $result->fetch_assoc();
            $row['customer_name'] = $row['customer_id'] > 0 ? $Customers->name($row['customer_id']) : 'Walk-in';
            $row['salestype'] = $row['sales_type'] == "C" ? "Cash" : "Charge";
            return $row;
        } else {
            return null;
        }
    }

    public function remove()
    {
        $ids = implode(",", $this->inputs['ids']);
        return $this->delete($this->table, "$this->pk IN($ids)");
    }

    public function generate()
    {
        //return 'DR-' . date('ymdHis');
        $fetch = $this->select($this->table, "max(sales_id) + 1 as max_id", $this->pk > 0);
        $row = $fetch->fetch_assoc();
        return sprintf("%'.06d", $row['max_id']);
    }

    public function finish()
    {
        $primary_id = $this->inputs['id'];
        $form = array(
            'status' => 'F',
        );
        return $this->update($this->table, $form, "$this->pk = '$primary_id'");
    }

    public function pk_by_name($name = null)
    {
        $name = $name == null ? $this->inputs[$this->name] : $name;
        $result = $this->select($this->table, $this->pk, "$this->name = '$name' AND status='F'");
        $row = $result->fetch_assoc();
        return $row[$this->pk] * 1;
    }

    public function pk_name($name, $customer_id)
    {
        $result = $this->select($this->table, $this->pk, "$this->name = '$name' AND status='F' AND customer_id='$customer_id'");
        $row = $result->fetch_assoc();
        return $row[$this->pk] * 1;
    }

    public function name($primary_id)
    {
        $result = $this->select($this->table, $this->name, "$this->pk = '$primary_id'");
        $row = $result->fetch_assoc();
        return $row[$this->name];
    }

    public function dataRow($primary_id, $field)
    {
        $result = $this->select($this->table, $field, "$this->pk = '$primary_id'");
        if($result->num_rows > 0){
            $row = $result->fetch_array();
            return $row[$field];
        }else{
            return "";
        }
    }

    public function detailsRow($primary_id, $field)
    {
        $result = $this->select($this->table_detail, $field, "$this->pk2 = '$primary_id'");
        if($result->num_rows > 0){
            $row = $result->fetch_array();
            return $row[$field];
        }else{
            return "";
        }
    }

    public function add_detail()
    {
        $primary_id = $this->inputs[$this->pk];
        $fk_det     = $this->inputs[$this->fk_det];

        // check inventory here ...
        $Inventory = new InventoryReport();
        $current_balance = $Inventory->balance($this->inputs['product_id']);
        if($current_balance-$this->inputs['quantity'] >= 0){

            $Products = new Products;
            $product_price = $Products->productPrice($fk_det);
            $product_cost = $Products->productCost($fk_det);
            $Discounts = new Discounts();
            $discount_id = $this->dataRow($primary_id, 'discount_id');
            $row_disc = $discount_id > 0 ? $Discounts->manual($discount_id, $fk_det, $this->inputs['quantity'], $product_price) : $Discounts->automatic($fk_det, $product_price, $this->inputs['quantity']);

            $form = array(
                $this->pk       => $this->inputs[$this->pk],
                $this->fk_det   => $fk_det,
                'quantity'      => $this->inputs['quantity'],
                'price'         => $product_price,
                'cost'          => $product_cost,
                'discount'      => $row_disc['discount_amount'],
                'discount_id'   => $row_disc['discount_id'],
            );
            return $this->insert($this->table_detail, $form);
        }else{
            return -3; //insufficient qty
        }
    }

    public function show_detail()
    {
        $param = isset($this->inputs['param']) ? $this->inputs['param'] : null;
        $rows = array();
        $count = 1;
        $result = $this->select($this->table_detail, '*', $param);
        while ($row = $result->fetch_assoc()) {
            $row['product'] = Products::name($row['product_id']);
            $amount = ($row['quantity'] * $row['price']) - $row['discount'];
            $row['amount'] = number_format($amount, 2);
            $row['pos_qty'] = number_format($row['quantity']);
            $row['pos_price'] = "@" . $row['price'];
            $row['count'] = $count++;
            $rows[] = $row;
        }
        return $rows;
    }

    public function remove_detail()
    {
        $ids = implode(",", $this->inputs['ids']);
        return $this->delete($this->table_detail, "$this->pk2 IN($ids)");
    }

    public function total($primary_id)
    {
        $result_dr = $this->select($this->table_detail, 'sum((quantity*price)-discount)', "$this->pk = '$primary_id'");
        $total_dr = $result_dr->fetch_array();

        $result_sr = $this->select("tbl_sales_return as sr, tbl_sales_return_details as srd", "SUM((srd.quantity_return*srd.price)-(srd.discount/srd.quantity*srd.quantity_return)) as total", "sr.sales_return_id=srd.sales_return_id AND sr.status='F' AND sr.sales_id='$primary_id'");
        $total_sr = $result_sr->fetch_array();

        return $total_dr[0] - $total_sr[0];
    }

    public function cancel_sales_pos()
    {

        $access_code = $this->inputs['access_code'];

        $Settings = new Settings();
        $setting_row = $Settings->view();
        if ($setting_row['module_cancel'] == $access_code) {
            $reference_number = $this->inputs['reference_number'];
            $param = "reference_number = '$reference_number'";
            $primary_id = $this->getID($param);
            $form = array(
                'status' => 'C'
            );

            return $this->update($this->table, $form, "$this->pk = '$primary_id'");
        } else {
            return -2;
        }
    }

    public function remove_detail_pos()
    {
        $access_code = $this->inputs['access_code'];

        $Settings = new Settings();
        $setting_row = $Settings->view();
        if ($setting_row['module_delete'] == $access_code) {
            $ids = implode(",", $this->inputs['ids']);
            return $this->delete($this->table_detail, "$this->pk2 IN($ids)");
        } else {
            return -2;
        }
    }

    public function save_sales()
    {
        $reference_number = $this->inputs['reference_number'];
        $param = "reference_number = '$reference_number'";
        $primary_id = $this->getID($param);
        $form = array(
            'status' => 'P'
        );

        return $this->update($this->table, $form, "$this->pk = '$primary_id'");
    }

    public function edit_discount()
    {
        $reference_number = $this->inputs['reference_number'];
        $discount_id = $this->inputs['discount_id'];
        $access_code = $this->inputs['access_code'];

        $Settings = new Settings();
        $setting_row = $Settings->view();
        if ($setting_row['module_discount'] == $access_code) {
            $param = "reference_number = '$reference_number'";
            $sales_id = $this->getID($param);

            $Discounts = new Discounts();
            $Discounts->inputs['id'] = $discount_id;
            $row = $Discounts->view();

            if ($row['coverage_type'] == 'A') {
                $result = $this->select($this->table_detail, "sales_detail_id,quantity,price", "sales_id = '$sales_id'");
            } else {
                $result = $this->table("$this->table_detail AS det")
                    ->join("tbl_discount_details AS d", "det.product_id", "=", "d.product_id")
                    ->selectRaw('sales_detail_id', 'quantity', 'price')
                    ->where('d.discount_id', $discount_id)
                    ->where('sales_id', $sales_id)
                    ->get();
            }

            $this->update($this->table_detail, ['discount_id' => 0, 'discount' => 0], "sales_id = '$sales_id'");
            while ($row_dr = $result->fetch_assoc()) {
                $qty = $row_dr['quantity'];
                $price = $row_dr['price'];
                $sales_detail_id = $row_dr['sales_detail_id'];

                $discount_price = $row['is_percentage'] == 1 ? $price * $row['discount_percent'] / 100 : $row['discount_amount'];
                $discount_amount = $discount_price * $qty;
                $this->update($this->table_detail, ['discount_id' => $discount_id, 'discount' => $discount_amount], "sales_detail_id = '$sales_detail_id'");
            }
            $this->update($this->table, ['discount_id' => $discount_id], "sales_id = '$sales_id'");
            return 1;
        } else {
            return -2;
        }
    }

    public function edit_line_discount()
    {
        $sales_detail_id = $this->inputs['sales_detail_id'];
        $line_discount_type = $this->inputs['line_discount_type'];
        $discount = $this->inputs['discount'];
        $access_code = $this->inputs['access_code'];

        $Settings = new Settings();
        $setting_row = $Settings->view();
        if ($setting_row['module_discount'] == $access_code) {
            $this->inputs['param'] = "sales_detail_id='$sales_detail_id'";
            $quantity = $this->detailsRow($sales_detail_id, 'quantity');
            if ($line_discount_type == 1) {
                $discount = ($quantity * $discount);
            } else {
                $discount = $discount;
            }

            $form = array(
                'discount' => $discount
            );

            return $this->update($this->table_detail, $form, " $this->pk2 = '$sales_detail_id'");
        } else {
            return -2; // access denied
        }
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

    public function edit_detail()
    {

        $sales_detail_id = $this->inputs['sales_detail_id'];

        $product_id = $this->detailsRow($sales_detail_id, 'product_id');
        $sales_id = $this->detailsRow($sales_detail_id, 'sales_id');
        $discount_id = $this->dataRow($sales_id, 'discount_id');

        $Products = new Products;
        $product_price = $Products->productPrice($product_id);

        $Discounts = new Discounts();
        $row_disc = $discount_id > 0 ? $Discounts->manual($discount_id, $product_id, $this->inputs['quantity'], $product_price) : $Discounts->automatic($product_id, $product_price, $this->inputs['quantity']);


        $row_disc = $Discounts->automatic($product_id, $product_price, $this->inputs['quantity']);

        $form = array(
            'quantity'      => $this->inputs['quantity'],
            'discount'      => $row_disc['discount_amount'],
            'discount_id'   => $row_disc['discount_id'],
        );
        return $this->update($this->table_detail, $form, " $this->pk2 = '$sales_detail_id'");
    }

    public function addSalesPOS()
    {

        if ($this->inputs['product_id'] == "" || $this->inputs['product_id'] <= 0) {
            $Products = new Products;
            $this->inputs['product_id'] = $Products->productID($this->inputs['product_barcode']);
        }

        if ($this->inputs['product_id'] > 0) {

            // check inventory here ...
            $Inventory = new InventoryReport();
            $current_balance = $Inventory->balance($this->inputs['product_id']);
            if($current_balance-$this->inputs['quantity'] >= 0){

                $reference_number = $this->inputs['reference_number'];
                $param = "reference_number = '$reference_number'";
                $this->inputs['sales_date'] = $this->getCurrentDate();
                $this->inputs['discount_id'] = 0;
                $sales_id = $this->add();

                if ($sales_id == -2) {
                    $sales_id = $this->getID($param);
                }

                $this->inputs[$this->pk] = $sales_id;

                //$res = $this->add_detail();
                $primary_id = $sales_id;
                $fk_det     = $this->inputs[$this->fk_det];

                $Products = new Products;
                $product_price = $Products->productPrice($fk_det);
                $product_cost = $Products->productCost($fk_det);

                $Discounts = new Discounts();
                $row_disc = $Discounts->automatic($fk_det, $product_price, $this->inputs['quantity']);

                $form = array(
                    $this->pk       => $this->inputs[$this->pk],
                    $this->fk_det   => $fk_det,
                    'quantity'      => $this->inputs['quantity'],
                    'price'         => $product_price,
                    'cost'          => $product_cost,
                    'discount'      => $row_disc['discount_amount'],
                    'discount_id'   => $row_disc['discount_id'],
                );

                //return $this->insert($this->table_detail, $form);
                $res = $this->insertIfNotExist($this->table_detail, $form, "sales_id='$primary_id' AND product_id='$fk_det'", 'Y');
                if ($res == -2) {
                    $qty = $this->inputs['quantity'];
                    $this->inputs['param'] = "sales_id='$primary_id' AND product_id='$fk_det' ";
                    $detail_row = $this->show_detail();

                    if (sizeof($detail_row) > 0) {
                        $this->inputs['quantity'] = $detail_row[0]['quantity'] + $qty;
                        $this->inputs['sales_detail_id'] = $detail_row[0]['sales_detail_id'];
                        $this->edit_detail();
                    }
                }

                return 1;

            }else{
                return -3; //insufficient qty
            }
            
            
        } else {
            return "Cannot find item. Please try again.";
        }
    }

    public function getDetailsPOS()
    {
        //$row = $this->show();
        //$sales_id = $row[0]['sales_id'];
        $reference_number = $this->inputs['reference_number'];
        $param = "reference_number = '$reference_number'";
        $sales_id = $this->getID($param);
        $this->inputs['param'] = "sales_id = '$sales_id' ORDER BY date_added DESC";
        return $this->show_detail();
    }

    public function get_withdrawal_items_pos()
    {
        $reference_number = $this->inputs['reference_number'];
        $param = "reference_number='$reference_number'";
        $sales_id = $this->getID($param);

        $Withdrawal = new StockWithdrawal();


        $rows = array();
        $count = 1;
        $result = $this->select($this->table_detail, '*', "sales_id = '$sales_id' ORDER BY date_added DESC");
        while ($row = $result->fetch_assoc()) {
            $row['product_id'] = $row['product_id'];
            $row['product'] = Products::name($row['product_id']);
            $row['pos_qty'] = number_format($row['quantity']);
            $row['remaining_qty'] = $Withdrawal->remaining_qty($row['sales_detail_id']);
            //$amount = ($row['quantity'] * $row['price']) - $row['discount'];
            //$row['amount'] = number_format($amount, 2);
            //$row['pos_price'] = "@" . $row['price'];
            //$row['count'] = $count++;
            $rows[] = $row;
        }
        return $rows;
    }

    public function change_sales_type()
    {

        $access_code = $this->inputs['access_code'];
        $action = $this->inputs['action'];

        $Settings = new Settings();
        $setting_row = $Settings->view();
        if ($setting_row['module_change_payment_type'] == $access_code || $action == "N") {
            $reference_number = $this->inputs['reference_number'];
            $param = "reference_number = '$reference_number'";
            $sales_id = $this->getID($param);

            $form = array(
                "sales_type" => $this->inputs['sales_type'],
                "paid_status" => $this->inputs['sales_type'] == "C" ? 1 : 0,
                "customer_id" => $this->inputs['customer_id']
            );

            return $this->update($this->table, $form, "sales_id = '$sales_id'");
        }else{
            return -2;
        }
    }

    public function finishSalesPOS()
    {
        $CustomerPayment = new CustomerPayment;
        $reference_number = $this->inputs['reference_number'];
        $customer_payment_amount = $this->inputs['customer_payment_amount'];
        $sales_type = $this->inputs['sales_type'];

        $param = "reference_number='$reference_number'";

        $primary_id = $this->getID($param);

        // add customer payment if charge
        if ($sales_type == "H" && $customer_payment_amount > 0) {
            $customer_payment_number = $CustomerPayment->generate();
            $CustomerPayment->inputs['reference_number'] = $customer_payment_number;
            $CustomerPayment->inputs['customer_id'] = $this->inputs['customer_id'];
            $CustomerPayment->inputs['payment_type'] = 'C';
            $CustomerPayment->inputs['payment_date'] = $this->getCurrentDate();
            $CustomerPayment->inputs['remarks'] = "walk-in";
            $CustomerPayment->inputs['payment_option_id'] = 0;
            $CustomerPayment->inputs['check_date'] = "";
            $CustomerPayment->inputs['check_number'] = "";
            $CustomerPayment->inputs['check_bank'] = 0;
            $CustomerPayment->inputs['encoded_by'] = $this->inputs['encoded_by'];
            $customer_payment_id = $CustomerPayment->add();

            $CustomerPayment->inputs['cp_id'] = $customer_payment_id;
            $CustomerPayment->inputs['ref_id'] = "DR-".$primary_id;
            $CustomerPayment->inputs['amount'] = $customer_payment_amount;

            $CustomerPayment->add_detail();
        }

        $form = array(
            'status' => 'F',
            'for_pick_up' => $this->inputs['for_pickup'],
            'withdrawal_status' => $this->inputs['for_pickup'],
            'encoded_by' => $this->inputs['encoded_by']
        );
        $res = $this->update($this->table, $form, "$this->pk = '$primary_id'");


        if ($res == 1) {
            // finish all related customer payment
            return $CustomerPayment->finishCustomerPaymentOfDRPOS($primary_id, $this->inputs['customer_id']);
        } else {
            return -1;
        }
    }

    public function sales_summary()
    {
        $user_id = $this->inputs['user_id'];
        $rows = array();
        $result = $this->select($this->table, $this->pk, "encoded_by='$user_id' AND sales_summary_id=0 AND status='F' ORDER BY date_added ASC ");
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_array()) {
                $rows[] = $row[0];
            }

            if (sizeof($rows) > 0) {
                $count = 0;
                $fetch = $this->select($this->table_detail, "*", "sales_id IN(" . implode(',', $rows) . ") ");
                while ($sales_row = $fetch->fetch_assoc()) {
                    $sales_row['product'] = Products::name($sales_row['product_id']);
                    $amount = ($sales_row['quantity'] * $sales_row['price']) - $sales_row['discount'];
                    $sales_row['amount'] = number_format($amount, 2);
                    $sales_row['discount'] = number_format($sales_row['discount'], 2);
                    $sales_row['pos_qty'] = number_format($sales_row['quantity']);
                    $sales_row['pos_price'] = "@" . $sales_row['price'];
                    $sales_row['count'] = $count++;
                    $sales_rows[] = $sales_row;
                }
                return $sales_rows;
            }
        } else {
            return [];
        }
    }

    public function summary_of_charge_sales(){
        $user_id = $this->inputs['user_id'];
        $rows = array();
        $result = $this->select($this->table, $this->pk, "encoded_by='$user_id' AND sales_summary_id=0 AND status='F' AND sales_type='H' ");
        if ($result->num_rows > 0) {
            $rows = $result->fetch_array();

            if (sizeof($rows) > 0) {
                $count = 0;
                $fetch = $this->select($this->table_detail, "sum((quantity*price)-discount) as total_charge_sales", "sales_id IN(" . implode(',', $rows) . ") ");
                $sales_row = $fetch->fetch_assoc();

                $fetch_payment = $this->select("tbl_customer_payment_details", "sum(amount) as total_payment", "ref_id IN(" . implode(',', $rows) . ") AND type='DR' ");
                $payment_row = $fetch_payment->fetch_assoc();
                
                $sales_rows['total_charge_sales'] = $sales_row['total_charge_sales']*1;
                $sales_rows['total_payment'] = $payment_row['total_payment']*1;
                return $sales_rows;
            }
        } else {
            return 0;
        }
    }

    public function update_review_sales_summary()
    {
        $encoded_by = $this->inputs['encoded_by'];
        $form = array(
            'sales_summary_id' => $this->inputs['sales_summary_id']
        );
        return $this->update($this->table, $form, "sales_summary_id=0 AND encoded_by='$encoded_by' and (status='F' or status='C') ");
    }

    public function released()
    {
        $sales_id = $this->inputs['sales_id'];
        $ref_number = 'SW-' . date('YmdHis');
        $r_qty = $this->inputs['r_qty'];
        $r_id = $this->inputs['r_id'];
        $p_id = $this->inputs['p_id'];

        $form = array(
            $this->name => $ref_number,
            'sales_id' => $sales_id,
            'withdrawal_date' => $this->sales_date($sales_id),
            'status' => 'S',
            'encoded_by' => isset($this->inputs['encoded_by']) ? $this->inputs['encoded_by'] :  $_SESSION['user']['id']
        );

        $withdrawal_id = $this->insert('tbl_stock_withdrawal', $form, 'Y');

        array_map(function ($id, $qty, $product_id) use ($withdrawal_id) {
            //if ($qty > 0) {
                $form_details = array(
                    'withdrawal_id' => $withdrawal_id,
                    'product_id' => $product_id,
                    'qty' => $qty,
                    'sales_detail_id' => $id,
                    'status' => 'F'
                );
                $this->insert('tbl_stock_withdrawal_details', $form_details);
           // }
        }, $r_id, $r_qty, $p_id);

        $StockWithdrawal = new StockWithdrawal();
        $StockWithdrawal->finish($withdrawal_id);
    }

    public function sales_date($primary_id)
    {
        $result = $this->select($this->table, 'sales_date', "$this->pk = '$primary_id'");
        $row = $result->fetch_assoc();
        return $row['sales_date'];
    }


    public function dr_balance($primary_id)
    {
        $dr_total = $this->total($primary_id);

        $fetch_cp = $this->select('tbl_customer_payment_details as d, tbl_customer_payment as h', "sum(amount) as total", "d.ref_id = $primary_id AND h.cp_id=d.cp_id AND h.status='F'");
        $paid_total = 0;
        while ($row = $fetch_cp->fetch_assoc()) {
            $paid_total += $row['total'];
        }

        return $dr_total - $paid_total;
    }

    public function totalSalesDays($days)
    {
        $fetchData = $this->select('tbl_sales_details as d, tbl_sales as h', "sum((quantity*price)-discount) as total", "h.sales_id = d.sales_id AND h.sales_date BETWEEN NOW() - INTERVAL $days DAY AND NOW() AND h.status='F'");
        $row = $fetchData->fetch_assoc();

        $result_sr = $this->select("tbl_sales_return as sr, tbl_sales_return_details as srd", "SUM((srd.quantity_return*srd.price)-(srd.discount/srd.quantity*srd.quantity_return)) as total", "sr.sales_return_id=srd.sales_return_id AND sr.status='F' AND sr.return_date BETWEEN NOW() - INTERVAL $days DAY AND NOW()");
        $total_sr = $result_sr->fetch_array();
        

        return ($row['total']-$total_sr['total']) == 0 ? 0 : ($row['total']-$total_sr['total']);
    }

    public function posPrintReceipt()
    {
        $print_type = $this->inputs['print_type'];
    
        $reference_number = $this->inputs['reference_number'];
        $sales_id = $this->getID("reference_number = '$reference_number'");
    
        $this->inputs['id'] = $sales_id;
        $header_data = $this->view();
    
        $Users = new Users();
        $Settings = new Settings();
        $settings_data = $Settings->view();
    
        $response = [
            'company_data' => $settings_data['company_name'] . "<br>" . $settings_data['company_address'],
            'print_header' => $settings_data['print_header'],
            'print_footer' => $settings_data['print_footer'],
            'sales_date' => $header_data['sales_date'],
            'current_time' => date('m/d/Y h:i:s A'),
            'cashier' => $Users->getUser($header_data['encoded_by']),
            'customer' => $header_data['customer_name'],
        ];
    
        if ($print_type == 'sales') {
    
            $this->inputs['param'] = "sales_id = '$sales_id'";
            $details = $this->show_detail();
    
            $total_qty = 0;
            $total_amt = 0;
            $items = [];
            foreach ($details as $row) {
                $quantity = $row['quantity'] * 1;
                $amount = ($quantity * $row['price']) - $row['discount'];
                $discounted_price = $amount / $quantity;
                $items[] = [
                    'quantity' => (float) $quantity,
                    'description' => $quantity . "&nbsp;" . $row['pos_price'],
                    'discounted_price' => (float) $discounted_price,
                    'discount' => (float) $row['discount'],
                    'amount' => number_format($amount, 2)
                ];
    
    
                $items[] = [
                    'quantity' => '',
                    'description' => substr(strtoupper($row['product']), 0, 15),
                    'discount' => '',
                    'amount' => ''
                ];
                $total_qty += $row['quantity'];
                $total_amt += $amount;
            }
    
            $CustomerPayment = new CustomerPayment();
    
            $response['reference_number'] = $header_data['reference_number'];
            $response['items'] = $items;
            $response['payments'] = $CustomerPayment->getPaymentByRef($sales_id);
            $response['total_qty'] = $total_qty;
            $response['total_amt'] = number_format($total_amt, 2);
        } else {
    
            $StockWithdrawal = new StockWithdrawal();
            $withdrawal_id = $StockWithdrawal->getID("sales_id = '$sales_id' ORDER BY date_added DESC LIMIT 1");
    
            $StockWithdrawal->inputs['param'] = "withdrawal_id = '$withdrawal_id'";
            $items = $StockWithdrawal->show_detail();
    
            $response['reference_number'] = $StockWithdrawal->name($withdrawal_id);
            $response['items'] = $items;
            $response['withdrawal_date'] = date("Y-m-d H:i:s");
        }
    
        return $response;
    }

    public function schema()
    {
        if (DEVELOPMENT) {
            $default['date_added'] = $this->metadata('date_added', 'datetime', '', 'NOT NULL', 'CURRENT_TIMESTAMP');
            $default['date_last_modified'] = $this->metadata('date_last_modified', 'datetime', '', 'NOT NULL', '', 'ON UPDATE CURRENT_TIMESTAMP');
            $default['encoded_by'] = $this->metadata('encoded_by', 'int', 11);


            // TABLE HEADER
            $tables[] = array(
                'name'      => $this->table,
                'primary'   => $this->pk,
                'fields' => array(
                    $this->metadata($this->pk, 'int', 11, 'NOT NULL', '', 'AUTO_INCREMENT'),
                    $this->metadata($this->name, 'varchar', 75),
                    $this->metadata('sales_invoice', 'int', 11),
                    $this->metadata('customer_id', 'int', 11),
                    $this->metadata('for_pick_up', 'int', 1, 'NOT NULL', '0', '', "'1 = Yes; 0 = No'"),
                    $this->metadata('sales_type', 'varchar', 1, 'NOT NULL', '', '', "'C=Cash;H=Charge'"),
                    $this->metadata('discount_id', 'int', 11, 'NOT NULL', '0', '', "'Manual Discounts'"),
                    $this->metadata('remarks', 'varchar', 255),
                    $this->metadata('sales_date', 'date'),
                    $this->metadata('paid_status', 'varchar', 1, 'NOT NULL', '0', '', "'1 = Paid; 0 = Unpaid'"),
                    $this->metadata('withdrawal_status', 'int', 1, '', '0', '', "'0 = Fully served ; 1 = Pending'"),
                    $this->metadata('status', 'varchar', 1, 'NOT NULL', "'S'", '', "'S = Saved; F = Finish'"),
                    $default['encoded_by'],
                    $default['date_added'],
                    $default['date_last_modified']
                )
            );

            // TABLE DETAILS
            $tables[] = array(
                'name'      => $this->table_detail,
                'primary'   => $this->pk2,
                'fields' => array(
                    $this->metadata($this->pk2, 'int', 11, 'NOT NULL', '', 'AUTO_INCREMENT'),
                    $this->metadata($this->pk, 'int', 11),
                    $this->metadata($this->fk_det, 'int', 11),
                    $this->metadata('discount_id', 'int', 11, 'NOT NULL', '0', '', "'Automatic Discounts'"),
                    $this->metadata('discount', 'decimal', '12,2'),
                    $this->metadata('quantity', 'decimal', '12,2'),
                    $this->metadata('cost', 'decimal', '12,2'),
                    $this->metadata('price', 'decimal', '12,2'),
                    $default['date_added'],
                    $default['date_last_modified']
                )
            );

            return $this->schemaCreator($tables);
        }
    }

    public function triggers()
    {
        $table_transaction = "tbl_product_transactions";
        // HEADER
        $triggers[] = array(
            'table' => $this->table,
            'name' => 'finish_sales',
            'action_time' => 'AFTER', // ['AFTER','BEFORE']
            'event' => "UPDATE", // ['INSERT','UPDATE', 'DELETE']
            "statement" => "UPDATE $table_transaction SET status = IF (NEW.status = 'F', 1, 0) WHERE header_id = NEW.$this->pk AND module = '$this->module'"
        );

        $triggers[] = array(
            'table' => $this->table,
            'name' => 'delete_sales',
            'action_time' => 'AFTER', // ['AFTER','BEFORE']
            'event' => "DELETE", // ['INSERT','UPDATE', 'DELETE']
            "statement" => "DELETE FROM $this->table_detail WHERE $this->pk = OLD.$this->pk"
        );


        // DETAILS
        $triggers[] = array(
            'table' => $this->table_detail,
            'name' => 'add_sales_detail',
            'action_time' => 'AFTER', // ['AFTER','BEFORE']
            'event' => "INSERT", // ['INSERT','UPDATE', 'DELETE']
            "statement" => "INSERT INTO $table_transaction (product_id,quantity,cost,price,header_id,detail_id,module,type) VALUES (NEW.product_id,NEW.quantity,NEW.cost,NEW.price,NEW.$this->pk,NEW.$this->pk2,'$this->module','OUT')"
        );

        $triggers[] = array(
            'table' => $this->table_detail,
            'name' => 'update_sales_detail',
            'action_time' => 'AFTER', // ['AFTER','BEFORE']
            'event' => "UPDATE", // ['INSERT','UPDATE', 'DELETE']
            "statement" => "UPDATE $table_transaction SET product_id = NEW.product_id,quantity = NEW.quantity,cost=NEW.cost, price = NEW.price WHERE detail_id = OLD.$this->pk2 AND header_id = OLD.$this->pk AND module = '$this->module'"
        );

        $triggers[] = array(
            'table' => $this->table_detail,
            'name' => 'delete_sales_detail',
            'action_time' => 'AFTER', // ['AFTER','BEFORE']
            'event' => "DELETE", // ['INSERT','UPDATE', 'DELETE']
            "statement" => "DELETE FROM $table_transaction WHERE header_id = OLD.$this->pk AND detail_id = OLD.$this->pk2 AND module = '$this->module'"
        );

        return $this->triggerCreator($triggers);
    }

    
    public function getHeader()
    {
        $Customers = new Customers;
        $id = $_POST['id'];
        $result = $this->select($this->table, "*", "$this->pk='$id'");
        $row = $result->fetch_assoc();
        $row['sales_date_mod'] = date("F j, Y", strtotime($row['sales_date']));
        $row['customer_name'] = $Customers->name($row['customer_id']);
        $rows[] = $row;
        return $rows;
    }
    public function getPrintDetails()
    {
        $id = $_POST['id'];
        $Products = new Products;
        $rows = array();
        $result = $this->select($this->table_detail, "*", "$this->pk='$id'");
        while ($row = $result->fetch_assoc()) {
            $amount = ($row['price']-$row['discount'])*$row['quantity'];
            $row['product_name'] = $Products->name($row['product_id']);
            $row['price'] = number_format($row['price'],2);
            $row['amount'] = number_format($amount,2);
            $rows[] = $row;
        }
        return $rows;
    }
}
