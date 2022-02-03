<?php
	
	function exeSql($query)
	{
		$rows = false;
		
		try {
			
			$hostDb='mysql:host=127.0.0.1;dbname='.Config::$db_name;
			$connection = new PDO($hostDb, Config::$username, Config::$password);
			$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$result = $connection->query($query);	
			
			if (strpos($query, 'SELECT') !== false)
			{
				$rows = $result->fetchAll();
			}
			
			//echo var_dump($rows);
			//echo $query.'<br/>';
			
		} catch (Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}

		return $rows;
	}
    
    //select record
    function select_product($product_id = '',$name = '')
    {
		
		$product_id = filter_var($product_id, FILTER_SANITIZE_NUMBER_INT);
		
		//check for product id
		if($product_id == null || $product_id <= 0)
        {
            return;
        }
	
        $query = "SELECT p1.ID as product_id, p1.post_title as product_title, p1.post_content as product_desc, p2.post_excerpt as product_color, p2.ID as product_variation_id, ";
		$query .= "(select meta_value from wp_postmeta where meta_key='_variation_description' and post_id=p2.ID limit 1) as product_hex, ";
		$query .= "(select meta_value from wp_postmeta where meta_key='_thumbnail_id' and post_id=p2.ID limit 1) as product_thumb_id ";
		$query .= "from wp_posts p1 ";
		$query .= "inner join wp_posts p2 on p1.ID=p2.post_parent ";
		$query .= "where p1.post_status = 'publish' and p1.post_type='product' ";
		$query .= "and p2.post_status = 'publish' and p2.post_type='product_variation' ";
		$query .= "and p1.ID = ". $product_id;
		$query .= " ORDER BY p2.menu_order";
        
        if(trim($name) != "")
        {
            $query .= "AND p1.post_title = '".trim($name)."' ";
        }
        
        $result = exeSql($query);	
		
		if(isset($result))
		{
			if(empty($result))
			{
				return false;
			}
			else
			{
				return $result;
			}
		}
		else
		{
			return false;
		}
		
		//return $result;
    }
	

	function select_product_media_by_product($product_id)
    {
		
		$product_id = filter_var($product_id, FILTER_SANITIZE_NUMBER_INT);
		
		//check for product id
		if($product_id == null || $product_id <= 0)
        {
            return;
        }
		
        $query = "SELECT meta_value FROM wp_postmeta WHERE meta_key ='_wp_attached_file' AND post_id='".$product_id."'";
		
        $result = exeSql($query);
		return $result[0];
    }
	
	
?>