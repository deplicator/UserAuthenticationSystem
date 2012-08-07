<?php

class profile {
	
    /*
     * Retrieves user profile record from table based on authentication.
     */
    function getProfile($userID) {
        $conn = databaseConnect();
        $sql = "SELECT * FROM profile WHERE userID = '$userID' LIMIT 1";
        $stmt = $conn->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
    }
    
    
}