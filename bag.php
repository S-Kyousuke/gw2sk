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
                Gw2Sk.getBagIds(function(result) {
                    $.each(result.bagIds, function( index, value ) {
                        var itemBox = new ItemIncomeBox();
                        $('#content').append(itemBox.getBox());
                        Gw2Sk.getItemName(value, function(name) {
                            itemBox.setName(name);
                        });
                        Gw2Sk.getItemIcon(value, function(icon) {                    
                            itemBox.setIcon(icon);    
                        });
                        Gw2Sk.getBagIncome(value, Gw2Sk.getItemTpSellPrice, function(income) {
                            itemBox.setIncome(income);
                        });
                        Gw2Sk.getBagIncome(value, Gw2Sk.getItemTpDayAvgSellPrice, function(dayAvgIncome) {
                            itemBox.setDayAvgIncome(dayAvgIncome);
                        });
                        Gw2Sk.getBagIncome(value, Gw2Sk.getItemTpWeekAvgSellPrice, function(weekAvgIncome) {
                            itemBox.setWeekAvgIncome(weekAvgIncome);
                        });
                    });
                });
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
                        <li class="active"><a href="bag">Bag Income</a></li>
                        <li><a href="material">Material Price</a></li>
                        <li><a href="average">Avgerage Price</a></li>
                        <li><a href="gem-alert">Gem Alert</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <div id="content" class="container-fluid"></div>
    </body>
</html>