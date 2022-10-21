<div class="content-wrapper">
    <div class="row">
        <div class="col-12 col-xl-12 mb-4 mb-xl-0">
            <h3 class="font-weight-bold">Settings</h3>
            <h6 class="font-weight-normal mb-0">Update comany settings here.</h6>
        </div>
    </div>
    <br>
    <div class="col-12 col-xl-12 card shadow mb-4">
        <ul id="tabs" class="nav nav-tabs">
            <li class="nav-item"><a href="" data-target="#company-profile" data-toggle="tab" class="nav-link small text-uppercase active">Company Profile</a></li>
            <li class="nav-item"><a href="" data-target="#access-codes" data-toggle="tab" class="nav-link small text-uppercase">Access Codes</a></li>
        </ul>
        <br>
        <div id="tabsContent" class="tab-content">
            <div id="company-profile" class="tab-pane fade active show">
                <div class="col-6">
                    <h4 class="card-title">Company Profile</h4>
                    <p class="card-description">
                        Set your company profile here.
                    </p>
                    <form class="forms-sample" id="frm_submit_profile">
                        <div class="form-group">
                            <label for="company_name">Company Name</label>
                            <input name="input[company_name]" type="text" class="form-control input-item" id="company_name" placeholder="Company Name" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="company_address">Company Address</label>
                            <input name="input[company_address]" type="text" class="form-control input-item" id="company_address" placeholder="Company Address" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="print_header">Customize Header Text (POS Print)</label>
                            <textarea name="input[print_header]" class="form-control input-item" id="print_header"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="print_footer">Customize Footer Text (POS Print)</label>
                            <textarea name="input[print_footer]" class="form-control input-item" id="print_footer"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary mr-2" id="btn_submit_2">Submit</button>
                    </form>
                </div>
            </div>

            <div id="access-codes" class="tab-pane fade">
                <form class="forms-sample" id="frm_submit">
                    <div class="col-12">
                        <h4 class="card-title">Access Codes</h4>
                        <p class="card-description">
                            Set access codes for each module.
                        </p>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <input type="hidden" id="hidden_id">
                            <div class="form-group">
                                <label for="module_discount">Discount</label>
                                <input name="input[module_discount]" type="password" class="form-control input-item" id="module_discount" placeholder="Discount Module">
                            </div>
                            <div class="form-group">
                                <label for="module_cancel">Cancel</label>
                                <input name="input[module_cancel]" type="password" class="form-control input-item" id="module_cancel" placeholder="Cancelling Module">
                            </div>
                            <div class="form-group">
                                <label for="module_delete">Delete</label>
                                <input name="input[module_delete]" type="password" class="form-control input-item" id="module_delete" placeholder="Delete Module">
                            </div>
                            <button type="submit" class="btn btn-primary mr-2" id="btn_submit">Submit</button>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="module_add_customer">Add Customer</label>
                                <input name="input[module_add_customer]" type="password" class="form-control input-item" id="module_add_customer" placeholder="Add Customer Module">
                            </div>
                            <div class="form-group">
                                <label for="module_change_payment_type">Change Payment Type</label>
                                <input name="input[module_change_payment_type]" type="password" class="form-control input-item" id="module_change_payment_type" placeholder="Cancelling Module">
                            </div>
                            <div class="form-group">
                                <label for="module_remove_online_payment">Remove Online Payment</label>
                                <input name="input[module_remove_online_payment]" type="password" class="form-control input-item" id="module_remove_online_payment" placeholder="Delete Module">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
<script>
    getEntries();
    $("#frm_submit_profile").submit(function(e) {
        e.preventDefault();

        $("#btn_submit_2").prop('disabled', true);
        $("#btn_submit_2").html("<span class='fa fa-spinner fa-spin'></span> Submitting ...");

        $.ajax({
            type: "POST",
            url: "controllers/sql.php?c=Settings&q=addProfile",
            data: $("#frm_submit_profile").serialize(),
            success: function(data) {
                var json = JSON.parse(data);

                if (json.data == 1) {
                    success_update();
                } else {
                    failed_query(json);
                }

                $("#btn_submit_2").prop('disabled', false);
                $("#btn_submit_2").html("<span class='fa fa-check-circle'></span> Submit");
            }
        });
    });

    function getEntries() {
        $.ajax({
            type: "POST",
            url: "controllers/sql.php?c=Settings&q=view",
            success: function(data) {
                var jsonParse = JSON.parse(data);
                const json = jsonParse.data;
                if (json != null) {

                    $('.input-item').map(function() {
                        const id_name = this.id;
                        this.value = json[id_name];
                    });
                }
            }
        });
    }
</script>