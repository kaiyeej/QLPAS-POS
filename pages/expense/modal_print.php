 <div class="modal fade bd-example-modal-lg" id="modalPrint" aria-labelledby="myModalLabel">
     <div class="modal-dialog  modal-lg" style="margin-top: 50px;" role="document">
         <div class="modal-content">
             <div class="modal-header">
                 <h4 class="modal-title" id="modalLabel"><span class='fa fa-print'></span> Print Record</h4>
             </div>
             <div class="modal-body" style="padding: 15px;">
                 <input type="hidden" id="hidden_id_expense" name="input[expense_id]">
                 <div class="col-12" id="print_canvas">
                     <center>
                         <strong>
                             <h4 class="report-header"><span id="company_name_label"></span></h4>
                             <h6 class="report-header"><span id="company_address_label" style="word-wrap: break-word;"></span></h6>
                             <h5>Expenses</h5>
                         </strong>
                     </center>
                     <strong>Reference Number: </strong><span id="reference_number_span"></span><br>
                     <!-- <strong>Type: </strong><span id="expense_type_span"></span><br> -->
                     <strong>Date: </strong><span id="expense_date_span"></span><br>
                     <strong>Remarks: </strong><span id="remarks_span"></span>
                     <div class="table-responsive">
                         <table class="table table-bordered mb-0" id="tbl_print_details">
                             <thead>
                                 <tr>
                                     <th>EXPENSE</th>
                                     <th>SUPPLIER</th>
                                     <th>INVOICE</th>
                                     <th>DESCRIPTION</th>
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
                 <button type="button" onclick="print_report('print_canvas')" class="btn btn-primary ml-1">
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

                 $("#reference_number_span").html(json.data[0].reference_number);
                //  $("#expense_type_span").html(json.data[0].expense_type);
                 $("#expense_date_span").html(json.data[0].expense_date);
                 $("#remarks_span").html(json.data[0].remarks);

                 getPrintDetails(json.data[0].expense_id);
             }
         });
     }

     function getPrintDetails(id) {
        var params = "expense_id = '" +id + "'";
         $.ajax({
             type: 'POST',
             url: "controllers/sql.php?c=" + route_settings.class_name + "&q=show_detail",
             data: {
                 input: {
                     param: params
                 }
             },
             success: function(data) {
                 var json = JSON.parse(data);
                 var arr_count = json.data.length;
                 var i = 0;
                 while (i < arr_count) {
                     console.log(json.data[i]);
                     $("#tb_id").append('<tr>' +
                         '<td>' + json.data[i].expense_category + '</td>' +
                         '<td>' + json.data[i].supplier + '</td>' +
                         '<td>' + json.data[i].invoice_no + '</td>' +
                         '<td>' + json.data[i].description + '</td>' +
                         '<td>' + json.data[i].amount + '</td>' +
                         '</tr>');
                     i++;
                 }
             }
         });
     }
 </script>