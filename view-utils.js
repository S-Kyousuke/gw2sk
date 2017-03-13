var ViewUtils = (function () {
    
    var CoinType = {
        GOLD: {name: "gold coin", source: "images/gold_coin.png"},
        SILVER: {name: "silver coin", source: "images/silver_coin.png"},
        COPPER: {name: "copper coin", source: "images/copper_coin.png"},
    };
    
    function getFillZeroNumber(num, size) {
        var numText = "000000000" + num;
        return numText.substr(numText.length - size);
    }
    
    function getCoinImageTagString(coinType) {
        return '<img class="coin" src="' + coinType.source + '" alt="' + coinType.name + '">';
    }
        
    return {
        getCoinValueDivString: function(priceInCopper, showPlusSign) {
            var negativeValue = priceInCopper < 0;
            var absValue = Math.abs(priceInCopper);
            
            var gold = Math.floor(absValue / 10000);
            var silver = Math.floor((absValue % 10000) / 100);
            var copper = absValue % 100;
            
            var htmlText = [];        
            htmlText.push('<div>');
            
            if (negativeValue) {
                htmlText.push("- ");
            }            
            else if (showPlusSign) {
                htmlText.push("+ ");
            }
        
            if (gold != 0) {
                htmlText.push(gold);
                htmlText.push(getCoinImageTagString(CoinType.GOLD));
            }
            if (silver != 0 || gold != 0) {
                if (gold != 0) {
                    htmlText.push(getFillZeroNumber(silver, 2));  
                } else {
                    htmlText.push(silver);
                }      
                htmlText.push(getCoinImageTagString(CoinType.SILVER));
                htmlText.push(getFillZeroNumber(copper, 2));
            } else {
                htmlText.push(copper);
            }
            htmlText.push(getCoinImageTagString(CoinType.COPPER));
            
            htmlText.push('</div>');
            return htmlText.join("");
        },
        
        getItemPriceBox: function(itemName, itemIcon, price, dayAvgPrice, weekAvgPrice) {
            var box =  $('<div class="bag-price"></div');
            var table = $('<table class="bag-price"></table>');
            var header = $('<thead></thead>');
            var body = $('<tbody></tbody>');
            
            var itemNameRow = $('<tr></tr>');
            var itemNameCell = $('<td colspan="2"></td>');
            itemNameCell.append('<img src="' + itemIcon + '" class="item">');
            itemNameCell.append('<span class="item-name">' + itemName + '</span>');
            itemNameRow.append(itemNameCell);            
            header.append(itemNameRow);
            
            var priceRow = $('<tr><td>Current Price:</td><td>' + ViewUtils.getCoinValueDivString(price, false)  + '</td></tr>');
            var dayAvgPriceRow = $('<tr><td>Day Avg Price:</td><td>' + ViewUtils.getCoinValueDivString(dayAvgPrice, false) + '</td></tr>');
            var weekAvgPriceRow = $('<tr><td>Week Avg Price:</td><td>' + ViewUtils.getCoinValueDivString(weekAvgPrice, false) + '</td></tr>');
            body.append(priceRow);            
            body.append(dayAvgPriceRow);            
            body.append(weekAvgPriceRow);
            
            table.append(header);
            table.append(body);
            box.append(table);
            
            return box;                        
        }
    }
    
}());