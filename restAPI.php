<?php
// Connect to database
	$connection=mysqli_connect('us-cdbr-iron-east-04.cleardb.net','b25c58f2d6b8cf','d4895c94','heroku_38b7b227b5e55e2');

	$request_method=$_SERVER["REQUEST_METHOD"];
	switch($request_method)
	{
		case 'GET':
			// Retrive Products
			$str_where="";
			if($_GET["size"] !='All')
				$str_where="size='".$_GET["size"]."'";
			if($_GET["color"] !='All')
				$str_where=($str_where!=""?"$str_where and ":'')."color='".$_GET["color"]."'";
			if($_GET["price"] !='All')
				{
					switch($_GET["price"]){
					case 1:
						$price= " price < 50000";break;
					case 2:
						$price= " price between 50000 and 100000";break;
					case 3:
						$price= " price > 100000";break;
					}
					
				$str_where=($str_where!=""?"$str_where and ":'')."$price";
				}
			
				
			get_products($str_where);
			break;
		case 'POST':
			// Insert Product
			insert_product();
			break;
		case 'PUT':
			// Update Product
			$product_id=intval($_GET["product_id"]);
			update_product($product_id);
			break;
		case 'DELETE':
			// Delete Product
			$product_id=intval($_GET["product_id"]);
			delete_product($product_id);
			break;
		default:
			// Invalid Request Method
			header("HTTP/1.0 405 Method Not Allowed");
			break;
	}

	function insert_product()
	{
		global $connection;
		$txt_name=$_POST["txt_name"];
		$ddl_size=$_POST["ddl_size"];
		$ddl_color=$_POST["ddl_color"];
		$txt_price=$_POST["txt_price"];
		$query="INSERT INTO products SET name='{$txt_name}', size='{$ddl_size}', color='{$ddl_color}', price='{$txt_price}'";
		if(mysqli_query($connection, $query))
		{
			$response=array(
				'status' => 1,
				'status_message' =>'Product Added Successfully.'
			);
		}
		else
		{
			$response=array(
				'status' => 0,
				'status_message' =>'Product Addition Failed.'
			);
		}
		header('Content-Type: application/json');
		echo json_encode($response);
	}
	function get_products($str_where="")
	{
		global $connection;
		$query="SELECT * FROM products";
		if($str_where != "")
		{
			$query.=" WHERE $str_where";
		}
		$response=array();
		$result=mysqli_query($connection, $query);
		while($row=mysqli_fetch_array($result))
		{
			$response[]=$row;
		}
		header('Content-Type: application/json');
		echo json_encode($response);
	}
	function delete_product($product_id)
	{
		global $connection;
		$query="DELETE FROM products WHERE product_id=".$product_id;
		if(mysqli_query($connection, $query))
		{
			$response=array(
				'status' => 1,
				'status_message' =>'Product Deleted Successfully.'
			);
		}
		else
		{
			$response=array(
				'status' => 0,
				'status_message' =>'Product Deletion Failed.'
			);
		}
		header('Content-Type: application/json');
		echo json_encode($response);
	}
	function update_product($product_id)
	{
		global $connection;
		parse_str(file_get_contents("php://input"),$post_vars);
		$name=$post_vars["name"];
		$size=$post_vars["size"];
		$color=$post_vars["color"];
		$price=$post_vars["price"];
		$query="UPDATE products SET name='{$name}', size={$size}, price={$price},  color='{$color}' WHERE product_id=".$product_id;
		if(mysqli_query($connection, $query))
		{
			$response=array(
				'status' => 1,
				'status_message' =>'Product Updated Successfully.'
			);
		}
		else
		{
			$response=array(
				'status' => 0,
				'status_message' =>'Product Updation Failed.'
			);
		}
		header('Content-Type: application/json');
		echo json_encode($response);
	}

	// Close database connection
	mysqli_close($connection);
?>
