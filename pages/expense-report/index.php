<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="row">
                <div class="col-12 col-xl-12 mb-4 mb-xl-0">
                    <h3 class="font-weight-bold">Expense Report</h3>
                    <h6 class="font-weight-normal mb-0">Generate expense report here</h6>
                </div>
            </div>
            <div id="daily" class="tab-pane fade active show">
                <div class="card-header py-3">
                    <form id='frm_generate'>
                        <div class="form-group row">
                            <div class="col">
                                <label><strong>Start Date</strong></label>
                                <div>
                                    <input type="date" value="<?php echo date('Y-m-01', strtotime(date("Y-m-d"))); ?>" required class="form-control" id="start_date" name="input[start_date]">
                                </div>
                            </div>
                            <div class="col">
                                <label><strong>End Date</strong></label>
                                <div>
                                    <input type="date" required class="form-control" value="<?php echo date('Y-m-t', strtotime(date("Y-m-d"))) ?>" id="end_date" name="input[end_date]">
                                </div>
                            </div>
                            <div class="col">
                                <label><strong>Type</strong></label>
                                <div>
                                    <select class="form-control form-control-sm select2" required id="expense_type" name="input[expense_type]">
                                        <option value="-1">&mdash; All &mdash; </option>
                                        <option value='OE'>Operational Expense</option>
                                        <option value='O'>Other Expense</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <label>&nbsp;</label>
                                <div>
                                    <div class="btn-group pull-right">
                                        <button type="submit" id="btn_daily" class="btn btn-primary btn-sm btn-icon-split">
                                            <span class="icon">
                                                <i class="ti ti-reload"></i>
                                            </span>
                                            <span class="text"> Generate</span>
                                        </button>
                                        <button type="button" onclick="exportTableToExcel(this,'dt_entries','Expense-Report')" class="btn btn-success btn-sm btn-icon-split">
                                            <span class="icon">
                                                <i class="ti ti-cloud-down"></i>
                                            </span>
                                            <span class="text"> Export</span>
                                        </button>
                                        <button type="button" onclick="print_report('daily_container')" class="btn btn-info btn-sm btn-icon-split">
                                            <span class="icon">
                                                <i class="ti ti-printer"></i>
                                            </span>
                                            <span class="text"> Print</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div id="daily_container" class="card-body">
                    <center>
                        <h4 class="report-header"><span id="company_name_label"></span></h4>
                        <h6 class="report-header"><span id="company_address_label" style="word-wrap: break-word;"></span></h6>
                        <h6 class="report-header" id="report_date"></h6>
                        <h6 class="report-header" id="type_span"></h6>
                    </center>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dt_entries" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>DATE</th>
                                    <th>CATEGORY</th>
                                    <th>REFERENCE #</th>
                                    <th>SUPPLIER</th>
                                    <th style="text-align:right">AMOUNT</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" style="text-align:right">Total:</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        getEntries();
        
        $("#company_name_label").html(company_profile.company_name);
        $("#company_address_label").html(company_profile.company_address);
    });

    $("#frm_generate").submit(function(e) {
        e.preventDefault();
        getEntries();
    });

    function expenseType() {
        var expense_type = $("#expense_type").val();
        if (expense_type == -1) {
            $("#type_span").html("ALL");
        } else if (expense_type == "O") {
            $("#type_span").html("Other Expense");
        } else if (expense_type == "OE") {
            $("#type_span").html("Operational Expense");
        }
    }

    function getEntries() {
        var start_date = $("#start_date").val();
        var end_date = $("#end_date").val();
        var expense_type = $("#expense_type").val();

        const d1 = new Date(start_date);
        let start = d1.toDateString().slice(4);

        const d2 = new Date(end_date);
        let end = d2.toDateString().slice(4);
        expenseType();
        $("#report_date").html(start + " - " + end);

        $("#dt_entries").DataTable().destroy();
        $("#dt_entries").DataTable({
            "processing": true,
            "searching": false,
            "paging": false,
            "ordering": false,
            "info": false,
            "ajax": {
                "url": "controllers/sql.php?c=" + route_settings.class_name + "&q=show",
                "dataSrc": "data",
                "method": "POST",
                "data": {
                    input: {
                        start_date: start_date,
                        end_date: end_date,
                        expense_type: expense_type
                    }
                },
            },
            "footerCallback": function(row, data, start, end, display) {
                var api = this.api();

                // Remove the formatting to get integer data for summation
                var intVal = function(i) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '') * 1 :
                        typeof i === 'number' ?
                        i : 0;
                };

                subTotal = api
                    .column(4, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // Update footer
                $(api.column(4).footer()).html(
                    "&#x20B1; " + subTotal.toLocaleString('en-US', {
                        minimumFractionDigits: 2
                    })
                );

            },
            "columns": [{
                    "data": "expense_date"
                },
                {
                    "data": "expense",
                },
                {
                    "data": "reference_number",
                },
                {
                    "data": "supplier",
                },
                {
                    "data": "amount",
                    className: "text-right"
                }

            ]

        });
    }
</script>