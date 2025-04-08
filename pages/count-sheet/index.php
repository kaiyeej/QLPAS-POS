<style>
    .lined-input {
        border: none;
        border-bottom: 1px solid #000;
        background-color: transparent;
        outline: none;
        width: 100%;
        font-size: 0.9rem;
        padding: 2px 4px;
    }

    .lined-input:focus {
        border-bottom: 2px solid #007bff;
    }

    @media print {

        h4.report-header,
        h5.report-header,
        h6.report-header {
            margin: 0 !important;
            padding: 2px 0 !important;
            line-height: 1.2 !important;
            font-weight: normal !important;
        }

        input.lined-input {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-color: transparent !important;
            border: none !important;
            border-bottom: 1px solid #000 !important;
            outline: none !important;
            width: 100% !important;
            padding: 2px 0 !important;
            font-size: 14px !important;
            color: #000 !important;
        }

        input.lined-input::placeholder {
            color: transparent !important;
        }

        .btn,
        .select2-container,
        .dataTables_filter,
        .dataTables_length,
        .dataTables_info,
        .dataTables_paginate {
            display: none !important;
        }

        /* Optional: force table borders for print clarity */
        table,
        th,
        td {
            border: 1px solid #000 !important;
            border-collapse: collapse !important;
        }

        th,
        td {
            padding: 4px !important;
        }
    }
</style>

<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="row">
                <div class="col-12 col-xl-12 mb-4 mb-xl-0">
                    <h3 class="font-weight-bold">Countsheet Report</h3>
                    <h6 class="font-weight-normal mb-3">Generate your report here</h6>
                </div>
            </div>

            <div class="col-12 col-xl-12 card shadow mb-4">
                <div class="card-body">
                    <form id='frm_generate'>
                        <div class="form-group row">
                            <div class="col">
                                <label><strong>Category</strong></label>
                                <div>
                                    <select class="form-control form-control-sm select2" id="product_category_id" name="input[product_category_id]">
                                        <option class="">&mdash; Please Select &mdash; </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <label><strong>Warehouse</strong></label>
                                <div>
                                    <select class="form-control form-control-sm select2" id="warehouse_id" name="input[warehouse_id]">
                                        <option class="">&mdash; Please Select &mdash; </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-5">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" id="btn_generate" class="btn btn-warning btn-icon-text">
                                        <i class="ti-reload mr-1"></i> Generate
                                    </button>
                                    <button type="button" class="btn btn-primary btn-icon-text" onclick="exportTableToExcel(this,'dt_entries','Inventroy-Report')">
                                        <i class="ti-cloud-down mr-1"></i> Export
                                    </button>
                                    <button type="button" class="btn btn-success btn-icon-text" onclick="print_report1('report_container')" id="btn_delete">
                                        <i class="ti-printer mr-1"></i> Print
                                    </button>

                                    <!-- <div class="btn-group pull-right">
                                        <button type="submit" id="btn_generate" class="btn btn-primary btn-sm btn-icon-split">
                                            <span class="icon text-white-50">
                                                <i class="fas fa-plus"></i>
                                            </span>
                                            <span class="text">Generate</span>
                                        </button>
                                        <button type="button" onclick="exportTableToExcel(this,'dt_entries','Inventroy-Report')" class="btn btn-success btn-sm btn-icon-split">
                                            <span class="icon">
                                                <i class="ti ti-cloud-down"></i>
                                            </span>
                                            <span class="text"> Export</span>
                                        </button>
                                        <button type="button" onclick="print_report('report_container')" class="btn btn-info btn-sm btn-icon-split">
                                            <span class="icon text-white-50">
                                                <i class="fas fa-trash"></i>
                                            </span>
                                            <span class="text">Print</span>
                                        </button>
                                    </div> -->


                                </div>
                            </div>
                        </div>
                    </form>

                    <div id="report_container" class="card-body">
                        <div class="table-responsive">
                            <center>
                                <h4 class="report-header"><span id="company_name_label"></span></h4>
                                <h6 class="report-header"><span id="company_address_label" style="word-wrap: break-word;"></span></h6>
                                <h5 class="report-header">Countsheet Report</h5>
                                <h6><span id="con_date"></span></h6><br>
                            </center>
                            <table class="display expandable-table" id="dt_entries" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Product Code</th>
                                        <th>Product</th>
                                        <th>System Count</th>
                                        <th>Actual Count</th>
                                        <th>For Pick-up</th>
                                        <th>Variance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <!-- <tr>
                                        <th colspan="2" style="text-align:right">Total:</th>
                                        <th></th>
                                        <th></th>
                                    </tr> -->
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function print_report1(container) {
        const printContent = document.getElementById(container);
        const WinPrint = window.open('', '', 'width=900,height=650');
        WinPrint.document.write(`
        <html>
            <head>
                <title>Print Report</title>
                <style>
    
                    body {
                        font-family: Arial, sans-serif;
                        
                    }

                    h4.report-header,
                    h5.report-header,
                    h6.report-header {
                        margin: 0 !important;
                        padding: 2px 0 !important;
                        line-height: 1.2 !important;
                        font-weight: normal !important;
                    }

                    input.lined-input {
                        appearance: none;
                        -webkit-appearance: none;
                        -moz-appearance: none;
                        background-color: transparent !important;
                        border: none !important;
                        border-bottom: 1px solid #000 !important;
                        outline: none !important;
                        width: 100% !important;
                        padding: 2px 0 !important;
                        font-size: 14px !important;
                        color: #000 !important;
                    }

                    input.lined-input::placeholder {
                        color: transparent !important;
                    }
                    
                    table, th, td {
                        border: 1px solid #000 !important;
                        border-collapse: collapse !important;
                    }

                    th, td {
                        padding: 4px !important;
                        text-align: center !important;
                    }

                </style>
            </head>
            <body onload="window.print(); setTimeout(() => window.close(), 100);">
                ${printContent.innerHTML}
            </body>
        </html>
    `);
        WinPrint.document.close();
    }


    $("#frm_generate").submit(function(e) {
        e.preventDefault();
        getReport();
    });

    function getReport() {
        const currentDate = new Date();
        const formattedDate = currentDate.toLocaleString('en-US', {
            month: 'long',
            day: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        }).replace(' at ', ' ');
        $("#con_date").html(formattedDate);

        var product_category_id = $("#product_category_id").val();
        var warehouse_id = $("#warehouse_id").val();
        $("#dt_entries").DataTable().destroy();
        $("#dt_entries").DataTable({
            "processing": true,
            "searching": false,
            "paging": false,
            "ordering": false,
            "info": false,
            "ajax": {
                "url": "controllers/sql.php?c=" + route_settings.class_name + "&q=countsheet",
                "dataSrc": "data",
                "method": "POST",
                "data": {
                    input: {
                        product_category_id: product_category_id,
                        warehouse_id: warehouse_id
                    }
                },
            },
            "columns": [{
                    "data": "product_code"
                },
                {
                    "data": "product_name"
                },
                {
                    "data": "in_stock"
                },
                {
                    "data": null,
                    "render": function(data, type, row, meta) {
                        return `<input type="number" class="lined-input actual-count" data-row-index="${meta.row}" value="" min="0">`;
                    }
                },
                {
                    "data": null,
                    "render": function(data, type, row, meta) {
                        return `<input type="number" class="lined-input for-pickup" data-row-index="${meta.row}" value="" min="0">`;
                    }
                },
                {
                    "data": null,
                    "render": function(data, type, row, meta) {
                        return `<input type="number" class="lined-input variance" data-row-index="${meta.row}" value="" min="0">`;
                    }
                }
            ]
        });
    }

    $(document).ready(function() {
        getSelectOption('ProductCategories', 'product_category_id', 'product_category', '', [], -1, 'All');
        getSelectOption('Warehouses', 'warehouse_id', 'warehouse_name', "branch_id ='" + current_branch_id + "'", [], -1, 'All');

        $("#company_name_label").html(company_profile.company_name);
        $("#company_address_label").html(company_profile.company_address);
    });

    // Handle dynamic variance calculation
    $(document).on('input', '.actual-count', function() {
        const rowIndex = $(this).data('row-index');
        const actualCount = parseInt($(this).val()) || 0;

        const table = $('#dt_entries').DataTable();
        const rowData = table.row(rowIndex).data();
        const inStock = parseInt(rowData.in_stock) || 0;

        const variance = actualCount - inStock;
        $(`.variance[data-row-index="${rowIndex}"]`).val(variance);
    });
</script>