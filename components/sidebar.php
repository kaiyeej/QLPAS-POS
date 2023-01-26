<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <?php

        $Menus = new Menus();

        // MASTER DATA
        $Menus->sidebar('Dashboard', 'homepage', 'layout-grid2');
        $Menus->sidebar('Beginning Balance', 'beginning-balance', 'archive');
        $Menus->sidebar_parent('Products', 'package', array(
            array('Categories', 'product-categories'),
            array('Entries', 'products'),
            array('Price Notice', 'product-price'),
        ));
        $Menus->sidebar('Banks', 'banks', 'wallet');
        $Menus->sidebar('Customers', 'customers', 'id-badge');
        $Menus->sidebar('Suppliers', 'suppliers', 'truck');
        $Menus->sidebar('Discounts', 'discounts', 'tag');
        $Menus->sidebar('Payment Option', 'payment-option', 'tag');
        $Menus->sidebar('Formulation', 'formulation', 'filter');

        // TRANSACTION
        $Menus->sidebar_parent('Purchase Order', 'package', array(
            array('Entries', 'purchase-order'),
            array('Returns', 'purchase-return'),
        ));
        $Menus->sidebar_parent('Sales', 'pencil-alt', array(
            array('Entries', 'sales'),
            array('Returns', 'sales-return'),
        ));
        $Menus->sidebar('Stock Withdrawal', 'stock-withdrawal', 'agenda');
        $Menus->sidebar_parent('Payment', 'receipt', array(
            array('Customer', 'customer-payment'),
            array('Supplier', 'supplier-payment'),
        ));
        $Menus->sidebar_parent('Expense', 'money', array(
            array('Entries', 'expense'),
            array('Categories', 'expense-category'),
        ));
        $Menus->sidebar('Job Order', 'job-order', 'layout');
        $Menus->sidebar('Product Conversion', 'product-conversion', 'exchange-vertical');
        $Menus->sidebar('Inventory Adjustment', 'inventory-adjustment', 'split-v');
        $Menus->sidebar('Deposit', 'deposit', 'credit-card');

        // REPORTS
        $Menus->sidebar_parent('Reports', 'write', array(
            array('Bank Ledger', 'bank-ledger'),
            array('Expense Report', 'expense-report'),
            array('Bank Due Report', 'bankdue-report'),
            array('Payable Aging', 'payable-aging'),
            array('Payable Ledger', 'payable-ledger'),
            array('Payable Report', 'payable-report'),
            array('Price List', 'price-list'),
            array('Purchase Report', 'purchase-report'),
            array('Aging of AR', 'receivable-aging'),
            array('Receivable Ledger', 'receivable-ledger'),
            array('Receivable Report', 'receivable-report'),
            array('Sales Report', 'sales-report'),
            array('Stock Releasal', 'stock-releasal'),
            array('Inventory Report', 'inventory-report'),
            array('Stock Card', 'stock-card'),
            array('Income Statement', 'income-statement'),
        ));

        // ADMIN
        $Menus->sidebar('User Accounts', 'users', 'user');
        $Menus->sidebar('Logs', 'log', 'user');
        ?>
    </ul>
</nav>