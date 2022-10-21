<?php
class ProductPrice extends Connection
{
    private $table = 'tbl_price_notice';
    public $pk = 'price_id';
    public $name = 'reference_number';


    private $table_detail = 'tbl_price_notice_details';
    public $pk2 = 'price_detail_id';
    public $fk_det = 'product_id';

    public function add()
    {
        $effective_date = date('Y-m-d', strtotime($this->inputs['effective_date']));
        $form = array(
            $this->name => $this->clean($this->inputs[$this->name]),
            'effective_date' => $effective_date,
            'remarks' => $this->clean($this->inputs['remarks']),
            'status' => 'S',
        );
        return $this->insertIfNotExist($this->table, $form, "effective_date = '$effective_date'", 'Y');
    }

    public function edit()
    {
        $form = array(
            $this->name => $this->clean($this->inputs[$this->name])
        );
        return $this->updateIfNotExist($this->table, $form);
    }

    public function show()
    {
        $param = isset($this->inputs['param']) ? $this->inputs['param'] : '';
        $rows = array();
        $result = $this->select($this->table, '*', $param);
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function view()
    {
        $primary_id = $this->inputs['id'];
        $result = $this->select($this->table, "*", "$this->pk = '$primary_id'");
        return $result->fetch_assoc();
    }

    public function remove()
    {
        $ids = implode(",", $this->inputs['ids']);
        return $this->delete($this->table, "$this->pk IN($ids)");
    }

    public function name($primary_id)
    {
        $result = $this->select($this->table, $this->name, "$this->pk = '$primary_id'");
        $row = $result->fetch_assoc();
        return $row[$this->name];
    }

    public function generate()
    {
        return 'PN-' . date('YmdHis');
    }

    public function add_detail()
    {
        $primary_id = $this->inputs[$this->pk];
        $fk_det     = $this->inputs[$this->fk_det];

        $form = array(
            $this->pk       => $this->inputs[$this->pk],
            $this->fk_det   => $fk_det,
            'old_price'     => $this->inputs['old_price'],
            'price'         => $this->inputs['new_price'],
        );

        return $this->insertIfNotExist($this->table_detail, $form, "$this->pk = '$primary_id' AND $this->fk_det = '$fk_det'");
    }

    public function show_detail()
    {
        $param = isset($this->inputs['param']) ? $this->inputs['param'] : null;
        $rows = array();
        $count = 1;
        $result = $this->select($this->table_detail, '*', $param);
        while ($row = $result->fetch_assoc()) {
            $row['count'] = $count++;
            $row['product'] = Products::name($row['product_id']);
            $rows[] = $row;
        }
        return $rows;
    }

    public function remove_detail()
    {
        $ids = implode(",", $this->inputs['ids']);
        return $this->delete($this->table_detail, "$this->pk2 IN($ids)");
    }

    public function finish()
    {
        $primary_id = $this->inputs['id'];
        $form = array(
            'status' => 'F',
        );
        return $this->update($this->table, $form, "$this->pk = '$primary_id'");
    }

    public function runner()
    {
        $current_date = date('Y-m-d');
        $rows = array();
        $result = $this->select($this->jccrypt($this->table), "$this->pk,$this->name", "status = 'F' AND effective_status = 0 AND effective_date <= '$current_date'");
        while ($row = $result->fetch_assoc()) {
            if ($this->implement($row[$this->pk]) == 1) {
                $rows[] = $row[$this->name];
                $this->update($this->jccrypt($this->table), ['effective_status' => 1], "$this->pk = '" . $row[$this->pk] . "'");
            }
        }
        return $rows;
    }

    public function implement($price_id)
    {
        $sql = "UPDATE " . $this->jccrypt($this->table_detail) . " AS d, " . $this->jccrypt('tbl_products') . " AS p SET p.product_price = d.price  WHERE d.product_id = p.product_id AND d.price_id = '$price_id'";
        return $this->query($sql);
    }

    public function schema()
    {
        if (DEVELOPMENT) {
            $default['date_added'] = $this->metadata('date_added', 'datetime', '', 'NOT NULL', 'CURRENT_TIMESTAMP');
            $default['date_last_modified'] = $this->metadata('date_last_modified', 'datetime', '', 'NOT NULL', '', 'ON UPDATE CURRENT_TIMESTAMP');


            // TABLE HEADER
            $tables[] = array(
                'name'      => $this->jccrypt($this->table),
                'primary'   => $this->pk,
                'fields' => array(
                    $this->metadata($this->pk, 'int', 11, 'NOT NULL', '', 'AUTO_INCREMENT'),
                    $this->metadata($this->name, 'varchar', 75),
                    $this->metadata('effective_date', 'date'),
                    $this->metadata('effective_status', 'int', 1),
                    $this->metadata('remarks', 'varchar', 255),
                    $this->metadata('status', 'varchar', 1),
                    $default['date_added'],
                    $default['date_last_modified']
                )
            );

            // TABLE DETAILS
            $tables[] = array(
                'name'      => $this->jccrypt($this->table_detail),
                'primary'   => $this->pk2,
                'fields' => array(
                    $this->metadata($this->pk2, 'int', 11, 'NOT NULL', '', 'AUTO_INCREMENT'),
                    $this->metadata($this->pk, 'int', 11),
                    $this->metadata($this->fk_det, 'int', 11),
                    $this->metadata('old_price', 'decimal', '12,2'),
                    $this->metadata('price', 'decimal', '12,2'),
                    $default['date_added'],
                    $default['date_last_modified']
                )
            );

            return $this->schemaCreator($tables);
        }
    }
}
