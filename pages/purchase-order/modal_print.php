 <div class="modal fade bd-example-modal-lg" id="modalPrint" aria-labelledby="myModalLabel">
     <div class="modal-dialog  modal-lg" style="margin-top: 50px;" role="document">
         <div class="modal-content">
             <div class="modal-header">
                 <h4 class="modal-title" id="modalLabel"><span class='fa fa-print'></span> Print Record</h4>
             </div>
             <div class="modal-body" style="padding: 15px;">
                 <input type="hidden" id="hidden_id_po" name="input[po_id]">
                 <div class="col-12" id="print_canvas">
                     <center>
                        <strong>
                            <h4 class="report-header"><span id="company_name_label"></span></h4>
                            <h6 class="report-header"><span id="company_address_label" style="word-wrap: break-word;"></span></h6>
                            <h5>Purchase Order</h5>
                         </strong>
                    </center>
                     <strong>Supplier Name: </strong><span id="supplier_name_span"></span><br>
                     <strong>Reference Number: </strong><span id="reference_number_span"></span><br>
                     <strong>Date: </strong><span id="po_date_span"></span><br>
                     <strong>Remarks: </strong><span id="remarks_span"></span>
                     <div class="table-responsive">
                         <table class="table table-bordered mb-0" id="tbl_print_details">
                             <thead>
                                 <tr>
                                     <th>PRODUCT</th>
                                     <th>QUANTITY</th>
                                     <th>PRICE</th>
                                     <th>AMOUNT</th>

                                 </tr>
                             </thead>
                             <tbody id="tb_id">
                             </tbody>
                         </table>
                     </div>
                     <br style="clear: both;" />
                 </div>
             </div>
             <div class="modal-footer">
                 <button type="button" onclick="print_report('print_canvas')" class="btn btn-primary ml-1 btn-sm">
                     <i class="bx bx-check d-block d-sm-none"></i>
                     <span class="d-none d-sm-block"><span class='ti ti-printer'></span> Printer</span>
                 </button>
             </div>
         </div>
     </div>
 </div>
 <script type="text/javascript">
    function printRecord(id) {
        $("#tb_id").html("");
        $("#modalPrint").modal('show');

        $("#company_name_label").html(company_profile.company_name);
        $("#company_address_label").html(company_profile.company_address);

        $.ajax({
            type: 'POST',
            url: "controllers/sql.php?c=" + route_settings.class_name + "&q=getHeader",
            data: {
                id: id
            },
            success: function(data) {
                console.log(data);
                var json = JSON.parse(data);

                $("#supplier_name_span").html(json.data[0].supplier_name);
                $("#reference_number_span").html(json.data[0].reference_number);
                $("#po_date_span").html(json.data[0].po_date_mod);
                $("#remarks_span").html(json.data[0].remarks);

                getPrintDetails(json.data[0].po_id);
            }
        });
    }

    function getPrintDetails(id) {

        $.ajax({
            type: 'POST',
            url: "controllers/sql.php?c=" + route_settings.class_name + "&q=getPrintDetails",
            data: {
                id: id
            },
            success: function(data) {
                var json = JSON.parse(data);
                var arr_count = json.data.length;
                var i = 0;
                while (i < arr_count) {
                    console.log(json.data[i]);
                    $("#tb_id").append('<tr>' +
                        '<td>' + json.data[i].product_name + '</td>' +
                        '<td>' + json.data[i].qty + '</td>' +
                        '<td>' + json.data[i].supplier_price + '</td>' +
                        '<td>' + json.data[i].qty * json.data[i].supplier_price + '</td>' +
                        '</tr>');
                    i++;
                }
            }
        });
    }
 </script>