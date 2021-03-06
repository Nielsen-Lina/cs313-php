<?php

session_start();

if (!empty($_SESSION['username']) && !empty($_SESSION['password']))
{
    require("dbConnect.php");
    $db = get_db();
}
else
{
    header("Location: login.php");
    die();
}

$company_name = htmlspecialchars($_POST['company_name']);
$transaction_amount = htmlspecialchars($_POST['transaction_amount']);
$purchase_date = htmlspecialchars($_POST['purchase_date']);
$match = false;

if (!isset($transaction_amount) || trim($transaction_amount) == '' || !isset($company_name) || trim($company_name) == '' || !isset($purchase_date) || trim($purchase_date) == '')
{
  include('error.php');
}
else
{
  $res = $db->query('SELECT count(*) FROM expense');
  $expense_id = $res->fetchColumn();
  (int)($expense_id += 1);

  $stmtName = $db->prepare('SELECT company_name FROM detail');
  $stmtName->execute();
  $company_name_findings = $stmtName->fetchAll(PDO::FETCH_ASSOC);
  foreach ($company_name_findings as $name)
  {
    $one = $name['company_name'];
    $two = ucfirst($company_name);
    if (strcasecmp($one, $two) == 0)
    {
        $match = true;
    }
  }
}

if ($match) 
{
  $stmtId = $db->prepare('SELECT detail_id FROM detail WHERE company_name=:company_name');
  $stmtId->bindValue(':company_name', ucfirst($company_name), PDO::PARAM_STR);
  $stmtId->execute();
  $detail_id = $stmtId->fetch(PDO::FETCH_ASSOC);

  $stmt = $db->prepare('INSERT INTO expense(expense_id, detail_id, transaction_amount, purchase_date) 
  	VALUES (:expense_id, :detail_id, :transaction_amount, :purchase_date)');
  $stmt->bindValue(':expense_id', $expense_id);
  $stmt->bindValue(':detail_id', (int)$detail_id['detail_id']);
  $stmt->bindValue(':transaction_amount', $transaction_amount);
  $stmt->bindValue(':purchase_date', $purchase_date);
  $stmt->execute();

  $new_page = "change.php";
  header("Location: $new_page");
  die();
}
else
{
  header("Location: error.php");
  die();
}

?>