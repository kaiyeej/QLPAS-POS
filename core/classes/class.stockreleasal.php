<?php

class StockReleasal extends Connection
{
    public $table = "tbl_sales";

    public function view()
    {
        $customer_id = $this->inputs['customer_id'];
        $product_id = $this->inputs['product_id'];

        if ($customer_id == "-1") {
            $cust_param = "";
        } else {
            $cust_param = "customer_id='$customer_id'";
        }

        if ($product_id == "-1") {
            $prod_param = "";
        } else {
            $prod_param = "AND d.product_id='$product_id'";
        }

        $Products = new Products();
        $StockWithdrawal = new StockWithdrawal();
        $Customer = new Customers();

        $data = "";
        $counter = 0;
        $fetch_customer = $this->select("tbl_customers","*","$cust_param");
        while ($cRow = $fetch_customer->fetch_array()) {

            $result = $this->select("tbl_sales as h, tbl_sales_details as d", "*, sum(d.quantity) as total_qty", "h.sales_id=d.sales_id AND h.status='F' AND h.customer_id='$cRow[customer_id]' AND h.withdrawal_status='1' $prod_param GROUP BY d.sales_detail_id");

            if ($result->num_rows > 0) {
                $counter += 1;
                $data .= '<table class="table" style="margin-bottom: 50px;"><thead><tr><th colspan="7" style="background: #607d8b;color: #fff;">Customer: '.$Customer->name($cRow["customer_id"]).'</th></tr><tr><th>DATE</th><th>REFERENCE #</th><th>PRODUCT</th><th style="text-align:right;">TOTAL QTY</th><th style="text-align:right;">REMAINING QTY</th></tr></thead><tbody>';
                $Sales = new Sales;
                while ($row = $result->fetch_assoc()) {
                    $remaining_qty = $StockWithdrawal->remaining_qty($row['sales_detail_id']);
                    // $paid_status = $row['paid_status'] == 1 ? "<label class='badge badge-success'>Paid</label>" : "<label class='badge badge-warning'>Unpaid</label>";
                    // $bal = $row['paid_status'] != 1 ? "₱ " . number_format($Sales->dr_balance($row['sales_id']), 2) : "0.00" ;

                    // if($row['paid_status'] == 1){
                    //     $paid_status = "<label class='badge badge-success'>Paid</label>";
                    //     $bal = "0.00";
                    // }else{
                    //     $r_bal = $Sales->dr_balance($row['sales_id']);
                    //     if($r_bal > 0){
                    //         $bal = "₱ " . number_format($r_bal,2);
                    //         $paid_status = "<label class='badge badge-warning'>Unpaid</label>";
                    //     }else{
                    //         $bal = "₱ " . number_format($r_bal,2);
                    //         $paid_status = "<label class='badge badge-success'>Paid</label>";
                    //     }
                        
                    // }

                    if ($remaining_qty > 0) {
                        $data .= "<tr>";
                        $data .= "<td>" . date('M d,Y', strtotime($row['sales_date'])) . "</td>";
                        $data .= "<td>" . $row['reference_number'] . "</td>";
                        // $data .= "<td>" . $paid_status . "</td>";
                        // $data .= "<td>" . $bal ."</td>";
                        $data .= "<td>" . $Products->name($row['product_id']) . "</td>";
                        $data .= "<td style='text-align: right;'>" . number_format($row['total_qty'], 2) . "</td>";
                        $data .= "<td style='text-align: right;'>" . number_format($remaining_qty, 2) . "</td>";
                        $data .= "</tr>";
                    }
                }
                $data .= "</tbody></table>";
            }
        }
        
        echo $counter > 0 ? $data : "<hr><center style='color: #757575;'><h3>No details found.</h3></center><hr>";
    }

    public function per_item()
    {
        $product_id = $this->inputs['product_id'];

        if ($product_id == "-1") {
            $prod_param = "";
        } else {
            $prod_param = "AND d.product_id='$product_id'";
        }
        $StockWithdrawal = new StockWithdrawal;
        $Inventory = new InventoryReport();
        $result = $this->select("tbl_products","*",$prod_param);
        while ($row = $result->fetch_assoc()) {
            //$result = $this->select("tbl_sales as sh, tbl_sales_details as sd, tbl_stock_withdrawal as wh, tbl_stock_withdrawal_details as wd", "sum(sd.quantity) as dr_qty", "sd.product_id='$prow[product_id]' AND sh.sales_id=sd.sales_id AND sh.status='F' AND wh.sales_id=sh.sales_id AND wh.withdrawal_id=wd.withdrawal_id AND wd.product_id='$prow[product_id]' AND wh.status='F'");
            
            /*
            prev version

            $fetchSAles = $this->select("tbl_sales as h, tbl_sales_details as d","*","d.product_id='$row[product_id]' AND h.sales_id=d.sales_id AND h.status='F' AND h.withdrawal_status='1'");
            $for_withdrawal = 0;
            while($sRow = $fetchSAles->fetch_array()){
                // get all qty
                $remaining_qty = $StockWithdrawal->remaining_qty($sRow['sales_detail_id']);
                $for_withdrawal += $remaining_qty;
            }

            */

            $for_withdrawal = 0;
            $fetch_claim_slips = $this->select("tbl_claim_slips", "*", "checked_by=0 and status='F'");
            while($cs_row = $fetch_claim_slips->fetch_array()){
                // withdrawal_id
                // sales_id
                $fetch_stock_withdrawal = $this->select("tbl_stock_withdrawal_details", "sum(qty)", "withdrawal_id='$cs_row[withdrawal_id]' and product_id='$row[product_id]'");
                $sw_qty = $fetch_stock_withdrawal->fetch_array();
                $for_withdrawal += $sw_qty[0];
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
