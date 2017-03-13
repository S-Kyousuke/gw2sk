<?php        
    header('Content-Type: application/json');      
    $json_result = array();    
    
    if( !isset($_POST['functionName']) ) { $json_result['error'] = 'No function name!'; }
    if( !isset($json_result['error']) ) {
        switch($_POST['functionName']) {
            case 'getItemName':
                $json_result['name'] = getItemName($_POST['arguments'][0]);
                break;
            case 'addItem':
                $agruments = $_POST['arguments'];
                $response = addItem($agruments[0], $agruments[1], $agruments[2], $agruments[3], toBoolean($agruments[4]), toBoolean($agruments[5]), $agruments[6]);
                if(isset($response['error'])) {
                    $json_result['error'] = 'addItem() error: '.$response['error'].'!';
                }
                break;
            case 'getItemIcon':
                $json_result['icon'] = getItemIcon($_POST['arguments'][0]);
                break;                
            case 'getItemListings':             
                $json_result['itemListings'] = getItemListings($_POST['arguments'][0]);
                break;    
            case 'getOpenedBags':
                $json_result['openedBags'] = getOpenedBags($_POST['arguments'][0]);
                break;   
            case 'getBagIds':
                $json_result['bagIds'] = getBagIds();            
                break;
            default:
                $json_result['error'] = 'Not found function '.$_POST['function_name'].'!';
        }
    }    
    echo json_encode($json_result);    
    
    function connect() {
        include 'secret.php';        
        $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_SCHEMA);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        return $conn;
    }
    
    function toBoolean($var) {
        return filter_var($var, FILTER_VALIDATE_BOOLEAN);
    }
    
    function getItemName($id) {
        $conn = connect();
        $sql = "SELECT Name FROM items WHERE id=?";
        
        $stmt = $conn->prepare($sql);  
        $stmt->bind_param("i", $id);
        $stmt->execute();        
        $stmt->bind_result($name);
        $stmt->fetch();
        $stmt->close();
        $conn->close();        
        return $name;
    }
    
    function getItemIcon($id) {
        $conn = connect();
        $sql = "SELECT Icon FROM items WHERE id=?";
        
        $stmt = $conn->prepare($sql);  
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($icon);
        $stmt->fetch();
        $stmt->close();
        $conn->close();        
        return $icon;
    }
    
    function addItem($id, $name, $rarity, $icon, $boundOnAcquire, $noSell, $vendorValue) {
        $conn = connect();
        $sql = "INSERT INTO items (Id, Name, Rarity, Icon, BoundOnAcquire, NoSell, VendorValue) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $response = array();
        $stmt = $conn->prepare($sql);
        
        if ($stmt === false)  {
            $response['error'] = 'prepare() failed: '.htmlspecialchars($conn->error);
        }        
        else if ($stmt->bind_param("isssiii", $id, $name, $rarity, $icon, $boundOnAcquire, $noSell, $vendorValue) === false ) {
            $response['error'] = 'bind_param() failed: '.htmlspecialchars($stmt->error);
        }
        else if ($stmt->execute() === false) {
            $response['error'] = 'execute() failed: '.htmlspecialchars($stmt->error);
        }
        
        $stmt->close();
        $conn->close();        
        
        return $response;
    }
    
    function getItemListings($bagId) {
        $conn = connect();
        $sql = "SELECT ItemId, Quantity, ResearchNumber FROM item_listings WHERE BagId=?";
        
        $stmt = $conn->prepare($sql);            
        $stmt->bind_param("i", $bagId);
        $stmt->execute();
        $stmt->bind_result($itemId, $quantity, $researchNumber);
        
        $item_listings = array();        
        while ($stmt->fetch()) {
            $item_listing = array();
            $item_listing['itemId'] = $itemId;            
            $item_listing['quantity'] = $quantity;            
            $item_listing['researchNumber'] = $researchNumber;
            $item_listings[] = $item_listing;
        }        
        $stmt->close();
        $conn->close();        
        return $item_listings;
    }
    
    function getOpenedBags($bagId) {
        $conn = connect();        
        $sql = "SELECT OtherNetIncome, Quantity, ResearchNumber FROM opened_bags WHERE BagId=?";
        
        $stmt = $conn->prepare($sql);            
        $stmt->bind_param("i", $bagId);        
        $stmt->execute();
        $stmt->bind_result($otherNetIncome, $quantity, $researchNumber);
        
        $opened_bags = array();        
        while ($stmt->fetch()) {
            $opened_bag = array();
            $opened_bag['otherNetIncome'] = $otherNetIncome;            
            $opened_bag['quantity'] = $quantity;            
            $opened_bag['researchNumber'] = $researchNumber;
            $opened_bags[] = $opened_bag;
        }        
        $stmt->close();
        $conn->close();        
        return $opened_bags;
    }
    
    function getBagIds() {
        $conn = connect();
        
        $sql = "SELECT DISTINCT BagId FROM opened_bags";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $stmt->bind_result($bagId);
        
        $bag_ids = array();
        while ($stmt->fetch()) {
            $bag_ids[] = $bagId;
        }        
        $stmt->close();
        $conn->close();        
        return $bag_ids;
    }
?>