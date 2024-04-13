<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="row">
                <div class="col-12 col-xl-12 mb-4 mb-xl-0">
                    <h3 class="font-weight-bold">Product Conversion</h3>
                    <h6 class="font-weight-normal mb-0">Convert your product here</h6>
                </div>
            </div>

            <div class="col-12 col-xl-12 card shadow mb-4">
                <div class="card-header py-3">
                    <!--<h6 class="m-0 font-weight-bold text-primary">List of Users</h6>-->
                    <div class="btn-group pull-right">
                        <a href="#" class="btn btn-primary btn-sm btn-icon-split" onclick="addModal()">
                            <span class="icon text-white-50">
                                <i class="fas fa-plus"></i>
                            </span>
                            <span class="text">Add Entry</span>
                        </a>
                        <a href="#" class="btn btn-danger btn-sm btn-icon-split" onclick='deleteEntry()' id='btn_delete'>
                            <span class="icon text-white-50">
                                <i class="fas fa-trash"></i>
                            </span>
                            <span class="text">Delete Entry</span>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="display expandable-table" id="dt_entries" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th><input type='checkbox' onchange="checkAll(this, 'dt_id')"></th>
                                    <th></th>
                                    <th>Reference</th>
                                    <th>Conversion Date</th>
                                    <th>Remarks</th>
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
<?php include 'modal_product_conversion.php' ?>
<script type="text/javascript">
    function getEntries() {
        var branch_id = "<?=$_SESSION['branch_id']?>";

        var param = "branch_id = '"+branch_id+"'";
        $("#dt_entries").DataTable().destroy();
        $("#dt_entries").DataTable({
            "processing": true,
            "ajax": {
                "url": "controllers/sql.php?c=" + route_settings.class_name + "&q=show",
                "dataSrc": "data",
                "type": "POST",
                "data" : {
                    input:{
                        param : param
                    }
                }
            },
            "columns": [{
                    "mRender": function(data, type, row) {
                        return row.status == 'F' ? "" : "<input type='checkbox' value=" + row.conversion_id + " class='dt_id' style='position: initial; opacity:1;'>";
                    }
                },
                {
                    "mRender": function(data, type, row) {
                        return "<center><button class='btn btn-primary btn-circle btn-sm' onclick='getEntryDetails2(" + row.conversion_id + ")'><span class='ti ti-list'></span></button></center>";
                    }
                },
                {
                    "data": "reference_number"
                },
                {
                    "data": "conversion_date"
                },
                {
                    "data": "remarks"
                },
                {
                    "mRender": function(data, type, row) {
                        return row.status == 'F' ? "<label class='badge badge-success'> Finish</label>" : "<label class='badge badge-danger'> Saved</label>";
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
        var params = "conversion_id = '" + $("#hidden_id_2").val() + "'";
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
            "columnDefs": [{
                "targets": [2, 3, 4],
                "render": $.fn.dataTable.render.number(',', '.', 2, ''),
                "className": 'dt-body-right'
            }],
            "columns": [{
                    "mRender": function(data, type, row) {
                        return "<input type='checkbox' value=" + row.conversion_detail_id + " class='dt_id_2' style='position: initial; opacity:1;'>";
                    }
                },
                {
                    "data": "original_product"
                },
                {
                    "data": "original_qty"
                },
                {
                    "data": "converted_product"
                },
                {
                    "data": "converted_qty"
                },
            ]
        });
    }

    function copyProductSelect() {
        var product_select = $("#product_id").html();
        $("#original_product_id").html(product_select);
        $("#converted_product_id").html(product_select);
    }

    $(document).ready(function() {
        schema();
        getEntries();
        getSelectOption('Products', 'product_id', 'product_name', 'product_id > 0 ORDER BY product_name ASC');

        myTimeout = setTimeout(copyProductSelect, 1000);
    });
</script>