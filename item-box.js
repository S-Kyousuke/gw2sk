class ItemBox {
    
    constructor() {    
        this.box = $('<div class="item-box"></div');
        
        this.itemIconTag = $('<img src="" class="item">');
        this.itemNameTag = $('<span class="item-name">...</span>');
                
        var table = $('<table class="item-box"></table>');
        var header = $('<thead></thead>');
        this.body = $('<tbody></tbody>');
        
        var itemNameRow = $('<tr></tr>');
        var itemNameCell = $('<td colspan="2"></td>');
        
        itemNameCell.append(this.itemIconTag);
        itemNameCell.append(this.itemNameTag);
        itemNameRow.append(itemNameCell);            
        header.append(itemNameRow);
        
        table.append(header);
        table.append(this.body);
        
        this.box.append(table);
    }
    
    setIcon(itemIcon) {
        var newItemIconTag = $('<img src="' + itemIcon + '" class="item">');
        this.itemIconTag.replaceWith(newItemIconTag);
        this.itemIconTag = newItemIconTag;
    }  

    setName(itemName) {
        var newItemNameTag = $('<span class="item-name">' + itemName + '</span>');
        this.itemNameTag.replaceWith(newItemNameTag);
        this.itemNameTag = newItemNameTag;
    }
    
    getBox() {
        return this.box;
    }
    
}

class ItemPriceBox extends ItemBox {
    
    constructor() {
        super();       
        
        this.priceRow = $('<tr><td>Current Price:</td><td>...</td></tr>');
        this.dayAvgPriceRow = $('<tr><td>Day Avg Price:</td><td>...</td></tr>');
        this.weekAvgPriceRow = $('<tr><td>Week Avg Price:</td><td>...</td></tr>');
        
        this.body.append(this.priceRow);            
        this.body.append(this.dayAvgPriceRow);            
        this.body.append(this.weekAvgPriceRow);
    }
    
    setPrice(value) {
        var newPriceRow = $('<tr><td>Current Price:</td><td>' + ViewUtils.getCoinValueDivTag(value, false)  + '</td></tr>');
        this.priceRow.replaceWith(newPriceRow);
        this.priceRow = newPriceRow;
    }
    
    setDayAvgPrice(value) {
        var newDayAvgPriceRow = $('<tr><td>Day Avg Price:</td><td>' + ViewUtils.getCoinValueDivTag(value, false) + '</td></tr>');
        this.dayAvgPriceRow.replaceWith(newDayAvgPriceRow);
        this.dayAvgPriceRow = newDayAvgPriceRow;        
    }
    
    setWeekAvgPrice(value) {
        var newWeekAvgPriceRow = $('<tr><td>Week Avg Price:</td><td>' + ViewUtils.getCoinValueDivTag(value, false) + '</td></tr>');
        this.weekAvgPriceRow.replaceWith(newWeekAvgPriceRow)
        this.weekAvgPriceRow = newWeekAvgPriceRow;
    }
}

class ItemIncomeBox extends ItemBox {
    
    constructor() {
        super();       
        
        this.incomeRow = $('<tr><td>Current Income:</td><td>...</td></tr>');
        this.dayAvgIncomeRow = $('<tr><td>Day Avg Income:</td><td>...</td></tr>');
        this.weekAvgIncomeRow = $('<tr><td>Week Avg Income:</td><td>...</td></tr>');
        
        this.body.append(this.incomeRow);            
        this.body.append(this.dayAvgIncomeRow);            
        this.body.append(this.weekAvgIncomeRow);
    }
    
    setIncome(value) {
        var newIncomeRow = $('<tr><td>Current Income:</td><td>' + ViewUtils.getCoinValueDivTag(value, false)  + '</td></tr>');
        this.incomeRow.replaceWith(newIncomeRow);
        this.incomeRow = newIncomeRow;
    }
    
    setDayAvgIncome(value) {
        var newDayAvgIncomeRow = $('<tr><td>Day Avg Income:</td><td>' + ViewUtils.getCoinValueDivTag(value, false) + '</td></tr>');
        this.dayAvgIncomeRow.replaceWith(newDayAvgIncomeRow);
        this.dayAvgIncomeRow = newDayAvgIncomeRow;        
    }
    
    setWeekAvgIncome(value) {
        var newWeekAvgIncomeRow = $('<tr><td>Week Avg Income:</td><td>' + ViewUtils.getCoinValueDivTag(value, false) + '</td></tr>');
        this.weekAvgIncomeRow.replaceWith(newWeekAvgIncomeRow)
        this.weekAvgIncomeRow = newWeekAvgIncomeRow;
    }
}
    