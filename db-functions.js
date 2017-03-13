var DbFunctions = (function () {
    
    return {
        call: function(functionName, arguments, callback) {
            $.ajax({
                type: "POST",
                url: 'db-functions.php',
                dataType: 'json',
                data: {functionName: functionName, arguments: arguments},            
                success: function (result, textstatus) {
                    typeof callback == "function" && callback(result);
                },
                error: function(jqXHR, error, errorThrown) {  
                    if(jqXHR.status && jqXHR.status==400){
                        alert(jqXHR.responseText); 
                    }else{
                        alert("Something went wrong!");
                    }
                }
            });
        }
    }
    
}());