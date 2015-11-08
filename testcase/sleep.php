<?php
$e=time()+2;
$narray=array();
$process = proc_open('sleep 500', $narray, $narray);
do {
$l=$e-time();
print $l . '\n';
print_r (proc_get_status($process));
sleep(1);
}while($l>0);

print_r (proc_get_status($process));

//proc_terminate($process, 9);
proc_terminate($process);
print_r (proc_get_status($process));
print 'termin';
sleep(10);
system('kill -9 ' . proc_get_status($process)['pid']);
print_r (proc_get_status($process));
print 'kill;' . proc_get_status($process)['pid'];
sleep(10);
proc_close($process);
print 'closed';
sleep (1);

?>
