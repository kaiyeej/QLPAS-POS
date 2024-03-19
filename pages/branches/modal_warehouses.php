<form method='POST' id='frm_submit_2' class="warehouse">
    <div class="modal fade" id="modalEntry2" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" style="margin-top: 50px;" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalLabel"><span class='fa fa-pen'></span> Add Entry</h4>
                </div>
                <div class="modal-body">
                    <input type="text" id="hidden_id_2" name="input[branch_id]">
                    <div class="form-group row">
                        <div class="col">
                            <label><strong>Warehouse Name</strong></label>
                            <div>
                                <input type="text" class="form-control" name="input[warehouse_name]" id="warehouse_name" autocomplete="off" placeholder="Warehouse Name" maxlength="50" required>
                            </div>
                        </div>
                    </div>
                    <hr />
                    <div class="table-responsive">
                        <table class="display expandable-table" id="dt_entries_2" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Warehouse</th>
                                    <th>Date Added</th>
                                    <th>Date Modified</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class='btn-group'>
                        <button type="submit" class="btn btn-primary btn-sm" id="btn_submit">Submit</button>
                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
    $("#frm_submit_2").submit(function(e) {
        e.preventDefault();

        $("#btn_submit").prop('disabled', true);
        $("#btn_submit").html("<span class='fa fa-spinner fa-spin'></span> Submitting ...");

        $.ajax({
            type: "POST",
            url: "controllers/sql.php?c=Warehouses&q=add",
            data: $("#frm_submit").serialize(),
            success: function(data) {
                var json = JSON.parse(data);
                if (json.data > 0) {
                    success_add();
                    getEntries2();
                } else if (json.data == -2) {
                    entry_already_exists();
                } else if (json.data == 'conflict') {
                    swal("Cannot proceed!", "Please check your effective dates!", "warning");
                } else {
                    failed_query(json);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                errorLogger('Error:', textStatus, errorThrown);
            }
        });
    });

    function getEntries2() {
        var hidden_id_2 = $("#hidden_id_2").val();
        var param = "branch_id = '" + hidden_id_2 + "'";
        $("#dt_entries_2").DataTable().destroy();
        $("#dt_entries_2").DataTable({
            "processing": true,
            "ajax": {
                "url": "controllers/sql.php?c=Warehouses&q=show",
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
                        return "<center><button class='btn btn-danger btn-circle btn-sm' onclick='removeWarehouse(" + row.warehouse_id + ")'><span class='ti ti-trash'></span></button></center>";
                    }
                },
                {
                    "data": "warehouse_name"
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

    function removeWarehouse(id) {
        swal({
                title: "Are you sure?",
                text: "You will not be able to recover these entries!",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel!",
                closeOnConfirm: false,
                closeOnCancel: false
            },
            function(isConfirm) {
                if (isConfirm) {
                    
                    $.ajax({
                        type: "POST",
                        url: "controllers/sql.php?c=Warehouses&q=remove",
                        data: {
                            input: {
                                id: id
                            }
                        },
                        success: function(data) {
                            getEntries2();
                            var json = JSON.parse(data);
                            console.log(json);
                            if (json.data == 1) {
                                success_delete();
                            } else {
                                failed_query(json);
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            errorLogger('Error:', textStatus, errorThrown);
                        }
                    });

                    $("#btn_delete").prop('disabled', true);

                } else {
                    swal("Cancelled", "Entries are safe :)", "error");
                }
            });
    }
</script>