<?php session_start();

$server="https://testsalestok.herokuapp.com";
//for post

if (isset($_POST["formid"])	&& $_POST["formid"] == $_SESSION["formid"]){ 
$_SESSION["formid"] = '';

	if(isset($_POST['submit'])) {
		$data=array(
				'txt_name' =>$_POST['txt_name'],
				'ddl_size' => $_POST['ddl_size'],
				'ddl_color' => $_POST['ddl_color'],
				'txt_price' =>$_POST['txt_price']
		);
		//print $_POST['txt_name']." ".$_POST['ddl_size']." ".$_POST['ddl_color']." ".$_POST['txt_price'];
		
		$url = "$server/restAPI.php/products";
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);
		$response=json_decode($response, true);
	}
		
	if(isset($_POST['delete'])) {
		$product_id=$_POST["txt_product_id"];
		$url = "$server/restAPI.php/products/?product_id=$product_id";
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($ch);
	curl_close($ch);
	$response=json_decode($response, true);
	}

}
else	
	$_SESSION["formid"]=md5(rand(0,10000000));

?>
<style>
body {
    font-family: "Trebuchet MS",Arial,Helvetica,sans-serif;}
table#tbl_test {
    border-collapse: collapse;
    border-spacing: 0;
    font-family: "Trebuchet MS",Arial,Helvetica,sans-serif;
    font-size: 16px;
    width: 100%;
}
#tbl_test td, #tbl_test thead {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
}
#tbl_test tr:nth-child(2n) {
    background-color: #f2f2f2;
}
#tbl_test thead {
    background-color: #4caf50;
    color: white;
    padding-bottom: 11px;
    padding-top: 11px;
}
</style>

 Filter :
<form method="get" action="user.php">
 <table>
	<tr>
		<td>Size</td>
		<td>: <select name="size">
				<option>All</option>
				<option>S</option>
				<option>M</option>
				<option>L</option>
				<option>XL</option>
				</select></td>
	</tr>
	<tr>
		<td>Color</td>
		<td>: <select name="color">
				<option>All</option>
				<option>Kuning</option>
				<option>Biru</option>
				<option>Hijau</option>
				</select></td>
	</tr>
	<tr>
		<td>Price</td>
		<td>: <select name="price">
				<option>All</option>
				<option value='1'>< 50,000</option>
				<option value='2'>50,000 - 100,000</option>
				<option value='3'>100,000 <</option>
				</select></td>
	</tr>
 </table>
 <input type="submit" value="Filter">
 <br><br>
 </form>

<table id="tbl_test" name="tbl_test">
<thead>
	<td>id</td>
	<td>Name</td>
	<td>Size</td>
	<td>Color</td>
	<td>Price</td>
	<td></td>
</thead>

 <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
 <input type="hidden" name="formid" value="<?php echo $_SESSION["formid"]; ?>" />
<tr>
	<td></td>
	<td><input type="text"id="txt_name" name="txt_name"></td>
	<td><select id="ddl_size" name="ddl_size">
				<option>S</option>
				<option>M</option>
				<option>L</option>
				<option>XL</option>
				</select></td>
	<td><select id="ddl_color" name="ddl_color">
				<option>Kuning</option>
				<option>Biru</option>
				<option>Hijau</option>
				</select></td>
	<td><input type="text" id="txt_price" name="txt_price"></td>
	<td><input type="submit" id="submit" name="submit" value="Add"></td>
</tr>

</form>

<?php
$size =(isset($_GET['size'])?$_GET['size']:"All");
$color =(isset($_GET['color'])?$_GET['color']:"All");
$price =(isset($_GET['price'])?$_GET['price']:"All");
$url = "$server/restAPI.php/products/?size=$size&color=$color&price=$price";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$response=json_decode($response, true);
foreach ($response as $key => $value) {
	print "<form method='post' action='". $_SERVER['PHP_SELF']."'>";
	print "<input type='hidden' name='formid' value='".$_SESSION["formid"]."'/>";
	print "<tr>";
	print "<td>".$value['product_id']."<input type='hidden' name='txt_product_id' value='".$value['product_id']."' /></td>";
	print "<td>".$value['name']."</td>";
	print "<td>".$value['size']."</td>";
	print "<td>".$value['color']."</td>";
	print "<td>".$value['price']."</td>";
	print "<td><input type='submit' id='delete' name='delete' value='Delete'>";
	print "</tr>";
	print "</form>";
}
?>
</table>
