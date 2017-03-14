<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" href="style.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script src="time-utils.js?v=1.0.0"></script>
        <script src="db-functions.js?v=1.0.0"></script>
        <script src="gw2-sk.js?v=1.0.0"></script>
        <script src="view-utils.js?v=1.0.0"></script>
        <script src="item-box.js?v=1.0.0"></script>
        <script>
            $(document).ready(function() {  
                var gemField = $("#gemField");                
                var gemAmount = $("#gemAmount");
                
                var dotText = $('<span>....</span>');
                var gemCost = dotText;                 

                var delayTask = (function(){
                    var timerId = 0;
                    return function(callback, ms){
                        clearTimeout(timerId);
                        timerId = setTimeout(callback, ms);
                    };
                })();                   
                
                $("#costs").append(gemCost);
                
                // input listener
                gemAmount.on('input', function() {                    
                    delayTask(function(){
                        gemField.attr('disabled', '');
                        var gemAmountValue = gemAmount.val();
                        if (gemAmountValue != '') {
                                                       
                            gemCost.replaceWith(dotText);
                            gemCost = dotText;
                        
                            Gw2Sk.getGemPrice(gemAmountValue, function(price){
                                var newGemCost = $(ViewUtils.getCoinValueDivTag(price, false));                            
                                gemCost.replaceWith(newGemCost);
                                gemCost = newGemCost;
                                gemField.removeAttr('disabled');   
                            }); 
                        } else {
                            gemCost.replaceWith(dotText);
                            gemCost = dotText;
                            gemField.removeAttr('disabled'); 
                        }            
                    }, 1000 );
                });
                
                // first animation
                setTimeout(function() {
                    gemAmount.val('1');                    
                    gemAmount.trigger('input');
                }, 1000);                   
                setTimeout(function() {
                    gemAmount.val('10');
                }, 1200);                 
                setTimeout(function() {
                    gemAmount.val('100');
                }, 1400);            
            });        
        </script>
    <body>
        <nav class="navbar navbar-inverse">
            <div class="container-fluid">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>                        
            </button>
                <div class="navbar-header"><a class="navbar-brand" href="javascript:void(0)">GW2SK</a></div>
                <div class="collapse navbar-collapse" id="navbar">
                    <ul class="nav navbar-nav">
                        <li><a href="bag">Bag Income</a></li>
                        <li><a href="material">Material Price</a></li>
                        <li><a href="average">Avgerage Price</a></li>                        
                        <li class="active"><a href="gem-alert">Gem Alert</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="container">
            <h4>Coins to Gems (Beta)</h4>
            <form class="form-inline">
                <div class="form-group form-group--gem-alert has-feedback">
                <fieldset id="gemField">
                    <input type="number" min="1" class="form-control" id="gemAmount" placeholder="Enter Gem">
                    <i class="icon-gem form-control-feedback"></i>
                </fieldset>
                </div>
                <div class="form-group">
                    <label class="control-label control-label--gem-alert">Costs</label>
                    <div id="costs" class="form-control-static"></div>
                </div>
             </form>
        </div>
    </body>
</html>