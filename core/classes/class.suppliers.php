<?php

class Suppliers extends Connection
{
    private $table = 'tbl_suppliers';
    private $pk = 'supplier_id';
    private $name = 'supplier_name';

    public function add()
    {
        $supplier_name = $this->clean($this->inputs['supplier_name']);
        $is_exist = $this->select($this->table, $this->pk, "supplier_name = '$supplier_name'");
        if ($is_exist->num_rows > 0) {
            return 2;
        } else {
            $form = array(
                'supplier_name' => $this->inputs['supplier_name'],
                'supplier_address' => $this->inputs['supplier_address'],
                'contact_number' => $this->inputs['contact_number'],
                'remarks' => $this->inputs['remarks']
            );
            return $this->insert($this->table, $form);
        }
    }

    public function edit()
    {
        $primary_id = $this->inputs[$this->pk];
        $supplier_name = $this->clean($this->inputs['supplier_name']);
        $is_exist = $this->select($this->table, $this->pk, "supplier_name = '$supplier_name' AND  $this->pk != '$primary_id'");
        if ($is_exist->num_rows > 0) {
            return 2;
        } else {
            $form = array(
                'supplier_name' => $supplier_name,
                'supplier_address' => $this->inputs['supplier_address'],
                'contact_number' => $this->inputs['contact_number'],
                'remarks' => $this->inputs['remarks']
            );
            return $this->update($this->table, $form, "$this->pk = '$primary_id'");
        }
    }


    public function remove()
    {
        $ids = implode(",", $this->inputs['ids']);
        return $this->delete($this->table, "$this->pk IN($ids)");
    }

    public function show()
    {
        $rows = array();
        $result = $this->select($this->table);
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

    public static function name($primary_id)
    {
        $self = new self;
        $result = $self->select($self->table, $self->name, "$self->pk  = '$primary_id'");
        $row = $result->fetch_assoc();
        return $row[$self->name];
    }

    public static function balance($primary_id){
        
        

    }
}
