<?php

class Customers extends Connection
{
    private $table = 'tbl_customers';
    public $pk = 'customer_id';
    public $name = 'customer_name';
    public $module_name = "Customer";

    // notes: make dynamic values for count if exist

    public function add()
    {
        $customer_name = $this->clean($this->inputs['customer_name']);
        $is_exist = $this->select($this->table, $this->pk, "customer_name = '$customer_name'");
        if ($is_exist->num_rows > 0) {
            Logs::storeCrud($this->module_name, 'c', 2, $customer_name);
            return 2;
        } else {
            $form = array(
                'customer_name' => $this->inputs['customer_name'],
                'customer_address' => $this->inputs['customer_address'],
                'customer_contact_number' => $this->inputs['customer_contact_number'],
                'remarks' => $this->inputs['remarks']
            );
            $result = $this->insert($this->table, $form);
            Logs::storeCrud($this->module_name, 'c', $result, $customer_name);
            return $result;
        }
    }

    public function edit()
    {
        $primary_id = $this->inputs[$this->pk];
        $customer_name = $this->clean($this->inputs['customer_name']);
        $is_exist = $this->select($this->table, $this->pk, "customer_name = '$customer_name' AND $this->pk != '$primary_id'");
        if ($is_exist->num_rows > 0) {
            Logs::storeCrud($this->module_name, 'u', 2, $customer_name);
            return 2;
        } else {
            $form = array(
                'customer_name' => $this->inputs['customer_name'],
                'customer_address' => $this->inputs['customer_address'],
                'customer_contact_number' => $this->inputs['customer_contact_number'],
                'remarks' => $this->inputs['remarks'],
                'date_last_modified' => $this->getCurrentDate()
            );
            $old_name = $this->name($primary_id);
            $result = $this->update($this->table, $form, "$this->pk = '$primary_id'");
            Logs::storeCrud($this->module_name, 'u', $result, $old_name, $customer_name);
            return $result;
        }
    }

    public function show()
    {
        $param = isset($this->inputs['param']) ? $this->inputs['param'] : null;
        $rows = array();
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
        $result = $this->select($this->table, $this->name, "$this->pk = '$primary_id'");
        $row = $result->fetch_assoc();
        return $row[$this->name];
    }

    public function totalCustomers()
    {
        $result = $this->select($this->table, "count(customer_id)");
        $row = $result->fetch_array();
        return $row[0];
    }

    public function addCustomerPOS()
    {
        $access_code = $this->clean($this->inputs['access_code']);

        $Settings = new Settings();
        $setting_row = $Settings->view();

        $access_code = $this->inputs['access_code'];

        if ($setting_row['module_add_customer'] == $access_code) {
            $customer_name = $this->clean($this->inputs['customer_name']);
            $is_exist = $this->select($this->table, $this->pk, "customer_name = '$customer_name'");
            if ($is_exist->num_rows > 0) {
                Logs::storeCrud($this->module_name, 'c', 2, $customer_name);
                return 2;
            } else {
                $form = array(
                    'customer_name' => $this->inputs['customer_name'],
                    'customer_address' => $this->inputs['customer_address'],
                    'customer_contact_number' => $this->inputs['customer_contact_number'],
                    'remarks' => $this->inputs['remarks']
                );
                $result = $this->insert($this->table, $form);
                Logs::storeCrud($this->module_name, 'c', $result, $customer_name);
                return $result;
            }
        } else {
            return -2;
        }
    }
}
