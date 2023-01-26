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
            $cust_param = "AND h.customer_id='$customer_id'";
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
        $fetch_customer = $this->select("tbl_customers");
        while ($cRow = $fetch_customer->fetch_array()) {

            $result = $this->select("tbl_sales as h, tbl_sales_details as d", "*, sum(d.quantity) as total_qty", "h.sales_id=d.sales_id AND h.status='F' AND h.customer_id='$cRow[customer_id]' AND h.withdrawal_status='1' $prod_param $cust_param GROUP BY d.sales_detail_id");

            if ($result->num_rows > 0) {
                $data .= '<table class="table" style="margin-bottom: 50px;"><thead><tr><th colspan="5" style="background: #607d8b;color: #fff;">Customer: '.$Customer->name($cRow["customer_id"]).'</th></tr><tr><th>DATE</th><th>REFERENCE #</th><th>PRODUCT</th><th style="text-align:right;">TOTAL QTY</th><th style="text-align:right;">REMAINING QTY</th></tr></thead><tbody>';

                while ($row = $result->fetch_assoc()) {
                    $remaining_qty = $StockWithdrawal->remaining_qty($row['sales_detail_id']);
                    if ($remaining_qty > 0) {
                        $data .= "<tr>";
                        $data .= "<td>" . date('M d,Y', strtotime($row['sales_date'])) . "</td>";
                        $data .= "<td>" . $row['reference_number'] . "</td>";
                        $data .= "<td>" . $Products->name($row['product_id']) . "</td>";
                        $data .= "<td style='text-align: right;'>" . number_format($row['total_qty'], 2) . "</td>";
                        $data .= "<td style='text-align: right;'>" . number_format($remaining_qty, 2) . "</td>";
                        $data .= "</tr>";
                    }
                }
                $data .= "</tbody></table>";
            }
        }
        echo $data;
    }
}
