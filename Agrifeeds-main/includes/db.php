<?php
class database{

    function opencon(): PDO{
        return new PDO(
            dsn: 'mysql:host=localhost;
            dbname=agri_db',
            username: 'root',
            password: '');
    }

    function addProduct($productName, $category, $description, $price, $stock) {

        $con = $this->opencon();
        
        try {
            $con->beginTransaction();

            $stmt = $con->prepare("INSERT INTO products (Prod_Name, Prod_Cat, Prod_Desc, Prod_Price, Prod_Stock) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$productName, $category, $description, $price, $stock]);
            
            $productID = $con->lastInsertId();

            $con->commit();

            return $productID;

        } catch (PDOException $e) {

            $con->rollback();
            return false;

        }

    }

    function viewProducts() {

        $con = $this->opencon();
        return $con->query("SELECT * FROM products")->fetchAll();

    }


    
       function updateProduct($name, $category, $description, $price, $stock, $id){
    try    {
        $con = $this->opencon();
        $con->beginTransaction();
        $query = $con->prepare("UPDATE products SET Prod_Name = ?, Prod_Cat = ?, Prod_Desc = ?, Prod_Price = ? , Prod_Stock = ? WHERE ProductID = ? ");
        $query->execute([$name, $category, $description, $price, $stock, $id]);
        $con->commit();
        return true;

    } catch (PDOException $e) {
       
         $con->rollBack();
        return false; 
    }
    }

    function deleteProduct($id) {
    try {
        $con = $this->opencon();
        $con->beginTransaction();
        $stmt = $con->prepare("DELETE FROM products WHERE ProductID = ?");
        $result = $stmt->execute([$id]);
        $con->commit();
        return $result;
    } catch (PDOException $e) {
        if (isset($con)) $con->rollBack();
        return false;
    }
}

function addCustomer($customerName, $contactInfo, $discountRate) {
 
        $con = $this->opencon();
       
        try {
            $con->beginTransaction();
 
            $stmt = $con->prepare("INSERT INTO customer (Cust_Name, Cust_CoInfo, Cust_DiscRate) VALUES (?, ?, ?)");
            $stmt->execute([$customerName, $contactInfo, $discountRate]);
           
            $custID = $con->lastInsertId();
 
            $con->commit();
 
            return $custID;
 
        } catch (PDOException $e) {
 
            $con->rollback();
            return false;
 
        }
 
    }
 
    function viewCustomers() {
 
        $con = $this->opencon();
        return $con->query("SELECT * FROM customers")->fetchAll();
 
    }
    

}
?>