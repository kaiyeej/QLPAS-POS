<form method='POST' id='frm_submit_price' class="banks">
    <div class="modal fade" id="modalProductPrice" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-md" style="margin-top: 50px; max-width:70%" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalLabel"><span class='fa fa-pen'></span> Set Warehouse Product Prices</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="product_price_id" name="input[product_price_id]">
                    

                    <div class="table-responsive">
                        <table class="display expandable-table" id="dt_prices" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Warehouse</th>
                                    <th style="width: 200px;">Retail</th>
                                    <th style="width: 200px;">Wholesale</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>


                </div>
                <div class="modal-footer">
                    <div class='btn-group'>
                        <button type="submit" class="btn btn-primary" id="btn_submit">Save</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    function getWarehouses() {
        var product_id = $("#product_price_id").val();
        
        $("#dt_prices").DataTable().destroy();
        $("#dt_prices").DataTable({
            "processing": true,
            "bPaginate": false,
            "ajax": {
                "type":"POST",
                "url": "controllers/sql.php?c=Products&q=showProductPrices",
                "dataSrc": "data",
                "data": {
                    input:{
                        product_id: product_id
                    }
                },
            },
            "columns": [
                {
                    "data": "warehouse_name"
                },
                {
                    "mRender": function(data, type, row) {
                        return "<input type='number' class='form-control' value='"+row.price_retail+"' name='warehouse_id["+row.warehouse_id+"][price_retail]' min='0'>";
                    }
                },
                {
                    "mRender": function(data, type, row) {
                        return "<input type='number' class='form-control' value='"+row.price_wholesale+"' name='warehouse_id["+row.warehouse_id+"][price_wholesale]' min='0'>";
                    }
                }
            ]
        });

    }
    
    $("#frm_submit_price").submit(function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "controllers/sql.php?c=Products&q=updateProductPrices",
            data: $("#frm_submit_price").serialize(),
            success: function(result) {
                var res = JSON.parse(result);
                if(res.data === 1){
                    success_update();
                }else{
                    failed_query();
                }
            },
        });
    });
</script>