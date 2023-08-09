<?php

class StockReleasal extends Connection
{
    public $table = "tbl_sales";

    public function view()
    {
        $customer_id = $this->inputs['customer_id'];
        $product_id = $this->inputs['product_id'];
        $start_date = $this->inputs['start_date'];
        $end_date = $this->inputs['end_date'];

        if ($customer_id == "-1") {
            $cust_param = "";
        } else {
            $cust_param = "AND c.customer_id='$customer_id'";
        }

        if ($product_id == "-1") {
            $prod_param = "";
        } else {
            $prod_param = "AND d.product_id='$product_id'";
        }

        $Products = new Products();
        $StockWithdrawal = new StockWithdrawal();

        $data = "";
        $counter = 0;
        $data_ = "";
        $fetch_customer = $this->select("tbl_customers as c, tbl_sales as s","c.customer_id, c.customer_name","c.customer_id=s.customer_id AND s.withdrawal_status=1 AND (s.sales_date >= '$start_date' AND s.sales_date <= '$end_date') AND s.status='F' $cust_param GROUP BY c.customer_id");
        while ($cRow = $fetch_customer->fetch_array()) {
            $result = $this->select("tbl_sales as h, tbl_sales_details as d", "*, sum(d.quantity) as total_qty", "h.sales_id=d.sales_id AND h.withdrawal_status=1 AND h.status='F' AND h.customer_id='$cRow[customer_id]' AND (h.sales_date >= '$start_date' AND h.sales_date <= '$end_date') $prod_param GROUP BY d.sales_detail_id");

            if ($result->num_rows > 0) {
                $counter += 1;
                $table_header = '<table class="table" style="margin-bottom: 50px;"><thead><tr><th colspan="7" style="background: #607d8b;color: #fff;">Customer: '.$cRow["customer_name"].'</th></tr><tr><th>DATE</th><th>REFERENCE #</th><th>PRODUCT</th><th style="text-align:right;">TOTAL QTY</th><th style="text-align:right;">REMAINING QTY</th></tr></thead><tbody>';
                $tbl_counter = 0;
                while ($row = $result->fetch_assoc()) {
                    $remaining_qty = $StockWithdrawal->remaining_qty($row['sales_detail_id']);
                    if ($remaining_qty > 0) {
                        $data_ .= "<tr>";
                        $data_ .= "<td>" . date('M d,Y', strtotime($row['sales_date'])) . "</td>";
                        $data_ .= "<td>" . $row['reference_number'] . "</td>";
                        // $data .= "<td>" . $paid_status . "</td>";
                        // $data .= "<td>" . $bal ."</td>";
                        $data_ .= "<td>" . $Products->name($row['product_id']) . "</td>";
                        $data_ .= "<td style='text-align: right;'>" . number_format($row['total_qty'], 2) . "</td>";
                        $data_ .= "<td style='text-align: right;'>" . number_format($remaining_qty, 2) . "</td>";
                        $data_ .= "</tr>";
                        $tbl_counter += 1;
                    }else{
                        $tbl_counter += 0;
                    }
                }

                

                if($tbl_counter > 0){
                    $data .= $table_header.$data_."</tbody></table>";
                }else{
                    $data .= "";
                }
            }
        }

        echo $counter > 0 ? $data : "<hr><center style='color: #757575;'><h3>No details found.</h3></center><hr>";
    }

    public function per_item()
    {
        $product_id = $this->inputs['product_id'];
        $start_date = $this->inputs['start_date'];
        $end_date = $this->inputs['end_date'];

        if ($product_id == "-1") {
            $prod_param = "";
        } else {
            $prod_param = "AND d.product_id='$product_id'";
        }
        $StockWithdrawal = new StockWithdrawal;
        $Inventory = new InventoryReport();
        $result = $this->select("tbl_products","*",$prod_param);
        while ($row = $result->fetch_assoc()) {
            
            // $for_withdrawal = 0;
            // $fetch_claim_slips = $this->select("tbl_claim_slips", "*", "checked_by=0 and status='F'");
            // while($cs_row = $fetch_claim_slips->fetch_array()){
            //     // withdrawal_id
            //     // sales_id
            //     $fetch_stock_withdrawal = $this->select("tbl_stock_withdrawal_details", "sum(qty)", "withdrawal_id='$cs_row[withdrawal_id]' and product_id='$row[product_id]'");
            //     $sw_qty = $fetch_stock_withdrawal->fetch_array();
            //     $for_withdrawal += $sw_qty[0];
            // }

            $fetchCS = $this->select("tbl_sales as h, tbl_sales_details as d", "*, sum(d.quantity) as total_qty", "h.sales_id=d.sales_id AND h.withdrawal_status=1 AND h.status='F' AND d.product_id='$row[product_id]' AND (h.sales_date >= '$start_date' AND h.sales_date <= '$end_date') GROUP BY d.sales_detail_id");
            $for_withdrawal = 0;
            while ($row = $result->fetch_assoc()) {
                $for_withdrawal += $StockWithdrawal->remaining_qty($row['sales_detail_id']);
            }
            
            $on_hand = $Inventory->balance($row['product_id']);

            $row['item'] = $row['product_name'];
            $row['on_hand'] = number_format(($on_hand+$for_withdrawal),2);
            $row['for_withdrawal'] = number_format($for_withdrawal,2);
            $row['available'] = number_format(($on_hand),2);
                
            $rows[] = $row;
        }

        return $rows;
    }
}
