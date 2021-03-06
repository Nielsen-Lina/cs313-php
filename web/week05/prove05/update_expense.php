<?php

require("dbConnect.php");
$db = get_db();

$company_name = htmlspecialchars($_POST['company_name']);
$transaction_amount = htmlspecialchars($_POST['transaction_amount']);
$purchase_date = htmlspecialchars($_POST['purchase_date']);

$new_page = "change.php";

$company_name = ucfirst($company_name);

$expense_chk = !empty($_POST['expense_chk']) ? $_POST['expense_chk'] : [];

foreach ($expense_chk as $expense)
{
  if (isset($_POST["update_company_name"]))
  {
    if (!isset($company_name) || trim($company_name) == '')
    {
      $new_page = "error.php";
    }
    else
    {
      $stmtId = $db->prepare('SELECT detail_id FROM detail WHERE company_name=:company_name');
      $stmtId->bindValue(':company_name', ucfirst($company_name));
      $stmtId->execute();
      $id = $stmtId->fetch(PDO::FETCH_ASSOC);

      $stmt = $db->prepare('UPDATE expense SET detail_id=:detail_id WHERE expense_id=:expense_id');
      $stmt->bindValue(':expense_id', (int)$expense);
      $stmt->bindValue(':detail_id', $id['detail_id']);
      $stmt->execute();
    }
  }
  elseif (isset($_POST["update_transaction_amount"]))
  {
    if (!isset($transaction_amount) || trim($transaction_amount) == '')
    {
      $new_page = "error.php";
    }
    else
    {
      $stmt = $db->prepare('UPDATE expense SET transaction_amount=:transaction_amount WHERE expense_id=:expense_id');
      $stmt->bindValue(':expense_id', (int)$expense);
      $stmt->bindValue(':transaction_amount', $transaction_amount);
      $stmt->execute();
    }
  }
  elseif (isset($_POST["update_date"]))
  {
    if (!isset($purchase_date) || trim($purchase_date) == '')
    {
      $new_page = "error.php";
    }
    else
    {
      $stmt = $db->prepare('UPDATE expense SET purchase_date=:purchase_date WHERE expense_id=:expense_id');
      $stmt->bindValue(':expense_id', (int)$expense);
      $stmt->bindValue(':purchase_date', $purchase_date);
      $stmt->execute();
    }
  }
  
}

header("Location: $new_page");
die();

?>