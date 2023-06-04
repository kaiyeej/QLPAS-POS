<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="row">
                <div class="col-12 col-xl-12 mb-4 mb-xl-0">
                    <h3 class="font-weight-bold">Debit Memo</h3>
                    <h6 class="font-weight-normal mb-0">Manage debit memo here</h6>
                </div>
            </div>

            <div class="col-12 col-xl-12 card shadow mb-4">
                <div class="card-header py-3">
                    <!--<h6 class="m-0 font-weight-bold text-primary">List of Users</h6>-->
                    
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col">
                            <label><strong>Start Date</strong></label>
                            <div>
                                <input type="date" required class="form-control form-control-sm" id="start_date" value="<?php echo date('Y-m-01', strtotime(date("Y-m-d"))); ?>" name="input[start_date]">
                            </div>
                        </div>
                        <div class="col">
                            <label><strong>End Date</strong></label>
                            <div>
                                <input type="date" required class="form-control form-control-sm" id="end_date" value="<?php echo date('Y-m-t', strtotime(date("Y-m-d"))) ?>" name="input[end_date]">
                            </div>
                        </div>
                        <div class="col">
                            <label>&nbsp;</label>
                            <div>
                                
                                <div class="btn-group">
                                    <a href="#" class="btn btn-primary btn-sm" style="padding: 10px;" onclick="getEntries()">
                                        <span class="text"> Generate</span>
                                    </a>
                                    <a href="#" class="btn btn-success btn-sm" style="padding: 10px;" onclick="addModalPO()">
                                        <span class="text">Add Entry</span>
                                    </a>
                                    <a href="#" class="btn btn-danger btn-sm" style="padding: 10px;" onclick='deleteEntry()' id='btn_delete'>
                                        <span class="text">Delete Entry</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="table-responsive">
                        <table class="display expandable-table" id="dt_entries" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th><input type='checkbox' onchange="checkAll(this, 'dt_id')"></th>
                                    <th></th>
                                    <th>Reference #</th>
                                    <th>Account</th>
                                    <th>Amount</th>
                                    <th>Encoded by</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Date Modified</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once 'modal_debit_memo.php'; ?>
<?php require_once 'modal_print.php'; ?>
<script type="text/javascript">
    
    function addModalPO() {
        document.getElementById("frm_submit").reset();

        generateReference(route_settings.class_name);

        $("#hidden_id").val(0);
        $("#modalLabel").html("<span class='fa fa-pen'></span> Add Entry");
        $("#modalEntry").modal('show');
    }

   
    function getEntries() {
        var start_date = $("#start_date").val();
        var end_date = $("#end_date").val();
        var param = "(memo_date >= '"+start_date+"' AND memo_date <= '"+end_date+"')";
        $("#dt_entries").DataTable().destroy();
        $("#dt_entries").DataTable({
            "processing": true,
            "order": [[ 2, 'desc' ]],
            "ajax": {
                "url": "controllers/sql.php?c=" + route_settings.class_name + "&q=show",
                "dataSrc": "data",
                "type": "POST",
                "data": {
                    input: {
                        param:param
                    }
                }
            },
            "columns": [{
                    "mRender": function(data, type, row) {
                        return row.status != 'F' ? "<input type='checkbox' value=" + row.dm_id + " class='dt_id' style='position: initial; opacity:1;'>" : "";
                    }
                },
                {
                    "mRender": function(data, type, row) {
                        return "<center><button class='btn btn-primary btn-circle btn-sm' onclick='getEntryDetails2(" + row.dm_id + ")'><span class='ti ti-list'></span></button><button onclick='printRecord("+ row.dm_id +")' class='btn btn-secondary btn-circle btn-sm'><span class='ti ti-printer'></span></button></center>";
                    }
                },
                {
                    "data": "reference_number"
                },
                {
                    "data": "account"
                },
                {
                    "data": "total"
                },
                {
                    "data": "encoded_name"
                },
                {
                    "mRender": function(data, type, row) {
                        return row.status == 'F' ? "<span class='badge badge-success'>Finish</span>" : "<span class='badge badge-danger'>Saved</span>";
                    }   
                },
                {
                    "data": "memo_date"
                },
                {
                    "data": "date_last_modified"
                }
            ]
        });
    }

    function getEntries2() {
        var hidden_id_2 = $("#hidden_id_2").val();
        var param = "dm_id = '" + hidden_id_2 + "'";
        $("#dt_entries_2").DataTable().destroy();
        $("#dt_entries_2").DataTable({
            "processing": true,
            "order": [[ 3, 'desc' ]],
            "ajax": {
                "url": "controllers/sql.php?c=" + route_settings.class_name + "&q=show_detail",
                "dataSrc": "data",
                "type": "POST",
                "data": {
                    input: {
                        param: param
                    }
                }
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

                // Total over all pages
                total = api
                    .column(3)
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // Total over this page
                pageTotal = api
                    .column(3, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // Update footer
                $(api.column(3).footer()).html(
                    "&#x20B1; " + this.fnSettings().fnFormatNumber(parseFloat(parseFloat(total).toFixed(2)))
                );
            },
            "columns": [{
                    "mRender": function(data, type, row) {
                        return "<input type='checkbox' value=" + row.dmd_id + " class='dt_id_2' style='position: initial; opacity:1;'>";
                    }
                },
                {
                    "data": "reference"
                },
                {
                    "data": "description"
                },
                {
                    "data": "amount"
                },
            ]
        });
    }

    function referenceCounter() {
        var reference_id = $("#reference_id").val();
        var id = $("#hidden_id_2").val();
        $.ajax({
            type: "POST",
            url: "controllers/sql.php?c=" + route_settings.class_name + "&q=ref_checker",
            data: {
                input: {
                    reference_id: reference_id,
                    id:id
                }
            },
            success: function(data) {
                var json = JSON.parse(data);
                if (json.data > 0) {
                    $("#btn_submit_2").prop('disabled', false);
                } else {
                    $("#btn_submit_2").prop('disabled', true);
                }
            }
        });
    }

    function getAccount(){
        var memo_type = $("#memo_type").val();
        if(memo_type == "AP"){
            getSelectMutiID('Suppliers', 'account_id', 'supplier_name', '', [], 'supplier_id');
        }else{
            getSelectMutiID('Customers', 'account_id', 'customer_name', '', [], 'customer_id');
        }
    }

    $(document).ready(function() {
        schema();
        getEntries();
    });

</script>