<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="row">
                <div class="col-12 col-xl-12 mb-4 mb-xl-0">
                    <h3 class="font-weight-bold">Stock Withdrawal</h3>
                    <h6 class="font-weight-normal mb-0">Manage stock withdrawal here</h6>
                </div>
            </div>

            <div class="col-12 col-xl-12 card shadow mb-4">
                <div class="card-header py-3">
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
                                    <a href="#" class="btn btn-success btn-sm" style="padding: 10px;" onclick="addModal()">
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
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="display expandable-table" id="dt_entries" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th><input type='checkbox' onchange="checkAll(this, 'dt_id')"></th>
                                    <th></th>
                                    <th>Reference #</th>
                                    <th>Delivery #</th>
                                    <th>Customer</th>
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
<?php require_once 'modal_stock_withdrawal.php'; ?>
<?php require_once 'modal_print.php'; ?>
<script type="text/javascript">
    
    function addModalWithdraw() {
        document.getElementById("frm_submit").reset();

        generateReference(route_settings.class_name);
        getSelectOption('Sales', 'sales_id', 'withdrawal_ref', "withdrawal_status = 1 AND status='F'");

        $("#hidden_id").val(0);
        $("#modalLabel").html("<span class='fa fa-pen'></span> Add Entry");
        $("#modalEntry").modal('show');
    }

   
    function getEntries() {
        var  start_date = $("#start_date").val();
        var end_date = $("#end_date").val();
        var branch_id = "<?=$_SESSION['branch_id'];?>"
        var param = "(withdrawal_date >= '"+start_date+"' AND withdrawal_date <= '"+end_date+"') AND branch_id = '"+branch_id+"'";

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
                        return row.status != 'F' ? "<input type='checkbox' value=" + row.withdrawal_id + " class='dt_id' style='position: initial; opacity:1;'>" : "" ;
                    }
                },
                {
                    "mRender": function(data, type, row) {
                        return "<center><button class='btn btn-primary btn-circle btn-sm' onclick='getEntryDetails2(" + row.withdrawal_id + ")'><span class='ti ti-list'></span></button><button onclick='printRecord("+ row.withdrawal_id +")' class='btn btn-secondary btn-circle btn-sm'><span class='ti ti-printer'></span></button></center>";
                    }
                },
                {
                    "data": "reference_number"
                },
                {
                    "data": "sales_ref"
                },
                {
                    "data": "customer"
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
                    "data": "withdrawal_date"
                },
                {
                    "data": "date_last_modified"
                }
            ]
        });
    }

    function getEntries2() {
        var param = "withdrawal_id = '" + $("#hidden_id_2").val() + "'";
        $("#dt_entries_2").DataTable().destroy();
        $("#dt_entries_2").DataTable({
            "processing": true,
            "searching": false,
            "paging": false,
            "ordering": false,
            "info": false,
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
            "columns": [{
                    "mRender": function(data, type, row) {
                        return "<input type='checkbox' style='display:none;' value=" + row.sw_detail_id + " class='dt_id_2' style='position: initial; opacity:1;'>"+row.product;
                    }
                },
                {
                    "data":"remaining_qty"
                },
                {
                    "mRender": function(data, type, row) {
                        if(row.qty > 0){
                            var val_qty = row.qty;
                        }else{
                            var val_qty = row.remaining_qty;
                        }

                        if(row.status == "F" ){
                            var stat = "disabled";
                        }else{
                            var stat = "";
                        }

                        return "<input "+stat+" id='r_"+ row.sw_detail_id +"' type='number' value='"+val_qty+"' step='.01' max='"+row.remaining_qty+"' min='0' name='r_qty[]' class='form-control form-control-sm'><input type='hidden' name='r_id[]' value='"+row.sw_detail_id+"'>";
                    }   
                }
            ]
        });
    }

    $("#frm_release").submit(function(e) {
        e.preventDefault();

        var r_qty = $("input[name='r_qty[]']").map(function(){return $(this).val();}).get();
        var r_id = $("input[name='r_id[]']").map(function(){return $(this).val();}).get();

        $.ajax({
            type: "POST",
            url: "controllers/sql.php?c=" + route_settings.class_name + "&q=save_detail",
            data: {
                input: {
                    r_qty:r_qty,
                    r_id:r_id
                }
            },
            success: function(data) {
                swal("Success!", "Successfully saved entry! Finish Transaction to release stocks.", "success");
            }
        });
    });

    $(document).ready(function() {
        getEntries();
        getSelectOption('Sales', 'sales_id', 'withdrawal_ref', "withdrawal_status = 1 AND status='F'");
    });

</script>