<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\BalanceModel;

class Profile extends Authenticated
{
	protected function before()
	{
		parent::before();
		$this->user=Auth::getUser();
	}
	
	public function showAction()
	{
		View::renderTemplate('Profile/show.html',[
		'user'=>$this->user
		]);
		
	}

	public function editAction()
	{
		View::renderTemplate('Profile/edit.html',[
		'user'=>$this->user
		]);
	}
	 
	public function editCategory($name) 
	{
	if (isset($_POST['new'])) {
	
			View::renderTemplate('Profile/Add'.$name.'Category.html');
	
		
	
	} else if (isset($_POST['edit'])) {
		
		if(isset($_POST['category'])){
			$_SESSION['name_to_edit']=$_POST['category'];
			View::renderTemplate('Profile/edit'.$name.'.html');
			
		}else{
			Flash::addMessage('Selecet category first', Flash::WARNING);
			$this->redirect('/profile/'.$name.'Category');
		}
    
	} else if(isset($_POST['delete'])){
		if(isset($_POST['category'])){
			
			Profile::deleteCategorys($name);
			Flash::addMessage('Item removed');
			$this->redirect('/profile/'.$name.'Category');
			
		}else{
			Flash::addMessage('Selecet category first', Flash::WARNING);
			$this->redirect('/profile/'.$name.'Category');
		}
    
	}
	}
	public function deleteCategorys($name)
	{
		$items=BalanceModel::getCategorys(''.$name.'s_category_assigned_to_users');
		if(count($items)>1)
		{
		$data=$_POST['category'];
		
		BalanceModel::delete_category(''.$name.'s_category_assigned_to_users',$data);
		

		}else{
			Flash::addMessage('Cant delete last item ',Flash::WARNING);
			$this->redirect('/profile/'.$name.'Category');
		}
		
	}
	
	public function editExpenseCategoryAction()
	{
		Profile::editCategory('Expense');
	}

	public function ExpenseCategoryAction()
	{
		View::renderTemplate('Profile/EditCategory.html',[
		'user'=>$this->user,
		'categorys'=>BalanceModel::getCategorys('expenses_category_assigned_to_users'),
		'action_title'=>'EditExpenseCategory',
		'title'=>'Expense'
		]);
	}
	public function AddExpenseCategoryAction()
	{
		$data=$_POST['category'];
		
		if(BalanceModel::check_if_category_exist('expenses_category_assigned_to_users',$data)){
		
		if($this->user->AddExpenseCategorys($data)){
			Flash::addMessage('Changes saved');
			$this->redirect('/profile/show');
		}else{
			
			View::renderTemplate('Profile/edit.html',[
				'user'=>$this->user
			]);
			}
		}else{
			Flash::addMessage('this category alerady exist', Flash::WARNING);
			$this->redirect('/profile/ExpenseCategory');
		}
	}




	public function saveEditExpenseCategorysAction()
	{
		if(BalanceModel::check_if_category_exist('expenses_category_assigned_to_users',$_POST['category'])){
		BalanceModel::setNewName('expenses_category_assigned_to_users',$_SESSION['name_to_edit'],$_POST['category']);
		$_SESSION['CATEGORY_TO_EDIT']="";
		Flash::addMessage('Name changed');
		$this->redirect('/profile/show');
		}	else{
			Flash::addMessage('this category alerady exist', Flash::WARNING);
			$this->redirect('/profile/ExpenseCategory');
		}
	}
	
	public function IncomeCategoryAction()
	{
		
		View::renderTemplate('Profile/EditCategory.html',[
		'user'=>$this->user,
		'categorys'=>BalanceModel::getCategorys('incomes_category_assigned_to_users'),
		'action_title'=>'editIncomesCategory',
		'title'=>'Income'
		]);
		
	}
	public function editIncomesCategoryAction()
	{
		Profile::editCategory('Income');
	}
	public function AddIncomeCategoryAction()
	{
		
		$new_category=$_POST['category'];
		
		if(BalanceModel::check_if_category_exist('incomes_category_assigned_to_users',$new_category)){
		
		if($this->user->AddIncomeCategory($_POST)){
			
			Flash::addMessage('Changes saved');
			$this->redirect('/profile/show');
		
		}else{
			
			View::renderTemplate('Profile/edit.html',[
				'user'=>$this->user
			]);
			}
		}else{
			Flash::addMessage('this category alerady exist', Flash::WARNING);
			$this->redirect('/profile/IncomeCategory');
		}
			
	}
	public function deleteIncomeCategorys()
	{
		$items=BalanceModel::getCategorys('incomes_category_assigned_to_users');
		if(count($items)>1)
		{
		$data=$_POST['category'];
		
		BalanceModel::delete_category('incomes_category_assigned_to_users',$data);
		
		Flash::addMessage('Category deleted');
		$this->redirect('/profile/show');
		}else{
			Flash::addMessage('Cant delete last item ',Flash::WARNING);
			$this->redirect('/profile/DeleteIncome');
		}
		
	}
	public function saveEditIncomesCategorysAction ()
	{
		if(BalanceModel::check_if_category_exist('incomes_category_assigned_to_users',$_POST['category'])){
		BalanceModel::setNewName('incomes_category_assigned_to_users',$_SESSION['name_to_edit'],$_POST['category']);
		$_SESSION['CATEGORY_TO_EDIT']="";
		Flash::addMessage('Name changed');
		$this->redirect('/profile/show');
		}	else{
			Flash::addMessage('this category alerady exist', Flash::WARNING);
			$this->redirect('/profile/IncomeCategory');
		}
	}

	public function PaymentCategoryAction()
	{
		View::renderTemplate('Profile/PaymentCategory.html',[
		'user'=>$this->user
		]);
	}
	public function AddPaymentCategoryAction()
	{
		
		$new_category=$_POST['category'];
		
		if(BalanceModel::check_if_category_exist('payment_methods_assigned_to_users',$new_category)){
		if($this->user->AddPaymentCategory($_POST)){
			Flash::addMessage('Changes saved');
			$this->redirect('/profile/show');
		}else{
			
			View::renderTemplate('Profile/edit.html',[
				'user'=>$this->user
			]);
			}
	}else{
		Flash::addMessage('this category alerady exist', Flash::WARNING);
			$this->redirect('/profile/IncomeCategory');
	}
	}
	public function deletePaymentCategorysAction()
	{
		$items=BalanceModel::getCategorys('payment_methods_assigned_to_users');
		if(count($items)>1)
		{
		$data=$_POST['category'];
		
		
		
		
		BalanceModel::delete_category('payment_methods_assigned_to_users',$data);
		
		Flash::addMessage('Category deleted');
		$this->redirect('/profile/show');
		}else{
			Flash::addMessage('Cant delete last item ',Flash::WARNING);
			$this->redirect('/profile/DeletePayment');
		}
		
	}
	public function DeletePaymentAction()
	{
		View::renderTemplate('Profile/DeletePayment.html',[
		'user'=>$this->user,
		'payment_methods'=>BalanceModel::getCategorys('payment_methods_assigned_to_users')
		]);
	}
	
	
	public function updateAction()
	{
		
		
		if($this->user->updateProfile($_POST)){
			Flash::addMessage('Changes saved');
			$this->redirect('/profile/show');
		}else{
			
			View::renderTemplate('Profile/edit.html',[
				'user'=>$this->user
			]);
			}
	}
	
	
	
	
	
}