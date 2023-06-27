<?php
class Expense extends Connection
{
    private $table = 'tbl_expense';
    public $pk = 'expense_id';
    public $name = 'reference_number';

    private $table_detail = 'tbl_expense_details';
    public $pk2 = 'expense_detail_id';
    public $fk_det = 'expense_category_id';

    public $module = 'EXP-';
    public $module_name = "Expense";
    public $inputs = [];
    public $searchable = ['reference_number', 'remarks'];
    public $uri = "expense";
    public function add()
    {
        $form = array(
            $this->name     => $this->clean($this->inputs[$this->name]),
            'remarks'       => $this->inputs['remarks'],
            'expense_date'  => $this->inputs['expense_date'],
            'encoded_by'    => $_SESSION['user']['id']
        );
        return $this->insertIfNotExist($this->table, $form, '', 'Y');
    }

    public function edit()
    {
        $form = array(
            'remarks'       => $this->inputs['remarks'],
            'expense_date'  => $this->inputs['expense_date'],
            'encoded_by'    => $_SESSION['user']['id']
        );
        return $this->updateIfNotExist($this->table, $form);
    }

    public function show()
    {
        $param = isset($this->inputs['param']) ? $this->inputs['param'] : null;
        $rows = array();
        $Users = new Users;
        $result = $this->select($this->table, '*', $param);
        while ($row = $result->fetch_assoc()) {
            $row['encoded_name'] = $Users->getUser($row['encoded_by']);
            $row['total'] = number_format($this->total($row['expense_id']), 2);
            $rows[] = $row;
        }
        return $rows;
    }

    public function view()
    {
        $primary_id = $this->inputs['id'];
        $ExpenseCategories = new ExpenseCategories();
        $result = $this->select($this->table, "*", "$this->pk = '$primary_id'");
        $row = $result->fetch_assoc();
        $row['expense_type_name'] = $ExpenseCategories->expense_type($row['expense_id']);
        return $row;
    }

    public function remove()
    {
        $ids = implode(",", $this->inputs['ids']);
        return $this->delete($this->table, "$this->pk IN($ids)");
    }

    public function generate()
    {
        return $this->module  . date('YmdHis');
    }

    public function finish()
    {
        $primary_id = $this->inputs['id'];
        $form = array(
            'status' => 'F',
        );
        return $this->update($this->table, $form, "$this->pk = '$primary_id'");
    }

    public function pk_by_name($name = null)
    {
        $name = $name == null ? $this->inputs[$this->name] : $name;
        $result = $this->select($this->table, $this->pk, "$this->name = '$name'");
        $row = $result->fetch_assoc();
        return $row[$this->pk] * 1;
    }

    public function name($primary_id)
    {
        $result = $this->select($this->table, $this->name, "$this->pk = '$primary_id'");
        $row = $result->fetch_assoc();
        return $row[$this->name];
    }

    public function add_detail()
    {
        $primary_id = $this->inputs[$this->pk];
        $fk_det     = $this->inputs[$this->fk_det];

        $form = array(
            $this->pk       => $this->inputs[$this->pk],
            $this->fk_det   => $fk_det,
            'supplier_id'   => $this->inputs['supplier_id'],
            'invoice_no'    => $this->inputs['invoice_no'],
            'amount'        => $this->inputs['amount'],
            'description'   => $this->clean($this->inputs['description']),
        );
        return $this->insert($this->table_detail, $form);
    }

    public function show_detail()
    {
        $param = isset($this->inputs['param']) ? $this->inputs['param'] : null;
        $rows = array();
        $result = $this->select($this->table_detail, '*', $param);
        while ($row = $result->fetch_assoc()) {
            $row['expense_category'] = ExpenseCategories::name($row['expense_category_id']);
            $row['supplier'] = Suppliers::name($row['supplier_id']);
            $rows[] = $row;
        }
        return $rows;
    }

    public function remove_detail()
    {
        $ids = implode(",", $this->inputs['ids']);
        return $this->delete($this->table_detail, "$this->pk2 IN($ids)");
    }

    public function totalExpensesDays($days)
    {
        $fetchData = $this->select('tbl_expense_details as d, tbl_expense as h', "sum(amount) as total", "h.expense_id = d.expense_id AND h.expense_date BETWEEN NOW() - INTERVAL $days DAY AND NOW() AND h.status='F'");
        $row = $fetchData->fetch_assoc();

        return $row['total'] == 0 ? 0 : $row['total'];
    }

    public function total($primary_id)
    {
        $fetchData = $this->select('tbl_expense_details as d, tbl_expense as h', "sum(amount) as total", "h.expense_id = d.expense_id AND h.expense_id='$primary_id'");
        $row = $fetchData->fetch_assoc();

        return $row['total'];
    }

    public function graph()
    {
        $backgroundColors = [
            'rgba(255, 99, 132, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 206, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(255, 159, 64, 0.2)'
        ];

        $borderColors = [
            'rgba(255,99,132,1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)'
        ];
        $ExpenseCategories = new ExpenseCategories();
        $ExpenseCategories->inputs['param'] = "expense_category_id > 0 ORDER BY expense_category ASC";
        $loop_categories = $ExpenseCategories->show();

        $labels = [];
        foreach ($loop_categories as $row) {
            $labels[] = $row['expense_category'];
        }
        $current_year = date('Y');
        $last_5years = $current_year - 4;

        $datasets = [];
        $counter = 0;
        for ($i = $last_5years; $i <= $current_year; $i++) {
            $data = [];
            $backgroundColor = [];
            $borderColor = [];
            foreach ($loop_categories as $row) {

                $result = $this->select(
                    "tbl_expense_details AS d,tbl_expense AS h",
                    "SUM(amount) AS amount",
                    "h.expense_id = d.expense_id AND h.status = 'F' AND YEAR(h.expense_date) = '$i' AND expense_category_id = '$row[expense_category_id]'"
                );

                if ($result->num_rows > 0) {
                    $rowE = $result->fetch_assoc();
                    $amount = $rowE['amount'] * 1;
                } else {
                    $amount = 0;
                }
                $data[] = $amount;
                $backgroundColor[] = $backgroundColors[$counter];
                $borderColor[] = $borderColors[$counter];
            }

            $list = array(
                'label' => "Year $i",
                'data' => array_sum($data) > 0 ? $data : [],
                'borderWidth' => 1,
                'backgroundColor' => $backgroundColor,
                'borderColor' => $borderColor,
                'fill' => true
            );

            $datasets[] = $list;
            $counter++;
        }

        // $result = $this->select(
        //     "tbl_expense_details AS d,tbl_expense AS h",
        //     "SUM(amount) AS amount,expense_category_id",
        //     "h.expense_id = d.expense_id AND h.status = 'F' AND YEAR(h.expense_date) = '$current_year' GROUP BY expense_category_id"
        // );

        // $data = [];

        // while ($row = $result->fetch_assoc()) {
        //     $data[] = (float) $row['amount'];
        // }

        return [
            'datasets' => $datasets,
            'labels' => $labels
        ];
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

    public function schema()
    {
        $default['date_added'] = $this->metadata('date_added', 'datetime', '', 'NOT NULL', 'CURRENT_TIMESTAMP');
        $default['date_last_modified'] = $this->metadata('date_last_modified', 'datetime', '', 'NOT NULL', '', 'ON UPDATE CURRENT_TIMESTAMP');

        // TABLE HEADER
        $tables[] = array(
            'name'      => $this->table,
            'primary'   => $this->pk,
            'fields' => array(
                $this->metadata($this->pk, 'int', 11, 'NOT NULL', '', 'AUTO_INCREMENT'),
                $this->metadata($this->name, 'varchar', 50),
                $this->metadata('expense_date', 'datetime', 'NOT NULL'),
                $this->metadata('remarks', 'varchar', 255, 'NOT NULL'),
                $this->metadata('status', 'varchar', 1, 'NOT NULL'),
                $this->metadata('encoded_by', 'int', 11, 'NOT NULL'),
                $default['date_added'],
                $default['date_last_modified']
            )
        );

        // TABLE DETAIL
        $tables[] = array(
            'name'      => $this->table_detail,
            'primary'   => $this->pk2,
            'fields' => array(
                $this->metadata($this->pk2, 'int', 11, 'NOT NULL', '', 'AUTO_INCREMENT'),
                $this->metadata($this->pk, 'int', 11),
                $this->metadata('supplier_id', 'int', 11),
                $this->metadata('expense_category_id', 'int', 11),
                $this->metadata('invoice_no', 'varchar', '15'),
                $this->metadata('amount', 'decimal', '12,2'),
                $this->metadata('description', 'text')
            )
        );
        return $this->schemaCreator($tables);
    }
}
