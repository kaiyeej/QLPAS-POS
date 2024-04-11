<?php

class Warehouses extends Connection
{
    private $table = 'tbl_warehouses';
    public $pk = 'warehouse_id';
    public $name = 'warehouse_name';
    public $module_name = "Warehouses";

    public $inputs = [];

    public $searchable = ['warehouse_name'];
    public $uri = "warehouses";

    public function add()
    {
        $warehouse_name = $this->clean($this->inputs['warehouse_name']);
        $branch_id = $this->clean($this->inputs['branch_id']);
        $is_exist = $this->select($this->table, $this->pk, "warehouse_name = '$warehouse_name' AND branch_id='$branch_id'");
        if ($is_exist->num_rows > 0) {
            Logs::storeCrud($this->module_name, 'c', 2, $warehouse_name);
            return -2;
        } else {
            $form = array(
                'warehouse_name' => $warehouse_name,
                'branch_id' => $branch_id
            );

            $result = $this->insert($this->table, $form);
            Logs::storeCrud($this->module_name, 'c', $result, $warehouse_name);
            return $result;
        }
    }

    public function edit()
    {
        $primary_id = $this->inputs[$this->pk];
        $warehouse_name = $this->clean($this->inputs['warehouse_name']);
        $is_exist = $this->select($this->table, $this->pk, "warehouse_name = '$warehouse_name' AND $this->pk != '$primary_id'");
        if ($is_exist->num_rows > 0) {
            Logs::storeCrud($this->module_name, 'u', 2, $warehouse_name);
            return 2;
        } else {
            $form = array(
                'warehouse_name' => $this->inputs['warehouse_name'],
            );

            $result = $this->update($this->table, $form, "$this->pk = '$primary_id'");
            Logs::storeCrud($this->module_name, 'u', $result, $this->name($primary_id), $warehouse_name);
            return $result;
        }
    }

    public function show()
    {
        $rows = array();
        $param = isset($this->inputs['param']) ? $this->inputs['param'] : null;
        $result = $this->select($this->table, "*", $param);
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
        $primary_id = $this->inputs['id'];
        $name = $this->name($primary_id);
        $result = $this->delete($this->table, "$this->pk = '$primary_id'");
        Logs::storeCrud($this->module_name, 'd', $result, $name);

        return $result;
    }

    public function name($primary_id)
    {
        $result = $this->select($this->table, '*', "$this->pk = '$primary_id'");
        $row = $result->fetch_assoc();
        return $row['warehouse_name'] . " - " . $row['bank_account_number'];
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
                    $this->metadata('branch_id', 'int'),
                    $default['date_added'],
                    $default['date_last_modified']
                )
            );

            return $this->schemaCreator($tables);
        }
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
