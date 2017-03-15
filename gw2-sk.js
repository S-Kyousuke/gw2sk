var Gw2Sk = (function () {
        
    function getItem(ItemId, callback) {
        $.getJSON("https://api.guildwars2.com/v2/items/" + ItemId, function (result) {
            callback(result);
            var flag;
            var boundOnAcquire = false;
            var noSell = false;
            for (flag of result.flags) {
                if (flag == "SoulbindOnAcquire" || flag == "AccountBound") {
                    boundOnAcquire = true;
                }
                if (flag == "NoSell") {
                    noSell = true;
                }
            }
            DbFunctions.call('addItem', [result.id, result.name, result.rarity, result.icon, boundOnAcquire, noSell, result.vendor_value]);        
        }).fail(function() {
            callback(null);
        });
    }
    
    function getCoinToGemRate(coin, callback) {
        $.getJSON("https://api.guildwars2.com/v2/commerce/exchange/coins?quantity=" + coin, function(result) {
            callback(result);
        }).fail(function() {
            callback(null);
        });
    }
    
    function getGemToCoinRate(gem, callback) {
        $.getJSON("https://api.guildwars2.com/v2/commerce/exchange/gems?quantity=" + gem, function(result) {
            callback(result);
        }).fail(function() {
            callback(null);
        });
    }
    
    function findGemPrice(gemQuantity, testCoinsPerGem, callback) {
        getCoinToGemRate(gemQuantity * testCoinsPerGem, function(result){
            if (result != null) {
                var gemQuantityDiff = Math.abs((result.quantity - gemQuantity));        
                if (gemQuantityDiff != 0) {
                    var newTestCoin = Math.round((result.coins_per_gem * gemQuantityDiff + testCoinsPerGem)/(gemQuantityDiff + 1));
                    if (newTestCoin < testCoinsPerGem) { 
                        findGemPrice(gemQuantity, newTestCoin, callback);
                    } else {
                        callback(newTestCoin * gemQuantity);
                    }
                } else {
                    callback(result.coins_per_gem * gemQuantity);
                }
            }
            else {
                const NEW_VALUE_FACTOR = 1.05;
                findGemPrice(Math.round(testCoinsPerGem * 1.05), gemQuantity, callback);
            }            
        });        
    }
    
    function findExactGemsPrice(gemQuantity, testCoins, lastChange, lastQuantity, callback) {       
        getCoinToGemRate(testCoins, function(result){         
            console.log(testCoins, lastChange);
            if (result != null) {               
                if (lastChange == 1 && lastQuantity == (gemQuantity - 1) && result.quantity == gemQuantity) {
                    callback(testCoins);
                    return;
                } else if (lastChange == 1 && lastQuantity == gemQuantity && result.quantity == (gemQuantity - 1)) {
                    callback(testCoins + 1);
                    return;
                }
                
                var change;                
                if ((result.quantity != (gemQuantity - 1) && result.quantity != gemQuantity) || lastQuantity == result.quantity) {
                    change = Math.floor(lastChange * Math.sqrt(2));
                } else {
                    change = Math.max(Math.floor(lastChange / 2), 1);
                }
                
                if (result.quantity < gemQuantity) {  
                    findExactGemsPrice(gemQuantity, testCoins + change, change, result.quantity, callback);
                } else if (result.quantity >= gemQuantity) {                    
                    findExactGemsPrice(gemQuantity, testCoins - change, change, result.quantity, callback);
                }
            }
            else {
                findExactGemsPrice(gemQuantity, testCoins, lastChange, lastQuantity, callback);
            }
        });
    }
            
    return {                 
    
        CoinType: {
            GOLD: {name: "gold coin", source: "images/gold_coin.png"},
            SILVER: {name: "silver coin", source: "images/silver_coin.png"},
            COPPER: {name: "copper coin", source: "images/copper_coin.png"},
        },
        
        getItemName: function(itemId, callback) {
            DbFunctions.call('getItemName', [itemId], function (result) {
                if (result.name != null) {
                    callback(result.name);
                } else {
                    getItem(itemId, function(result) {
                        callback(result.name);
                    });
                }  
            });        
        },
        
        getItemIcon: function(itemId, callback) {
            DbFunctions.call('getItemIcon', [itemId], function (result) {
                if (result.icon != null) {
                    callback(result.icon);
                } else {
                    getItem(itemId, function(result) {
                        callback(result.icon);
                    });
                }  
            });    
        },
        
        getItemTpSellPrice: function(itemId, callback) {
            $.getJSON("https://api.guildwars2.com/v2/commerce/prices/" + itemId, function(data) {
                callback(data.sells.unit_price);
            }).fail(function() {
                callback(null);
            });            
        },
        
        getItemTpAvgSellPrice: function(itemId, dayCount, callback) {
            $.getJSON("http://www.gw2spidy.com/api/v0.9/json/listings/" + itemId + "/sell", function(data) {
                var results = data.results;
                var sumPrice = 0;
                var priceCount = 0;
                
                for (result of results) {
                    if (TimeUtils.isAgeInRange(result.listing_datetime, dayCount)) {
                        sumPrice += result.unit_price;
                        priceCount++;
                    } else {
                        enoughData = true;
                        break;
                    }
                }
                callback(Math.round(sumPrice / priceCount));
            }).fail(function() {
                callback(null);
            });
        },
        
        getItemTpDayAvgSellPrice: function(itemId, callback) {
            Gw2Sk.getItemTpAvgSellPrice(itemId, 1, callback);
        },
        
        getItemTpWeekAvgSellPrice: function(itemId, callback) {
            Gw2Sk.getItemTpAvgSellPrice(itemId, 7, callback);
        },
        
        getTpFee: function(sellPrice) {
            const LISTING_FEE = 0.05;
            const SELLING_FEE = 0.10;            
            return Math.round(sellPrice * LISTING_FEE) + Math.round(sellPrice * SELLING_FEE);
        },
        
        getItemListings: function(bagId, callback) {
            DbFunctions.call('getItemListings', [bagId], function (result) {
                callback(result);  
            });    
        },
        
        getOpenedBags: function(bagId, callback) {
            DbFunctions.call('getOpenedBags', [bagId], function (result) {
                callback(result);  
            });    
        },
        
        getBagIds: function(callback) {
            DbFunctions.call('getBagIds', [], function (result) {
                callback(result);  
            });             
        },
        
        getBagIncome: function(bagId, tpPriceFunction, callback) {
            Gw2Sk.getItemListings(bagId, function(result) {
                var itemListings = result.itemListings;
                
                Gw2Sk.getOpenedBags(bagId, function(result) {
                    var openedBags = result.openedBags;                    
                    var sumBagQuantity = 0;         
                    var sumOtherNetIncome = 0;                    
                    for (openedBag of openedBags) {
                        sumBagQuantity += openedBag.quantity;
                        sumOtherNetIncome += openedBag.otherNetIncome;
                    }                    
                    var remainingItemId = itemListings.length;
                    var sumIncome = 0;                             
                    $.each(itemListings, function( index, value ) {
                        tpPriceFunction(value.itemId, function(price) {
                            var income = price - Gw2Sk.getTpFee(price);                            
                            sumIncome += value.quantity * income;
                            remainingItemId--;
                            if (remainingItemId == 0) {
                                callback(Math.round((sumIncome + sumOtherNetIncome) / sumBagQuantity));
                            }
                        });                        
                    });
                });
            });            
        },

        getGemPrice: function(gemQuantity, callback) {
            getGemToCoinRate(gemQuantity, function(result) {              
                findGemPrice(gemQuantity, Math.round((result.quantity/0.85)/0.85), callback);     
            });       
        },
        
        getExactGemPrice: function(gemQuantity, callback) {
            Gw2Sk.getGemPrice(gemQuantity, function(price) {
                findExactGemsPrice(gemQuantity, price, Math.round((price/gemQuantity)/2), gemQuantity, callback);
            });
        },
        
        isInt: function(value) {
            return !isNaN(value) && (function(x) { return (x | 0) === x; }) (parseFloat(value))
        },
        
        intervalTask: function(intervalMs, count, intervalCallback) {
            var callCount = 0;
            var timer = setInterval(function() {
                callCount++;            
                intervalCallback(callCount);
                if (callCount == count) {
                    clearInterval(timer);
                } 
            }, intervalMs);
            return timer;
        }
    }
}());
