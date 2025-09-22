
/*===========================================================================
*
*  IMAGE - PROCESS DOCUMENT (RAW TEXT) RESULTS
*
*============================================================================*/

function processImageDetectText(data) {

    "use strict";

    ctx.clearRect(0, 0, canvas.width, canvas.height);

    var img = document.getElementById("image");

    var imgWidth = img.width;
    var imgHeight = img.height;

    /* FOR DATA DOWNLOAD*/
    rawJson = data;

     $("<div class='no-analysis' />").html("<div>FORM Analysis is Not Enabled</div><div>If Image contains a FORM : Enable Checkbox and Re-Upload Image</div>").appendTo("#forms");
     $("<div class='no-analysis' />").html("<div>TABLE Analysis is Not Enabled</div><div>If Image contains a TABLE : Enable Checkbox and Re-Upload Image</div>").appendTo("#tables");
     $("<div class='no-analysis' />").html("<div>Receipt Analysis is Not Enabled</div><div>If Image contains a Receipt : Enable Checkbox and Re-Upload Image</div>").appendTo("#receipt-summary");
     $("<div class='no-analysis' />").html("<div>Receipt Analysis is Not Enabled</div><div>If Image contains a Receipt : Enable Checkbox and Re-Upload Image</div>").appendTo("#receipt-items");
  
    if (data === undefined || data.length == 0) {
      
        $("<div class='text' />").html("<div>No text was found</div>").appendTo("#raw-text");

    } else {

        /* FOR DATA DOWNLOAD*/
        for( var i = 0; i < data.length; i++ ) {

            rawText.push([]);

        }


        for (var i = 0; i < data.length; i++) {    
            
            if(data[i].BlockType == 'LINE') {

                $("<div class='text inline-text'/>").html("<span data-toggle='tooltip' data-placement='top' title='Confidence: " + data[i].Confidence.toFixed(2) + "%'>" + data[i].Text + "</span>").appendTo("#raw-text");

                var x = data[i].Geometry.BoundingBox.Left * imgWidth;;
                var y = data[i].Geometry.BoundingBox.Top * imgHeight;
                var width = data[i].Geometry.BoundingBox.Width * imgWidth;
                var height = data[i].Geometry.BoundingBox.Height * imgHeight;
                    
                drawBoundingBox(x, y, width, height);

                /* FOR DATA DOWNLOAD*/
                rawText[i].push(data[i].Text);  

            }  
        }
     }         
}



/*===========================================================================
*
*  IMAGE - PROCESS DOCUMENT (FORMS & TABLES) RESULTS
*
*============================================================================*/

function processImageAnalyzeText(data, type) { 

   "use strict";

    ctx.clearRect(0, 0, canvas.width, canvas.height);

    var img = document.getElementById("image");

    var imgWidth = img.width;
    var imgHeight = img.height;
    
    /* LOCAL VARIABLES*/
    var key_value_pair = {};
    var key_map = {};
    var value_map = {};
    var block_map = {};
    var table_blocks_map = {};
    var table_blocks = [];

    /* FOR DATA DOWNLOAD*/
    rawJson = data;

    $("<div class='no-analysis' />").html("<div>Receipt Analysis is Not Enabled</div><div>If Image contains a Receipt : Enable Checkbox and Re-Upload Image</div>").appendTo("#receipt-summary");
    $("<div class='no-analysis' />").html("<div>Receipt Analysis is Not Enabled</div><div>If Image contains a Receipt : Enable Checkbox and Re-Upload Image</div>").appendTo("#receipt-items");

    if (data === undefined || data.length == 0) {
      
        $("<div class='text' />").html("<div>No text was found</div>").appendTo("#raw-text");
        $("<div class='text' />").html("<div>No forms were found</div>").appendTo("#forms");
        $("<div class='text' />").html("<div>No tables were found</div>").appendTo("#tables");

    } else {

        /* FOR DATA DOWNLOAD*/
        for( var i = 0; i < data.length; i++ ) {

            rawText.push([]);

        }


        for (var i = 0; i < data.length; i++) {    
            
            if(data[i].BlockType == 'LINE') {

                $("<div class='text inline-text'/>").html("<span id='" + data[i].Id + "'data-toggle='tooltip' data-placement='top' title='Confidence: " + data[i].Confidence.toFixed(2) + "%'>" + data[i].Text + "</span>").appendTo("#raw-text");

                var x = data[i].Geometry.BoundingBox.Left * imgWidth;;
                var y = data[i].Geometry.BoundingBox.Top * imgHeight;
                var width = data[i].Geometry.BoundingBox.Width * imgWidth;
                var height = data[i].Geometry.BoundingBox.Height * imgHeight;
                    
                drawBoundingBox(x, y, width, height);

                /* FOR DATA DOWNLOAD*/
                rawText[i].push(data[i].Text);

            } 

            block_map[i] = data[i];

            table_blocks_map[data[i].Id] = data[i];


            if (data[i].BlockType == 'KEY_VALUE_SET') {

                if(data[i].EntityTypes == 'KEY') {
                    
                    key_map[i] = data[i];         

                } else {

                    value_map[i] = data[i];
                }
            }


            if (data[i].BlockType == 'TABLE') {

                table_blocks.push(data[i]);

            }           
        }



        /* --------------------------------------------------- */
        /*   PROCESS FORMS 
        /* --------------------------------------------------- */
        if(type == 'form') {

            key_value_pair = get_kv_relationship(key_map, value_map, block_map);
    
            var key_data = Object.entries(key_value_pair);

            if(key_data.length == 0) {

                  $("<div class='no-analysis' />").html("<div>No Key - Pair values were found in the Document.</div>").appendTo("#forms");

            } else {

                  $('#forms').html('<table id="form-results" class="table table-striped table-hover"><thead class="thead-dark"><th scope="col">Key</th><th scope="col">Value</th></thead><tbody><tr></tr></tbody></table>');

                         
                  for(var i = key_data.length - 1; i >= 0; i--) {

                       $('#form-results').find('tbody:last').append('<tr><td>'+ key_data[i][0] + '</td><td>'+ key_data[i][1] + '</td></tr>');   

                      /* FOR DATA DOWNLOAD*/
                       keyPair +=  key_data[i][0] + "," + key_data[i][1];
                       keyPair += '\n';
                  }
            }           

        } else {

             $("<div class='no-analysis' />").html("<div>FORM Analysis is Not Enabled</div><div>If Image contains a FORM : Enable Form Analysis and Re-Upload Image and Create a New Task</div>").appendTo("#forms");

        }
        


        /* --------------------------------------------------- */
        /*   PROCESS TABLES 
        /* --------------------------------------------------- */
        if (type == 'table') {

            var csv = '';

            if(table_blocks.length == 0) {

                  $("<div class='no-analysis' />").html("<div>No Tables were found in the Document.</div>").appendTo("#tables");

            } else {

                  for (var index in table_blocks) {

                      csv += generate_table_csv(table_blocks[index], table_blocks_map, index++);
                      csv += '\n\n';

                  }
            }

            /* FOR DATA DOWNLOAD*/
            tableValues = csv;
            
        } else {

            $("<div class='no-analysis' />").html("<div>TABLE Analysis is Not Enabled</div><div>If Image contains a TABLE : Enable Table Analysis and Re-Upload the Image and Create a New Task</div>").appendTo("#tables");
        
        }
     }       
}



/*===========================================================================
*
*  IMAGE - PROCESS DOCUMENT (RECEIPT) RESULTS
*
*============================================================================*/

function processImageReceipt(data) { 

    "use strict";
 
     ctx.clearRect(0, 0, canvas.width, canvas.height);
 
     var img = document.getElementById("image");
 
     var imgWidth = img.width;
     var imgHeight = img.height;
 
     /* FOR DATA DOWNLOAD*/
     rawJson = data;

     $("<div class='no-analysis' />").html("<div>FORM Analysis is Not Enabled</div><div>If Image contains a FORM : Enable Checkbox and Re-Upload Image</div>").appendTo("#forms");
     $("<div class='no-analysis' />").html("<div>TABLE Analysis is Not Enabled</div><div>If Image contains a TABLE : Enable Checkbox and Re-Upload Image</div>").appendTo("#tables");

     if (data === undefined || data.length == 0) {
       
         $("<div class='text' />").html("<div>No text was found</div>").appendTo("#raw-text");
         $("<div class='text' />").html("<div>No receips were found</div>").appendTo("#forms");
 
     } else {
 
         /* FOR DATA DOWNLOAD*/
         for( var i = 0; i < data.length; i++ ) { 
             rawText.push([]); 
         } 

         $('#receipt-summary').html('<table id="summary-results" class="table table-striped table-hover"><thead class="thead-dark"><th scope="col">Key</th><th scope="col">Value</th></thead><tbody><tr></tr></tbody></table>');
         $('#receipt-items').html('<table id="summary-items" class="table table-striped table-hover"><thead class="thead-dark"><th scope="col">Item</th><th scope="col">Price</th></thead><tbody><tr></tr></tbody></table>');
         
         for (var i = 0; i < data.length; i++) {    
  
            for (var j = 0; j < data[i].SummaryFields.length; j++) {
                
                var field = data[i].SummaryFields[j];
                
                if (field.LabelDetection) {

                    $("<div class='text inline-text'/>").html("<span data-toggle='tooltip' data-placement='top' title='Confidence: " + field.LabelDetection.Confidence.toFixed(2) + "%'>" + field.LabelDetection.Text + "</span>").appendTo("#raw-text");
 
                    var x = field.LabelDetection.Geometry.BoundingBox.Left * imgWidth;;
                    var y = field.LabelDetection.Geometry.BoundingBox.Top * imgHeight;
                    var width = field.LabelDetection.Geometry.BoundingBox.Width * imgWidth;
                    var height = field.LabelDetection.Geometry.BoundingBox.Height * imgHeight;
                        
                    drawBoundingBox(x, y, width, height);
    
                    /* FOR DATA DOWNLOAD*/
                    rawText[i].push(field.LabelDetection.Text);

                } 
                
                if (field.ValueDetection) {

                    $("<div class='text inline-text'/>").html("<span data-toggle='tooltip' data-placement='top' title='Confidence: " + field.ValueDetection.Confidence.toFixed(2) + "%'>" + field.ValueDetection.Text + "</span>").appendTo("#raw-text");
 
                    var x = field.ValueDetection.Geometry.BoundingBox.Left * imgWidth;;
                    var y = field.ValueDetection.Geometry.BoundingBox.Top * imgHeight;
                    var width = field.ValueDetection.Geometry.BoundingBox.Width * imgWidth;
                    var height = field.ValueDetection.Geometry.BoundingBox.Height * imgHeight;
                        
                    drawBoundingBox(x, y, width, height);
    
                    /* FOR DATA DOWNLOAD*/
                    rawText[i].push(field.ValueDetection.Text);
                }
                
                if (field.Type.Text == 'VENDOR_NAME') {
                    $('#summary-results').find('tbody:last').append('<tr><td>VENDOR_NAME</td><td>'+ field.ValueDetection.Text + '</td></tr>');   
 
                    /* FOR DATA DOWNLOAD*/
                    summaryKeyPair +=  'VENDOR_NAME' + "," + field.ValueDetection.Text;
                    summaryKeyPair += '\n';
                
                } else {
                    $('#summary-results').find('tbody:last').append('<tr><td>' + field.LabelDetection.Text + '</td><td>'+ field.ValueDetection.Text + '</td></tr>');   
 
                    /* FOR DATA DOWNLOAD*/
                    summaryKeyPair +=  field.LabelDetection.Text + "," + field.ValueDetection.Text;
                    summaryKeyPair += '\n';
                }
            }


            for (var j = 0; j < data[i].LineItemGroups[0].LineItems.length; j++) {
                
                var item = data[i].LineItemGroups[0].LineItems[j];

                for (var k = 0; k < item.LineItemExpenseFields.length; k++) {                    

                    if (item.LineItemExpenseFields[k].Type.Text == 'EXPENSE_ROW') {

                        $("<div class='text inline-text'/>").html("<span data-toggle='tooltip' data-placement='top' title='Confidence: " + item.LineItemExpenseFields[k].ValueDetection.Confidence.toFixed(2) + "%'>" + item.LineItemExpenseFields[k].ValueDetection.Text + "</span>").appendTo("#raw-text");
    
                        var x = item.LineItemExpenseFields[k].ValueDetection.Geometry.BoundingBox.Left * imgWidth;;
                        var y = item.LineItemExpenseFields[k].ValueDetection.Geometry.BoundingBox.Top * imgHeight;
                        var width = item.LineItemExpenseFields[k].ValueDetection.Geometry.BoundingBox.Width * imgWidth;
                        var height = item.LineItemExpenseFields[k].ValueDetection.Geometry.BoundingBox.Height * imgHeight;
                            
                        drawBoundingBox(x, y, width, height);
        
                        /* FOR DATA DOWNLOAD*/
                        rawText[i].push(item.LineItemExpenseFields[k].ValueDetection.Text);

                    } 
                }

                $('#summary-items').find('tbody:last').append('<tr><td>' + item.LineItemExpenseFields[0].ValueDetection.Text + '</td><td>'+ item.LineItemExpenseFields[1].ValueDetection.Text + '</td></tr>');   
 
                /* FOR DATA DOWNLOAD*/
                itemsKeyPair +=  item.LineItemExpenseFields[0].ValueDetection.Text + "," + item.LineItemExpenseFields[1].ValueDetection.Text;
                itemsKeyPair += '\n';
                
            }
           

         }
     
      }       
 }



/*===========================================================================
*
*  PDF - PROCESS DOCUMENT (RAW TEXT) RESULTS
*
*============================================================================*/

function processPDFDetectText(response) {

    "use strict";

    /* FOR DATA DOWNLOAD*/
     rawJson = response;

     $("<div class='no-analysis' />").html("<div>FORM Analysis is Not Enabled</div><div>If PDF Document contains a FORM : Enable Checkbox and Re-Upload Document</div>").appendTo("#forms");
     $("<div class='no-analysis' />").html("<div>TABLE Analysis is Not Enabled</div><div>If PDF Document contains a TABLE : Enable Checkbox and Re-Upload Document</div>").appendTo("#tables");
     $("<div class='no-analysis' />").html("<div>Receipt Analysis is Not Enabled</div><div>If Image contains a Receipt : Enable Checkbox and Re-Upload Image</div>").appendTo("#receipt-summary");
     $("<div class='no-analysis' />").html("<div>Receipt Analysis is Not Enabled</div><div>If Image contains a Receipt : Enable Checkbox and Re-Upload Image</div>").appendTo("#receipt-items");


    if (response === undefined || response.length == 0) {
      
        $("<div class='text' />").html("<div>No text was found</div>").appendTo("#raw-text");

    } else {

        /* FOR DATA DOWNLOAD*/
        for( var i = 0; i < response.length; i++ ) {
            for( var j = 0; j < response[i].length; j++ ) {
                rawText.push([]);
            }
        }

        for (let index = 0; index < response.length; index++) {
 
            var data = response[index];

            for (var i = 0; i < data.length; i++) {    
                
                if(data[i].BlockType == 'LINE') {

                    $("<div class='text inline-text'/>").html("<span data-toggle='tooltip' data-placement='top' title='Confidence: " + data[i].Confidence.toFixed(2) + "%'>" + data[i].Text + "</span>").appendTo("#raw-text");

                    /* FOR DATA DOWNLOAD*/          
                    rawText[i].push(data[i].Text);

                }                 
            }
        }
     }  
}



/*===========================================================================
*
*  PDF - PROCESS DOCUMENT (FORMS & TABLES) RESULTS
*
*============================================================================*/

function processPDFAnalyzeText(response, type) { 

   "use strict";

    var key_value_pair = {};
    var key_map = {};
    var value_map = {};
    var block_map = {};
    var table_blocks_map = {};
    var table_blocks = [];

    /* FOR DATA DOWNLOAD*/
    rawJson = response;

    $("<div class='no-analysis' />").html("<div>Receipt Analysis is Not Enabled</div><div>If Image contains a Receipt : Enable Checkbox and Re-Upload Image</div>").appendTo("#receipt-summary");
    $("<div class='no-analysis' />").html("<div>Receipt Analysis is Not Enabled</div><div>If Image contains a Receipt : Enable Checkbox and Re-Upload Image</div>").appendTo("#receipt-items");


    if (response === undefined || response.length == 0) {
      
        $("<div class='text' />").html("<div>No text was found</div>").appendTo("#raw-text");
        $("<div class='text' />").html("<div>No forms were found</div>").appendTo("#forms");
        $("<div class='text' />").html("<div>No tables were found</div>").appendTo("#tables");

    } else {

        /* FOR DATA DOWNLOAD*/
        for( var i = 0; i < response.length; i++ ) {
            for( var j = 0; j < response[i].length; j++ ) {
                rawText.push([]);
            }
        }


        for (let index = 0; index < response.length; index++) {
 
            var data = response[index];

            for (var i = 0; i < data.length; i++) {    
            
                if(data[i].BlockType == 'LINE') {

                    $("<div class='text inline-text'/>").html("<span id='" + data[i].Id + "'data-toggle='tooltip' data-placement='top' title='Confidence: " + data[i].Confidence.toFixed(2) + "%'>" + data[i].Text + "</span>").appendTo("#raw-text");

                    /* FOR DATA DOWNLOAD*/   
                    rawText[i].push(data[i].Text);

                } 

                block_map[i] = data[i];

                table_blocks_map[data[i].Id] = data[i];


                if(data[i].BlockType == 'KEY_VALUE_SET') {

                    if(data[i].EntityTypes == 'KEY') {
                        
                        key_map[i] = data[i];         

                    } else {

                        value_map[i] = data[i];
                    }
                }


                if (data[i].BlockType == 'TABLE') {

                    table_blocks.push(data[i]);

                }
            }
        }

        /* --------------------------------------------------- */
        /*   PROCESS FORMS
        /* --------------------------------------------------- */
        if(type == 'form') {

            key_value_pair = get_kv_relationship(key_map, value_map, block_map);
    
            var key_data = Object.entries(key_value_pair);

            if(key_data.length == 0) {

                 $("<div class='no-analysis' />").html("<div>No Key - Pair values were found in the Document.</div>").appendTo("#forms");

            } else {

                $('#forms').html('<table id="form-results" class="table table-striped table-hover"><thead class="thead-dark"><th scope="col">Key</th><th scope="col">Value</th></thead><tbody><tr></tr></tbody></table>');

                             
                for(var i = key_data.length - 1; i >= 0; i--) {

                    $('#form-results').find('tbody:last').append('<tr><td>'+ key_data[i][0] + '</td><td>'+ key_data[i][1] + '</td></tr>');   

                    /* FOR DATA DOWNLOAD*/
                    keyPair +=  key_data[i][0] + "," + key_data[i][1];
                    keyPair += '\n';

                } 
            }        

        } else {

             $("<div class='no-analysis' />").html("<div>FORM Analysis is Not Enabled</div><div>If PDF Document contains a FORM : Enable Form Analysis and Re-Upload PDF Document and Create a New Task</div>").appendTo("#forms");

        }
        

        /* --------------------------------------------------- */
        /*   PROCESS TABLES
        /* --------------------------------------------------- */
        if (type == 'table') {

            var csv = '';

            if(table_blocks.length == 0) {

                  $("<div class='no-analysis' />").html("<div>No Tables were found in the Document.</div>").appendTo("#tables");

            } else {

                  for (var index in table_blocks) {

                      csv += generate_table_csv(table_blocks[index], table_blocks_map, index++);
                      csv += '\n\n';

                  }
            }

            /* FOR DATA DOWNLOAD*/
            tableValues = csv;              
            
        } else {

            $("<div class='no-analysis' />").html("<div>TABLE Analysis is Not Enabled</div><div>If PDF Document contains a TABLE : Enable Document Analysis and Re-Upload PDF Document and Create a New Task</div>").appendTo("#tables");
        
        }
     }       
}



/*===========================================================================
*
*  PDF - PROCESS DOCUMENT (RECEIPT) RESULTS
*
*============================================================================*/

function processPDFReceipt(data) { 

    "use strict";
 
     /* FOR DATA DOWNLOAD*/
     rawJson = data;

     $("<div class='no-analysis' />").html("<div>FORM Analysis is Not Enabled</div><div>If Image contains a FORM : Enable Checkbox and Re-Upload Image</div>").appendTo("#forms");
     $("<div class='no-analysis' />").html("<div>TABLE Analysis is Not Enabled</div><div>If Image contains a TABLE : Enable Checkbox and Re-Upload Image</div>").appendTo("#tables");

     if (data === undefined || data.length == 0) {
       
         $("<div class='text' />").html("<div>No text was found</div>").appendTo("#raw-text");
         $("<div class='text' />").html("<div>No receips were found</div>").appendTo("#forms");
 
     } else {
 
         /* FOR DATA DOWNLOAD*/
         for( var i = 0; i < data.length; i++ ) { 
             rawText.push([]); 
         } 

         $('#receipt-summary').html('<table id="summary-results" class="table table-striped table-hover"><thead class="thead-dark"><th scope="col">Key</th><th scope="col">Value</th></thead><tbody><tr></tr></tbody></table>');
         $('#receipt-items').html('<table id="summary-items" class="table table-striped table-hover"><thead class="thead-dark"><th scope="col">Item</th><th scope="col">Price</th></thead><tbody><tr></tr></tbody></table>');
         
         for (var i = 0; i < data.length; i++) {    
  
            for (var j = 0; j < data[i].SummaryFields.length; j++) {
                
                var field = data[i].SummaryFields[j];
                
                if (field.LabelDetection) {

                    $("<div class='text inline-text'/>").html("<span data-toggle='tooltip' data-placement='top' title='Confidence: " + field.LabelDetection.Confidence.toFixed(2) + "%'>" + field.LabelDetection.Text + "</span>").appendTo("#raw-text");
    
                    /* FOR DATA DOWNLOAD*/
                    rawText[i].push(field.LabelDetection.Text);

                } 
                
                if (field.ValueDetection) {

                    $("<div class='text inline-text'/>").html("<span data-toggle='tooltip' data-placement='top' title='Confidence: " + field.ValueDetection.Confidence.toFixed(2) + "%'>" + field.ValueDetection.Text + "</span>").appendTo("#raw-text");
    
                    /* FOR DATA DOWNLOAD*/
                    rawText[i].push(field.ValueDetection.Text);
                }
                
                if (field.Type.Text == 'VENDOR_NAME') {
                    $('#summary-results').find('tbody:last').append('<tr><td>VENDOR_NAME</td><td>'+ field.ValueDetection.Text + '</td></tr>');   
 
                    /* FOR DATA DOWNLOAD*/
                    summaryKeyPair +=  'VENDOR_NAME' + "," + field.ValueDetection.Text;
                    summaryKeyPair += '\n';
                
                } else {
                    $('#summary-results').find('tbody:last').append('<tr><td>' + field.LabelDetection.Text + '</td><td>'+ field.ValueDetection.Text + '</td></tr>');   
 
                    /* FOR DATA DOWNLOAD*/
                    summaryKeyPair +=  field.LabelDetection.Text + "," + field.ValueDetection.Text;
                    summaryKeyPair += '\n';
                }
            }


            for (var j = 0; j < data[i].LineItemGroups[0].LineItems.length; j++) {
                
                var item = data[i].LineItemGroups[0].LineItems[j];

                for (var k = 0; k < item.LineItemExpenseFields.length; k++) {                    

                    if (item.LineItemExpenseFields[k].Type.Text == 'EXPENSE_ROW') {

                        $("<div class='text inline-text'/>").html("<span data-toggle='tooltip' data-placement='top' title='Confidence: " + item.LineItemExpenseFields[k].ValueDetection.Confidence.toFixed(2) + "%'>" + item.LineItemExpenseFields[k].ValueDetection.Text + "</span>").appendTo("#raw-text");
        
                        /* FOR DATA DOWNLOAD*/
                        rawText[i].push(item.LineItemExpenseFields[k].ValueDetection.Text);

                    } 
                }

                $('#summary-items').find('tbody:last').append('<tr><td>' + item.LineItemExpenseFields[0].ValueDetection.Text + '</td><td>'+ item.LineItemExpenseFields[1].ValueDetection.Text + '</td></tr>');   
 
                /* FOR DATA DOWNLOAD*/
                itemsKeyPair +=  item.LineItemExpenseFields[0].ValueDetection.Text + "," + item.LineItemExpenseFields[1].ValueDetection.Text;
                itemsKeyPair += '\n';
                
            }
           

         }
     
      }       
 }

/*===========================================================================
*
*  RESPONSE - GENERATE TABLE WITH COLUMNS AND ROWS
*
*============================================================================*/

function generate_table_csv(table_result, table_blocks_map, table_index) {

  "use strict";

  var csv = '';

  var rows = get_rows_columns_map(table_result, table_blocks_map);

  var rows_data = Object.entries(rows); 

  var table = document.getElementById('dynamic-table');


  for(var i = 0; i < rows_data.length; i++) {      

      var column_data = Object.entries(rows_data[i][1]);
     
      var row = document.createElement('tr');

      row.setAttribute('class', 'dynamic-row');      
      

      for(var j = 0; j < column_data.length; j++) {

          var column = document.createElement('td');
          column.setAttribute('class', 'dynamic-column');
          
          var col_value = document.createElement('div');
          col_value.setAttribute('class', 'col-value');

          if(column_data[j] != undefined){
              col_value.innerText = column_data[j][1];
          }

          column.appendChild(col_value);

          row.appendChild(column);

      }

      table.appendChild(row);
  }


  var table_id = 'Table_' + table_index.toString();

  for(var i = 0; i < rows_data.length; i++) {      

      var column_data = Object.entries(rows_data[i][1]);     

      for(var j = 0; j < column_data.length; j++) {


          if(column_data[j] != undefined){

              csv += column_data[j][1] + ","; 

          }

      }

      csv += '\n'; 
  }

  csv += '\n\n\n';

  return csv;

}

/* --------------------------------------------------- */
/*   GET ROWS AND COLUMNS MAPPING
/* --------------------------------------------------- */
function get_rows_columns_map(table_result, table_blocks_map) {

    "use strict";

    var rows = {};

    var table_data = Object.entries(table_result);

    var relationships = table_data[4];

    var relationship = relationships[1];

    if(relationship !== undefined) {

      if(relationship[0]['Type'] == 'CHILD') {
    
          for(var child_id in relationship[0].Ids) {

              var cell = table_blocks_map[relationship[0].Ids[child_id]];

              if (cell['BlockType'] !== undefined) {
                
                if (cell['BlockType'] == 'CELL') {
                    
                    var row_index = cell['RowIndex'];
                    var column_index = cell['ColumnIndex'];

                    if (!(row_index in rows)) {

                        rows[row_index] = {};

                    }

                    rows[row_index][column_index] = get_table_text(cell, table_blocks_map);
                }

              }

          }
          
      }

    }

    return rows;

}


/* --------------------------------------------------- */
/*   EXTRACT TEXT FOR TABLE
/* --------------------------------------------------- */
function get_table_text(result, table_blocks_map) {

    "use strict";

    var result_data = Object.entries(result);

    var relationships = result_data[8];

    var text = '';


    if (relationships !== undefined) {

        var relationship = relationships[1];
    }
   
            
    if (relationship !== undefined) {

      if(relationship[0]['Type'] == 'CHILD') {

          for(var child_id in relationship[0].Ids) {

              var word = table_blocks_map[relationship[0].Ids[child_id]];

              if(word['BlockType'] == 'WORD') {

                  text += word['Text'] + ' ';

              }

              if(word['BlockType'] == 'SELECTION_ELEMENT') {

                  if(word['SelectionStatus'] == 'SELECTED') {

                      text += 'X ';

                  }
              }
                  
          }
      }
    }

    return text; 

}



/*===========================================================================
*
*  RESPONSE - GENERATE KEY VALUE PAIR
*
*============================================================================*/

function get_kv_relationship(key_map, value_map, block_map) {

    "use strict";

    var kvs = {};

    for(var key_block in key_map) {

        var key_value = key_map[key_block];

        var value_block = find_value_block(key_value, value_map);

        var key = get_text(key_value, block_map);

        var val = get_text(value_block, block_map);

        kvs[key] = val;

    }

    return kvs;
}


/* --------------------------------------------------- */
/*   FIND VALUE BLOCKS
/* --------------------------------------------------- */
function find_value_block(key_block, value_map) {

    "use strict";

    var key_data = Object.entries(key_block);
    var value_data = Object.entries(value_map);

    var relationships = key_data[4];
    var relationship = relationships[1];

    var value_block = '';


    if(relationship[0]['Type'] == 'VALUE') {

        for(var i = 0; i < value_data.length; i++) {

            if(value_data[i][1].Id == relationship[0]['Ids'])

                value_block = value_data[i][1];              
        
        }
    }

    return value_block;

}


/* --------------------------------------------------- */
/*   EXTRACT TEXT
/* --------------------------------------------------- */
function get_text(result, block_map) {

    "use strict";

    var result_data = Object.entries(result);
    var block_map_data = Object.entries(block_map);

    var relationships = result_data[4];
    var relationship = relationships[1];

    var text = '';


    if (result_data[5] !== undefined) {

      if(result_data[5][1] == 'VALUE') {
        
        if(relationship[0]['Type'] == 'CHILD') {

            for(var child_id of relationship[0]['Ids']) {

                 for(var i = 0; i < block_map_data.length; i++) {

                    if(block_map_data[i][1].Id == child_id) {

                        var word = block_map_data[i][1];

                        if(word['BlockType'] == 'WORD') {

                            text += word['Text'] + ' ';

                        }

                        if(word['BlockType'] == 'SELECTION_ELEMENT') {

                            if(word['SelectionStatus'] == 'SELECTED') {

                                text += 'X ';

                            }
                        }
                    }         
                  }
            }
        }
      }
    }


    if (result_data[5] !== undefined) {

      if(result_data[5][1] == 'KEY') {
      
        if(relationship[1]['Type'] == 'CHILD') {

            for(var child_id of relationship[1]['Ids']) {

                 for(var i = 0; i < block_map_data.length; i++) {

                    if(block_map_data[i][1].Id == child_id) {

                        var word = block_map_data[i][1];

                        if(word['BlockType'] == 'WORD') {

                            text += word['Text'] + ' ';

                        }

                        if(word['BlockType'] == 'SELECTION_ELEMENT') {

                            if(word['SelectionStatus'] == 'SELECTED') {

                                text += 'X ';

                            }
                        }
                    }         
                  }
            }
        }
      }
    }

    return text;

}


/*===========================================================================
*
*  DONWLOAD TEXTRACT RESULTS
*
*============================================================================*/

var rawJson = '';
var rawText = [];
var keyPair = '';
var itemsKeyPair = '';
var summaryKeyPair = '';
var tableValues = '';

$(document).ready(function() {

  "use strict";

  $('#download-now').on('click', function(e){
      
      e.preventDefault();

      var text = '';

      rawText.forEach(function(row) {
                text += row.join();
                text += " \n";
      });

      var zip = new JSZip();

      zip.file("raw_text.txt", text);
      zip.file("raw_json.json", JSON.stringify(rawJson)); 

      if (keyPair !== '') {
        zip.file("key_pair.csv", keyPair);
      }

      if (itemsKeyPair !== '') {
        zip.file("receipt_items.csv", itemsKeyPair);
      }

      if (summaryKeyPair !== '') {
        zip.file("receipt_summary.csv", summaryKeyPair);
      }
      
      if (tableValues !== '') {
        zip.file("table.csv", tableValues);
      }
      
      var d = new Date();
      var date = ("0" + d.getDate()).slice(-2) + "-" + ("0"+(d.getMonth()+1)).slice(-2) + "-" + d.getFullYear();

      zip.generateAsync({type:"blob"}).then(function (content) { 
              saveAs(content, date + "-textract-results.zip");                          
          }, function (err) {
              $("#download-now").text(err);
      });

  });

});



/*===========================================================================
*
*  DISPLAY CANVAS BOUNDING BOXES
*
*============================================================================*/
try {
  var $canvas = document.getElementById('canvas');
  var ctx = canvas.getContext("2d");
} catch(error) {
  console.log('Not Applicable for  PDF Textract');
}

$(document).ready(function(){

    "use strict";
    try {
        $canvas.width = $("#document-inner").width();
        $canvas.height = $("#document-inner").height();
    } catch(error) {
        console.log('Not Applicable for  PDF Textract');
    }
 
});


function drawBoundingBox(left, top, width, height) {

  "use strict";

  var x = left;
  var y = top;
  var w = width;
  var h = height;

  ctx.beginPath();
  ctx.lineWidth = "1";
  ctx.strokeStyle = "#00BFFF";
  ctx.rect(x, y, w, h);
  ctx.stroke();

}


/* --------------------------------------------------- */
/*   DISPLAY UPLOADED PDF IN THE MAIN PDF BOX
/* --------------------------------------------------- */
function displayPDF(input) {

    "use strict";
  
    var request = new XMLHttpRequest();
    request.open('GET', input, true);
    request.responseType = 'blob';
    request.onload = function() {
        var reader = new FileReader();
        reader.readAsDataURL(request.response);
        reader.onload =  function(e){

            var options = {
                height: "800px",
                page: '1',
                fallbackLink: "<p>This browser does not support PDF</p>",
                pdfOpenParams: {
                    view: 'FitV',
                    pagemode: 'thumbs'
                }
            };

            PDFObject.embed(e.target.result, '#pdf-container', options);
        };
    };

    request.send();
    
  }


  /*===========================================================================
*
*  TOOLTIP ENABLER 
*
*============================================================================*/

$(document).ready(function (e) {

    "use strict";

    $('body').tooltip({selector:'[data-toggle=tooltip]'})

});