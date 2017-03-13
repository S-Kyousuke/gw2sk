<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">        
        <link rel="stylesheet" href="style.css"> 
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script src="time-utils.js"></script>
        <script src="db-functions.js"></script>
        <script src="gw2-sk.js"></script>
        <script src="view-utils.js"></script>
        <script src="item-box.js"></script>
        <script>
            $(document).ready(function() {                
                var materialIds = [ 
                    19718, 19739, 19741, 19743, 19748, 19745, 
                    19723, 19726, 19727, 19724, 19722, 19725, 
                    19719, 19728, 19730, 19731, 19729, 19732, 
                    19697, 19699, 19703, 19698, 19702, 19700, 19701                
                ];
                $.each(materialIds, function( index, value ) {
                    var itemBox = new ItemPriceBox();                    
                    $('#content').append(itemBox.getBox());
                    
                    Gw2Sk.getItemName(value, function(name) {
                        itemBox.setName(name);
                    });
                    Gw2Sk.getItemIcon(value, function(icon) {                    
                        itemBox.setIcon(icon);    
                    });                  
                    Gw2Sk.getItemTpSellPrice(value, function(price) {
                        itemBox.setPrice(price);
                    });
                    
                    Gw2Sk.getItemTpDayAvgSellPrice(value, function(dayAvgPrice){
                        itemBox.setDayAvgPrice(dayAvgPrice);
                    });
                    Gw2Sk.getItemTpWeekAvgSellPrice(value, function(weekAvgPrice){
                        itemBox.setWeekAvgPrice(weekAvgPrice);
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
                        <li><a href="bag">Bag Income</a></li>
                        <li class="active"><a href="material">Material Price</a></li>
                        <li><a href="average">Avgerage Price</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <div id="content" class="container-fluid"></div>
    </body>
</html>