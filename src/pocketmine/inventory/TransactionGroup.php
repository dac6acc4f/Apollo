<?php
namespace pocketmine\inventory;
interface TransactionGroup{
	function getCreationTime();
	function getTransactions();
	function getInventories();
	function addTransaction(Transaction $transaction);
	function canExecute();
	function execute();
	function hasExecuted();
}