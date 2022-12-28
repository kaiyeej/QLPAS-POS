<?php
class Products extends Connection
{
    private $table = 'tbl_products';
    public $pk = 'product_id';
    public $name = 'product_name';
    public $module_name = "Product";

    public function add()
    {
        $form = array(
            $this->name             => $this->clean($this->inputs[$this->name]),
            'product_price'         => $this->inputs['product_price'],
            'product_category_id'   => $this->inputs['product_category_id'],
            'remarks'               => $this->inputs['remarks'],
            'product_code'          => $this->inputs['product_code'],
            'product_barcode'       => $this->inputs['product_barcode'],
            'is_package'            => isset($this->inputs['is_package']) ? '1' : '0'
        );
        $param = "product_code='" . $this->inputs['product_code'] . "'";
        $param .= $this->inputs['product_barcode'] != '' ? " OR product_barcode = '" . $this->inputs['product_barcode'] . "'" : '';
        $result = $this->insertIfNotExist($this->table, $form, $param);
        Logs::storeCrud($this->module_name, 'c', $result, $this->inputs[$this->name]);
        return $result;
    }

    public function edit()
    {
        $form = array(
            $this->name             => $this->clean($this->inputs[$this->name]),
            'product_category_id'   => $this->inputs['product_category_id'],
            'product_code'          => $this->inputs['product_code'],
            'product_price'         => $this->inputs['product_price'],
            'product_barcode'       => $this->inputs['product_barcode'],
            'remarks'               => $this->inputs['remarks'],
            'is_package'            => isset($this->inputs['is_package']) ? '1' : '0'
        );
        $param = "(product_code='" . $this->inputs['product_code'] . "'";
        $param .= $this->inputs['product_barcode'] != '' ? " OR product_barcode = '" . $this->inputs['product_barcode'] . "')" : ')';
        $old_name = self::name($this->inputs[$this->pk]);
        $result = $this->updateIfNotExist($this->table, $form, $param);
        Logs::storeCrud($this->module_name, 'u', $result, $old_name, $this->inputs[$this->name]);
        return $result;
    }

    public function show()
    {
        $param = isset($this->inputs['param']) ? $this->inputs['param'] : '';
        $ProductCategories = new ProductCategories();
        $rows = array();
        $result = $this->select($this->table, '*', $param);
        $count = 1;
        while ($row = $result->fetch_assoc()) {
            $row['count'] = $count++;
            $row['product_category'] = $ProductCategories->name($row['product_category_id']);
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

        foreach ($this->inputs['ids'] as $id) {
            $name = self::name($id);
            $result = $this->delete($this->table, "$this->pk = '$id'");
            Logs::storeCrud($this->module_name, 'd', $result, $name);
        }

        return 1; //$this->delete($this->table, "$this->pk IN($ids)");
    }

    public static function name($primary_id)
    {
        $self = new self;
        $result = $self->select($self->table, $self->name, "$self->pk = '$primary_id'");
        $row = $result->fetch_assoc();
        return $row[$self->name];
    }

    public function productID($product_barcode)
    {
        $fetch = $this->select($this->table, $this->pk, "product_barcode='$product_barcode'");
        $row = $fetch->fetch_assoc();
        return $row[$this->pk];
    }

    public function productPrice($primary_id)
    {
        $fetch = $this->select($this->table, "product_price", "$this->pk = '$primary_id'");
        $row = $fetch->fetch_assoc();
        return $row['product_price'];
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
                    $this->metadata('product_barcode', 'varchar', 50),
                    $this->metadata('product_code', 'varchar', 10),
                    $this->metadata('product_price', 'decimal', '11,2'),
                    $this->metadata('product_cost', 'decimal', '11,2'),
                    $this->metadata('product_img', 'text'),
                    $this->metadata('product_category_id', 'int', 11),
                    $this->metadata('remarks', 'varchar', 255),
                    $this->metadata('is_package', 'int', 1),
                    $default['date_added'],
                    $default['date_last_modified']
                )
            );

            return $this->schemaCreator($tables);
        }
    }

    public function productCost($primary_id)
    {
        $fetch = $this->select($this->table, "product_cost", "$this->pk = '$primary_id'");
        $row = $fetch->fetch_assoc();
        return $row['product_cost'];
    }

    public function prodAVG($product_id, $new_qty, $new_cost)
    {
        $Inv = new InventoryReport();
        $current_cost = $this->productCost($product_id);
        $current_qty = $Inv->balance($product_id);

        $avg_cost = (($current_qty * $current_cost) + ($new_qty * $new_cost)) / ($current_qty + $new_qty);

        $form = array(
            'product_cost' => ($avg_cost > 0 ? $avg_cost : 0)
        );

        return $this->update($this->table, $form, "$this->pk = '$product_id'");
    }

    public function prodRVS($product_id, $new_qty, $new_cost)
    {
        $Inv = new InventoryReport();
        $current_cost = $this->productCost($product_id);
        $current_qty = $Inv->balance($product_id);
        if (($current_qty - $new_qty) <= 0) {
            $avg_cost = 0;
        } else {
            $avg_cost = (($current_qty * $current_cost) - ($new_qty * $new_cost)) / ($current_qty - $new_qty);
        }

        $form = array(
            'product_cost' => ($avg_cost > 0 ? $avg_cost : 0)
        );

        return $this->update($this->table, $form, "$this->pk = '$product_id'");
    }
}
