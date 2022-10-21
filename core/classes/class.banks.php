<?php

class Banks extends Connection
{
    private $table = 'tbl_banks';
    public $pk = 'bank_id';
    public $name = 'bank_name';

    // notes: make dynamic values for count if exist

    public function add()
    {
        $bank_name = $this->clean($this->inputs['bank_name']);
        $is_exist = $this->select($this->table, $this->pk, "bank_name = '$bank_name'");
        if ($is_exist->num_rows > 0) {
            return 2;
        } else {
            $form = array(
                'bank_name' => $this->inputs['bank_name'],
                'bank_branch' => $this->inputs['bank_branch'],
                'bank_account_name' => $this->inputs['bank_account_name'],
                'bank_account_number' => $this->inputs['bank_account_number']
            );
            return $this->insert($this->table, $form);
        }
    }

    public function edit()
    {
        $primary_id = $this->inputs[$this->pk];
        $bank_name = $this->clean($this->inputs['bank_name']);
        $is_exist = $this->select($this->table, $this->pk, "bank_name = '$bank_name' AND $this->pk != '$primary_id'");
        if ($is_exist->num_rows > 0) {
            return 2;
        } else {
            $form = array(
                'bank_name' => $this->inputs['bank_name'],
                'bank_branch' => $this->inputs['bank_branch'],
                'bank_account_name' => $this->inputs['bank_account_name'],
                'bank_account_number' => $this->inputs['bank_account_number']
            );
            return $this->update($this->table, $form, "$this->pk = '$primary_id'");
        }
    }

    public function show()
    {
        $rows = array();
        $result = $this->select($this->table);
        while ($row = $result->fetch_assoc()) {
            $row['bank'] = $row['bank_name']." - ".$row['bank_account_number'];
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
        $result = $this->select($this->table, '*', "$this->pk = '$primary_id'");
        $row = $result->fetch_assoc();
        return $row['bank_name']." - ".$row['bank_account_number'];
    }
}
