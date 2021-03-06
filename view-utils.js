var ViewUtils = (function () {
    
    function getFillZeroNumber(num, size) {
        var numText = "000000000" + num;
        return numText.substr(numText.length - size);
    }
    
    function getCoinImageTag(coinType) {
        return '<img class="coin" src="' + coinType.source + '" alt="' + coinType.name + '">';
    }
    
    function getMiddleVertTextTag(text) {
        return '<span class="middle-vert">' + text + '</span>';
    }
        
    return {
        getCoinValueDivTag: function(priceInCopper, showPlusSign) {
            var negativeValue = priceInCopper < 0;
            var absValue = Math.abs(priceInCopper);
            
            var gold = Math.floor(absValue / 10000);
            var silver = Math.floor((absValue % 10000) / 100);
            var copper = absValue % 100;
            
            var htmlText = [];        
            htmlText.push('<div>');
            
            if (negativeValue) {
                htmlText.push(getMiddleVertTextTag('- '));
            }            
            else if (showPlusSign) {
                htmlText.push(getMiddleVertTextTag('+ '));
            }
        
            if (gold != 0) {                
                htmlText.push(getMiddleVertTextTag(gold));
                htmlText.push(getCoinImageTag(Gw2Sk.CoinType.GOLD));                
            }
                       
            if (silver != 0 || gold != 0) {
                if (gold != 0) {
                    htmlText.push(getMiddleVertTextTag(getFillZeroNumber(silver, 2)));  
                } else {
                    htmlText.push(getMiddleVertTextTag(silver));
                }
                htmlText.push(getCoinImageTag(Gw2Sk.CoinType.SILVER));
                
                htmlText.push(getMiddleVertTextTag(getFillZeroNumber(copper, 2)));
            } else {        
                htmlText.push(getMiddleVertTextTag(copper));
            }
            htmlText.push(getCoinImageTag(Gw2Sk.CoinType.COPPER));
            
            htmlText.push('</div>');
            return htmlText.join("");
        }
    }
    
}());