<?php
class Settings extends Connection
{
    private $table = 'tbl_settings';
    public $pk = 'settings_id';

    public function add()
    {
        $form = array(
            'module_discount'               => $this->inputs['module_discount'],
            'module_cancel'                 => $this->inputs['module_cancel'],
            'module_delete'                 => $this->inputs['module_delete'],
            'module_add_customer'           => $this->inputs['module_add_customer'],
            'module_change_payment_type'    => $this->inputs['module_change_payment_type'],
            'module_remove_online_payment'  => $this->inputs['module_remove_online_payment'],
        );

        $result = $this->select($this->table, 'settings_id');
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $settings_id = $row['settings_id'];
            return $this->update($this->table, $form, "settings_id = '$settings_id'");
        } else {
            return $this->insert($this->table, $form);
        }
    }

    public function addProfile()
    {
        $form = array(
            'company_name'      => $this->clean($this->inputs['company_name']),
            'company_address'   => $this->clean($this->inputs['company_address']),
            'print_header'      => $this->clean($this->inputs['print_header']),
            'print_footer'      => $this->clean($this->inputs['print_footer']),
        );

        $result = $this->select($this->table, 'settings_id');
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $settings_id = $row['settings_id'];
            return $this->update($this->table, $form, "settings_id = '$settings_id'");
        } else {
            return $this->insert($this->table, $form);
        }
    }

    public function view()
    {
        $result = $this->select($this->table);
        return $result->fetch_assoc();
    }

    public function version()
    {
        $repo = __DIR__ . "../../";
        $output = shell_exec("cd $repo && git pull 2>&1");
        return "<pre>$output</pre>";
    }

    public function schema()
    {
        if (DEVELOPMENT) {
            $default['date_added'] = $this->metadata('date_added', 'datetime', '', 'NOT NULL', 'CURRENT_TIMESTAMP');
            $default['date_last_modified'] = $this->metadata('date_last_modified', 'datetime', '', 'NOT NULL', '', 'ON UPDATE CURRENT_TIMESTAMP');


            // TABLE HEADER
            $tables[] = array(
                'name'      => $this->table,
                'primary'   => $this->pk,
                'fields' => array(
                    $this->metadata($this->pk, 'int', 11, 'NOT NULL', '', 'AUTO_INCREMENT'),
                    $this->metadata('module_discount', 'varchar', 32),
                    $this->metadata('module_cancel', 'varchar', 32),
                    $this->metadata('module_delete', 'varchar', 32),
                    $this->metadata('module_add_customer', 'varchar', 32),
                    $this->metadata('module_change_payment_type', 'varchar', 32),
                    $this->metadata('module_remove_online_payment', 'varchar', 32),
                    $this->metadata('company_name', 'varchar', 50),
                    $this->metadata('company_address', 'varchar', 100),
                    $this->metadata('print_header', 'text'),
                    $this->metadata('print_footer', 'text'),
                    $default['date_added'],
                    $default['date_last_modified']
                )
            );

            return $this->schemaCreator($tables);
        }
    }
}
