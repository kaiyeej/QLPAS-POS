<?php

class Branches extends Connection
{
    private $table = 'tbl_branch';
    public $pk = 'branch_id';
    public $name = 'branch_name';
    public $module_name = "Branches";

    public $inputs = [];

    public $searchable = ['branch_name'];
    public $uri = "banks";

    public function add()
    {
        $branch_name = $this->clean($this->inputs['branch_name']);
        $is_exist = $this->select($this->table, $this->pk, "branch_name = '$branch_name'");
        if ($is_exist->num_rows > 0) {
            Logs::storeCrud($this->module_name, 'c', 2, $branch_name);
            return 2;
        } else {
            $form = array(
                'branch_name' => $this->inputs['branch_name'],
            );

            $result = $this->insert($this->table, $form);
            Logs::storeCrud($this->module_name, 'c', $result, $branch_name);
            return $result;
        }
    }

    public function edit()
    {
        $primary_id = $this->inputs[$this->pk];
        $branch_name = $this->clean($this->inputs['branch_name']);
        $is_exist = $this->select($this->table, $this->pk, "branch_name = '$branch_name' AND $this->pk != '$primary_id'");
        if ($is_exist->num_rows > 0) {
            Logs::storeCrud($this->module_name, 'u', 2, $branch_name);
            return 2;
        } else {
            $form = array(
                'branch_name' => $this->inputs['branch_name'],
            );

            $result = $this->update($this->table, $form, "$this->pk = '$primary_id'");
            Logs::storeCrud($this->module_name, 'u', $result, $this->name($primary_id), $branch_name);
            return $result;
        }
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

    public function remove()
    {
        $ids = implode(",", $this->inputs['ids']);

        foreach ($this->inputs['ids'] as $id) {
            $name = $this->name($id);
            $result = $this->delete($this->table, "$this->pk = '$id'");
            Logs::storeCrud($this->module_name, 'd', $result, $name);
        }

        return 1; //$this->delete($this->table, "$this->pk IN($ids)");
    }

    public function name($primary_id)
    {
        $result = $this->select($this->table, '*', "$this->pk = '$primary_id'");
        $row = $result->fetch_assoc();
        return $row['branch_name'] . " - " . $row['bank_account_number'];
    }

    public static function search($words,&$rows)
    {
        $self = new self;
        if(count($self->searchable) > 0 ){
            $where = implode(" LIKE '%$words%' OR ", $self->searchable)." LIKE '%$words%'";
            $result = $self->select($self->table, '*', $where);
            while ($row = $result->fetch_assoc()) {
                $names = [];
                foreach($self->searchable as $f){
                    $names[] = $row[$f];
                }
                $rows[] = array(
                    'name' => implode(" ", $names),
                    'module' => $self->module_name,
                    'slug' => $self->uri."?id=".$row[$self->pk]
                );
            }
        }
    }
}