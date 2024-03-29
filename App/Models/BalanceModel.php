<?php

namespace App\Models;

use PDO;

use \Core\View;
use \App\Mail;

/**
 * User model
 *
 * PHP version 7.0
 */
class BalanceModel extends \Core\Model
{
	public $errors = [];
	//public $start_date;
	//public $end_date;
	
	 
	 
	public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }
	

	public static function findIncome( $start_date, $end_date)
	{		
	
				
	
				$db = static::getDB();
				$stmt = $db->prepare('SELECT incomes.amount AS amount, incomes.date_of_income AS date, incomes.income_comment AS comment, incomes_category_assigned_to_users.name AS category  FROM incomes,incomes_category_assigned_to_users  WHERE incomes.user_id=:user_id  AND incomes.income_category_assigned_to_user_id=incomes_category_assigned_to_users.id AND  incomes.date_of_income  BETWEEN :start_date AND :end_date ORDER BY incomes_category_assigned_to_users.name DESC ');
				
				
				$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
				$stmt->bindValue(':start_date', $start_date, PDO::PARAM_STR);
				$stmt->bindValue(':end_date', $end_date, PDO::PARAM_STR);
				
				
				$stmt->setFetchMode(PDO::FETCH_ASSOC);
				//$stmt->setFetchMode(PDO::FETCH_CLASS,get_called_class());
				
				
				$stmt->execute();

				$result = $stmt->fetchAll();
				
						
		return $result;
	}

	public static function findExpense($start_date, $end_date)
	{		
	
				$db = static::getDB();
				$stmt = $db->prepare('SELECT expenses.amount AS amount, expenses.date_of_expense AS date, expenses.expense_comment AS comment, expenses_category_assigned_to_users.name AS category  FROM expenses,expenses_category_assigned_to_users  WHERE   expenses.user_id=:user_id  AND expenses.expense_category_assigned_to_user_id=expenses_category_assigned_to_users.id AND  expenses.date_of_expense  BETWEEN :start_date AND :end_date ORDER BY expenses_category_assigned_to_users.name DESC ');
				
				
				$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
				$stmt->bindValue(':start_date', $start_date, PDO::PARAM_STR);
				$stmt->bindValue(':end_date', $end_date, PDO::PARAM_STR);
				
				
				$stmt->setFetchMode(PDO::FETCH_ASSOC);
				//$stmt->setFetchMode(PDO::FETCH_CLASS,get_called_class());
				
				
				$stmt->execute();

				$result = $stmt->fetchAll();
				
						
		return $result;
	}


	public static function findIncomeSums($start_date, $end_date)
	{
				$db = static::getDB();
				
			
				$stmt = $db->prepare('SELECT incomes_category_assigned_to_users.name AS name, SUM(incomes.amount) AS sum FROM incomes_category_assigned_to_users,incomes WHERE   incomes.user_id=:user_id AND incomes.income_category_assigned_to_user_id=incomes_category_assigned_to_users.id AND incomes.date_of_income  BETWEEN :start_date AND :end_date GROUP BY incomes_category_assigned_to_users.name ORDER BY sum DESC ');
				
				
				$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
				$stmt->bindValue(':start_date', $start_date, PDO::PARAM_STR);
				$stmt->bindValue(':end_date', $end_date, PDO::PARAM_STR);
				
				
				$stmt->setFetchMode(PDO::FETCH_ASSOC);
				//$stmt->setFetchMode(PDO::FETCH_CLASS,get_called_class());
				
				
				$stmt->execute();

				$result = $stmt->fetchAll();
				
						
		return $result;
		
	}
	public static function findExpensesSums($start_date, $end_date)
	{
				$db = static::getDB();
				
			
				$stmt = $db->prepare('SELECT expenses_category_assigned_to_users.name AS name, SUM(expenses.amount) AS sum FROM expenses_category_assigned_to_users,expenses WHERE expenses.user_id=:user_id AND expenses.expense_category_assigned_to_user_id=expenses_category_assigned_to_users.id AND expenses.date_of_expense  BETWEEN :start_date AND :end_date GROUP BY expenses_category_assigned_to_users.name ORDER BY sum DESC ');
				
				
				$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
				$stmt->bindValue(':start_date', $start_date, PDO::PARAM_STR);
				$stmt->bindValue(':end_date', $end_date, PDO::PARAM_STR);
				
				
				$stmt->setFetchMode(PDO::FETCH_ASSOC);
				//$stmt->setFetchMode(PDO::FETCH_CLASS,get_called_class());
				
				
				$stmt->execute();

				$result = $stmt->fetchAll();
				
						
		return $result;
	}
	public static function findOverallExpenseSum($start_date, $end_date)
	{
		$array=BalanceModel::findExpensesSums($start_date, $end_date);
						$sum=BalanceModel::sumsAllItems($array);
						return $sum;
	}
	public static function findOverallIncomeSum($start_date, $end_date)
	{
		$array=BalanceModel::findIncomeSums($start_date, $end_date);
						$sum=BalanceModel::sumsAllItems($array);
						return $sum;
	}
	public static function checkBalance($expenseSum, $incomeSum)
	{
		$balance=$incomeSum-$expenseSum;
		return $balance;
	}
	public static function sumsAllItems($array)
	{
		$sum=0;

		foreach($array as $item){
			foreach($item as $key=>$value){
										
										  
				$sum+= floatval($value);
										
				}
		}
		
		return $sum;
	}
	
	
	public static function getCategorys($table_name)
	{
			$sql = 'SELECT * FROM '.$table_name.' WHERE user_id=:user_id AND active=1';
			
			$db = static::getDB();
			
			$stmt = $db->prepare($sql);
			$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
			//$stmt->bindValue(':table_name', $table_name, PDO::PARAM_STR);

			$stmt->setFetchMode(PDO::FETCH_ASSOC);

			$stmt->execute();
			
			
			$result=$stmt->fetchAll();
			return $result;
			
	}
	public static function check_if_category_exist($table_name, $category_name)
	{
		
		
		$categorys=BalanceModel::getCategorys($table_name);
		
		foreach($categorys as $item )
		{
			 foreach( $item  as $key => $value)
			 {
			  if($value==$category_name)
			  {
				  return true;
			  }
				 
			 }
    
		}
		return false;
		
		
	}
	public static function delete_category($table_name, $data)
	{
	 $sql = 'UPDATE '.$table_name.' SET active=0 WHERE user_id=:user_id AND name=:name';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':name',$data, PDO::PARAM_STR);

        return $stmt->execute();
		
	}


	public static function setNewName($table_name, $catID,$newName)
	{
	 $sql = 'UPDATE '.$table_name.' SET name=:newName WHERE user_id=:user_id AND id=:id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':id',$catID, PDO::PARAM_INT);
		$stmt->bindValue(':newName',$newName, PDO::PARAM_STR);
        

        return $stmt->execute();
		
	}
	/*
	public static function setNewName($table_name, $oldName,$newName)
	{
	 $sql = 'UPDATE '.$table_name.' SET name=:newName WHERE user_id=:user_id AND name=:oldName';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':oldName',$oldName, PDO::PARAM_STR);
        $stmt->bindValue(':newName',$newName, PDO::PARAM_STR);

        return $stmt->execute();
		
	}
	*/
	public static function setLimitForCategory($limit,$categoryID)
	{
	 $sql = 'UPDATE expenses_category_assigned_to_users SET category_limit=:limit WHERE  id=:categoryID';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        //$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':categoryID',$categoryID, PDO::PARAM_STR);
       

        return $stmt->execute();
		
	}
	public static function checkLimit($catID)
	{
		$sql='SELECT category_limit FROM expenses_category_assigned_to_users WHERE id=:id ';
		
		$db = static::getDB();
         $stmt = $db->prepare($sql);
		 
		//$stmt->bindValue(':user_id',$id, PDO::PARAM_INT);
		$stmt->bindValue(':id', $catID, PDO::PARAM_INT);
		//$stmt->bindValue(':name', $category_name, PDO::PARAM_STR);
		//$stmt->bindValue(':table_name', $table_name, PDO::PARAM_STR);
		 
		 		$stmt->execute();

				$result = $stmt->fetch();
				return $result;	
	}
	public static function getAmount($catID,$date)
	{
		
		$first_day=date("Y-m-01", strtotime($date));
		$last_day=date("Y-m-t", strtotime($date));
		
	//$first_day='2022-01-01';
	//	$last_day='2022-11-30';
		
		
				$sql='SELECT SUM(amount) AS sum FROM expenses WHERE expense_category_assigned_to_user_id=:id AND date_of_expense BETWEEN :first_day AND :last_day';
		
		$db = static::getDB();
         $stmt = $db->prepare($sql);
		 
		//$stmt->bindValue(':user_id',$id, PDO::PARAM_INT);
		$stmt->bindValue(':id', $catID, PDO::PARAM_INT);
		$stmt->bindValue(':first_day', $first_day, PDO::PARAM_STR);
		$stmt->bindValue(':last_day', $last_day, PDO::PARAM_STR);
		
		//$stmt->bindValue(':name', $category_name, PDO::PARAM_STR);
		//$stmt->bindValue(':table_name', $table_name, PDO::PARAM_STR);
		 
		 		$stmt->execute();

				$result = $stmt->fetch();
				return $result;	
		
		
		
	}
	


	public static function  get_user_category_id($table_name, $category_name)
	{
		
		
		$sql='SELECT id FROM '.$table_name.' WHERE name=:name AND user_id=:user_id ';
		
		$db = static::getDB();
         $stmt = $db->prepare($sql);
		 
		$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
		$stmt->bindValue(':name', $category_name, PDO::PARAM_STR);
		//$stmt->bindValue(':table_name', $table_name, PDO::PARAM_STR);
		 
		 		$stmt->execute();

				$result = $stmt->fetch();
				return $result[0];	
	}
	public function validate()
	{
		
		$date_pattern='/^(20[1-2][1-9])-(0[1-9]|1[0-2])-(0[1-9]|1\d|2\d|3[0-1])$/';
		$text_area_check=preg_match('/^[A-Za-z0-9 ,.]*$/',$this->comment);
		
		$date_Y_m_d=explode("-",$this->date);
		$year=$date_Y_m_d[0];
		$month=$date_Y_m_d[1];
		$day=$date_Y_m_d[2];
		
		$date=$this->date;
		if(!preg_match($date_pattern, $date)){
			$this->errors[] = 'Wrong format';
		}
		
		
		if($this->amount==""){
		$this->errors[] = 'Amount is required';
        }
		
		if (is_numeric($this->amount=="")) {
		$this->errors[] = 'This field must be number';	
		}
		
		if($this->date>time()){
		$this->errors[] = 'This date is in future';
		}
		
		if($this->date==""){
		$this->errors[] = 'Date is required';
		}
		
		if(!checkdate($month, $day, $year)){
		$this->errors[] = 'Wrong date';	 
		}
		
		if(!$text_area_check){
			$this->errors[]='wrong text';
		}
		
		

	}
	
		/*test do PHP MAILER
	*/
	public static function testMailer()
	{
		Mail::sendtest();
	}
	
	
	
	
}