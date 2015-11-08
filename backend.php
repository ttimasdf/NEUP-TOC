<?php
error_reporting(E_ERROR);
require_once('function.php');
require_once('queue.php');
require_once('actionclass.php');

$actionList = new actionQueue();
$socketQueue = new actionQueue();
//$actionList->initQueue();
$socketQueue->initQueue();
$mainSocket = SocketOpen();

while(true)
{
    if($socketQueue->len > 5)                           //Maximum count
        continue;                                       //Do not accept create new socket
    //if($resSocket = socket_accept($mainSocket) && $resSocket != FALSE)
	$resSocket = socket_accept($mainSocket);
	if($resSocket != false)
    {
        $socketQueue->push($resSocket);                 //inqueue newly accepted connection
    }
    if($socketQueue->isempty() == false)
    {
        $currentSocket = $socketQueue->pop();
        $rawMsg = socket_read($currentSocket, 1000);    //fetch message from scheduler
        if(Auth($rawMsg) == true)                       //validation
        {
            $actionObj = ParseMsg($rawMsg);             //objectify raw message
            $actionObj->Compile();                      //Compile
            $simpleResultObj = $actionObj->Run();       //Run

            if($simpleResultObj->resultno == 0)         //Send Msg back to client
            {
                socket_write($currentSocket, "OK");
                socket_write($currentSocket, $simpleResultObj->resultStr, strlen($simpleResultObj->resultStr));
            }
            else
            {
                socket_write($currentSocket, "ERR");
                socket_write($currentSocket, $simpleResultObj->resultStr, strlen($simpleResultObj->resultStr));
            }
        }
        else
        {
            socket_write($currentSocket, "FATAL");
        }
    }
}
