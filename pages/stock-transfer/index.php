<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="row">
                <div class="col-12 col-xl-12 mb-4 mb-xl-0">
                    <h3 class="font-weight-bold">Stock Transfer</h3>
                    <h6 class="font-weight-normal mb-0">Manage stock transfer here</h6>
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
                                    <th>Date</th>
                                    <th>Reference</th>
                                    <th>Source Warehouse</th>
                                    <th>Destination Warehouse</th>
                                    <th>Encoded by</th>
                                    <th>Status</th>
                                    <th>Date Added</th>
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
<?php include 'modal_stock_transfer.php' ?>
<script type="text/javascript">
    function getEntries() {
        var start_date = $("#start_date").val();
        var end_date = $("#end_date").val();
        var param = "(stock_transfer_date >= '" + start_date + "' AND stock_transfer_date <= '" + end_date + "')";

        $("#dt_entries").DataTable().destroy();
        $("#dt_entries").DataTable({
            "processing": true,
            "order": [
                [3, 'desc']
            ],
            "ajax": {
                "url": "controllers/sql.php?c=" + route_settings.class_name + "&q=show",
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
                        return row.status == 'F' ? '' : "<input type='checkbox' value=" + row.stock_transfer_id + " class='dt_id' style='position: initial; opacity:1;'>";
                    }
                },
                {
                    "mRender": function(data, type, row) {
                        return "<center><button class='btn btn-primary btn-circle btn-sm' onclick='getEntryDetails2(" + row.stock_transfer_id + ")'><span class='ti ti-list'></span></button></center>";
                    }
                },
                {
                    "data": "stock_transfer_date"
                },
                {
                    "data": "reference_number"
                },
                {
                    "data": "source_warehouse"
                },
                {
                    "data": "destination_warehouse"
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
                    "data": "date_added"
                },
                {
                    "data": "date_last_modified"
                }
            ]
        });
    }


    function getEntries2() {
        var params = "stock_transfer_id = '" + $("#hidden_id_2").val() + "'";
        $("#dt_entries_2").DataTable().destroy();
        $("#dt_entries_2").DataTable({
            "processing": true,
            "ajax": {
                "url": "controllers/sql.php?c=" + route_settings.class_name + "&q=show_detail",
                "dataSrc": "data",
                "method": "POST",
                "data": {
                    input: {
                        param: params
                    }
                }
            },
            "columns": [{
                    "mRender": function(data, type, row) {
                        return "<input type='checkbox' value=" + row.jo_detail_id + " class='dt_id_2' style='position: initial; opacity:1;'>";
                    }
                },
                {
                    "data": "product"
                },
                {
                    "data": "qty"
                },
                {
                    "data": "cost"
                }
            ]
        });
    }

    function getProductCost() {
        var optionSelected = $("#product_id_2").find('option:selected').attr('product_cost');
        $("#cost").val(parseFloat(optionSelected).toFixed(2));
    }

    // function getDestination(){
    //     var source_warehouse_id = $("#source_warehouse_id").val();
    //     $('#destination_warehouse_id').select2().trigger('change');
    //     getSelectOption('Warehouses', 'destination_warehouse_id', 'warehouse_name_branch', "source_warehouse_id = '"+source_warehouse_id+"'");
    //     alert(source_warehouse_id);
    // }

    function getCurrentQty(){
        var product_id = $("#product_id").val();
        $.ajax({
          type: "POST",
          url: "controllers/sql.php?c=" + route_settings.class_name + "&q=get_qty",
          data: {
            input: {
                product_id:product_id
            }
          },
          success: function(data) {
            var json = JSON.parse(data);
            $("#current_qty").val(data);
          },
          error: function(jqXHR, textStatus, errorThrown) {
            errorLogger('Error:', textStatus, errorThrown);
          }
        });

    }

    function getDestination() {
        var source_warehouse_id = $("#source_warehouse_id").val();
        getSelectOption('Warehouses', 'destination_warehouse_id', 'warehouse_name_branch', "warehouse_id != "+source_warehouse_id);
    }

    $(document).ready(function() {
        schema();
        getEntries();
        getSelectOption('Warehouses', 'source_warehouse_id', 'warehouse_name', "branch_id = '" + current_branch_id + "'");
        getSelectOption('Products', 'product_id', 'product_name');
    });
</script>