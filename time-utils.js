var TimeUtils = (function () {
    
    return {
        isAgeInRange(createdTimeString, dayRange) {
            var timeDiff = new Date().getTime() - Date.parse(createdTimeString);
            var dayDiff = timeDiff / (24 * 60 * 60 * 1000);
            return dayDiff <= dayRange;
        }
        
    }
    
    
    
}());