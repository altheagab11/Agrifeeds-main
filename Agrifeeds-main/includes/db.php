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

    

}
?>