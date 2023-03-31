<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="col-md-12 grid-margin">
                <div class="row">
                    <div class="col-12 col-xl-12 mb-4 mb-xl-0">
                        <h3 class="font-weight-bold">Stock Releasal Report</h3>
                        <h6 class="font-weight-normal mb-0">Generate stock releasal here</h6>
                    </div>
                </div>
                <br>
                <br>
                <div class="card shadow mb-4">
                    <ul id="tabs" class="nav nav-tabs">
                        <li class="nav-item"><a href="" data-target="#per_transaction" data-toggle="tab" class="nav-link small text-uppercase active">Per Transaction</a></li>
                        <li class="nav-item"><a href="" data-target="#per_item" data-toggle="tab" class="nav-link small text-uppercase">Per Item</a></li>
                    </ul>
                    <br>
                    <div id="tabsContent" class="tab-content">
                        <div id="per_transaction" class="tab-pane fade active show">
                            <h5>Per Transaction Report</h5>
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <form id='frm_generate'>
                                        <div class="form-group row">
                                            <div class="col">
                                                <label><strong>Customer</strong></label>
                                                <div>
                                                    <select class="form-control form-control-sm select2" id="customer_id" name="input[customer_id]">
                                                        <option class="-1">&mdash; All &mdash; </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <label><strong>Product</strong></label>
                                                <div>
                                                    <select class="form-control form-control-sm select2" id="product_id" name="input[product_id]">
                                                        <option class="-1">&mdash; All &mdash; </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <label>&nbsp;</label>
                                                <div>
                                                    <div class="btn-group pull-right">
                                                        <button type="submit" id="btn_generate" class="btn btn-primary btn-sm btn-icon-split">
                                                            <span class="icon text-white-50">
                                                                <i class="fas fa-plus"></i>
                                                            </span>
                                                            <span class="text">Generate</span>
                                                        </button>
                                                        <button type="button" onclick="exportTableToExcel(this,'dt_entries','Stock-Releasal-Report')" class="btn btn-success btn-sm btn-icon-split">
                                                            <span class="icon">
                                                                <i class="ti ti-cloud-down"></i>
                                                            </span>
                                                            <span class="text"> Export</span>
                                                        </button>
                                                        <button onclick="print_report('report_container')" type="button" class="btn btn-info btn-sm btn-icon-split">
                                                            <span class="icon text-white-50">
                                                                <i class="fas fa-trash"></i>
                                                            </span>
                                                            <span class="text">Print</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div id="report_container" class="card-body">
                                    <center>
                                        <h4 class="report-header"><span id="company_name_label"></span></h4>
                                        <h6 class="report-header"><span id="company_address_label" style="word-wrap: break-word;"></span></h6>
                                        <h5 class="report-header">Stock Releasal Report</h5><br>
                                    </center>
                                    <div class="table-responsive" id="dt_entries">

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="per_item" class="tab-pane fade">
                            <h5>Per Item Stock Releasal Report</h5>
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <form id='frm_generate_item'>
                                        <div class="form-group row">
                                            <div class="col">
                                                <label><strong>Product</strong></label>
                                                <div>
                                                    <select class="form-control form-control-sm select2" id="product_id_2" name="input[product_id_2]">
                                                        <option class="-1">&mdash; All &mdash; </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <label>&nbsp;</label>
                                                <div>
                                                    <div class="btn-group pull-right">
                                                        <button type="submit" id="btn_generate" class="btn btn-primary btn-sm btn-icon-split">
                                                            <span class="icon text-white-50">
                                                                <i class="fas fa-plus"></i>
                                                            </span>
                                                            <span class="text">Generate</span>
                                                        </button>
                                                        <button type="button" onclick="exportTableToExcel(this,'dt_entries','Stock-Releasal-Report')" class="btn btn-success btn-sm btn-icon-split">
                                                            <span class="icon">
                                                                <i class="ti ti-cloud-down"></i>
                                                            </span>
                                                            <span class="text"> Export</span>
                                                        </button>
                                                        <button onclick="print_report('report_container')" type="button" class="btn btn-info btn-sm btn-icon-split">
                                                            <span class="icon text-white-50">
                                                                <i class="fas fa-trash"></i>
                                                            </span>
                                                            <span class="text">Print</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div id="report_container" class="card-body">
                                    <center>
                                        <h4 class="report-header"><span id="company_name_label"></span></h4>
                                        <h6 class="report-header"><span id="company_address_label" style="word-wrap: break-word;"></span></h6>
                                        <h5 class="report-header">Stock Releasal Report</h5><br>
                                    </center>
                                    <div class="table-responsive" id="dt_entries_item">
                                    <thead>
                                        <tr>
                                            <th>ITEM</th>
                                            <th style="text-align:right">QTY</th>
                                            <th style="text-align:right">AMOUNT</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="1" style="text-align:right">Total:</th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>


        </div>
    </div>
</div>
<script type="text/javascript">
    $("#frm_generate").submit(function(e) {
        e.preventDefault();

        getReport();
    });

    $("#frm_generate_item").submit(function(e) {
        e.preventDefault();

        getReportItem();
    });

    function print_report(container, title) {

        var printContents = document.getElementById("" + container + "").innerHTML;

        //alert(printContents);
        var mywindow = window.open('', 'PRINT', 'height=400,width=600');

        mywindow.document.write('<html><head><title>' + document.title + '</title>');
        mywindow.document.write('<link href="../assets/css/bootstrap.min.css" rel="stylesheet">');
        mywindow.document.write('</head><body>');

        mywindow.document.write("<div class='container-fluid' id='print_report_header' style='margin-bottom: 20px;'></div>");
        mywindow.document.write("<div class='container-fluid' id='report_content'></div>");
        //mywindow.document.write(document.getElementById(elem).innerHTML);

        mywindow.document.write('</body></html>');

        mywindow.document.getElementById("report_content").innerHTML = printContents;

        mywindow.document.close(); // necessary for IE >= 10

        mywindow.onload = function() { // necessary if the div contain images

            mywindow.focus(); // necessary for IE >= 10
            mywindow.print();
            mywindow.close();

        }

        return true;
    }


    function getReport() {
        var customer_id = $("#customer_id").val();
        var product_id = $("#product_id").val();

        $("#dt_entries").html("<center><h3><span class='fa fa-spinner fa-spin'></span> Loading ...</h3></center>");

        $.ajax({
            type: "POST",
            url: "controllers/sql.php?c=" + route_settings.class_name + "&q=view",
            data: {
                input: {
                    customer_id: customer_id,
                    product_id: product_id
                }
            },
            success: function(data) {
                $("#dt_entries").html(data);
            }
        });
    }

    function getReportItem() {
        var product_id = $("#product_id").val();

        $("#dt_entries_item").html("<center><h3><span class='fa fa-spinner fa-spin'></span> Loading ...</h3></center>");

        $.ajax({
            type: "POST",
            url: "controllers/sql.php?c=" + route_settings.class_name + "&q=per_item",
            data: {
                input: {
                    product_id: product_id
                }
            },
            success: function(data) {
                $("#dt_entries_item").html(data);
            }
        });
    }

    $(document).ready(function() {
        $("#company_name_label").html(company_profile.company_name);
        $("#company_address_label").html(company_profile.company_address);
        getSelectOption('Products', 'product_id', 'product_name', '', [], -1, 'All');
        getSelectOption('Products', 'product_id_2', 'product_name', '', [], -1, 'All', 1);
        getSelectOption('Customers', 'customer_id', 'customer_name', '', [], -1, 'All');
    });
</script>